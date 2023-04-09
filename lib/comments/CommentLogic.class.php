<?php



class CommentLogic {
	

	function load_comments($input,$output) {

		$post_id = $input->get('/post_id');

		$db = db();

		$do = new CommentoDO();

		$comments = $do->findAll(_eq('post_id',$post_id))->orderBy(asc('ordine_string'))->go($db);

		$output->set('/post_comments',$comments);

		if (empty($cleaned_ordered_comments)) $last_comment_order_string = "X0000000";
		else {
			$last_comment = end($cleaned_ordered_comments);

			$last_comment_order_string = $last_comment->ordine_string;
		}

		$output->set('/last_ordine_string',$last_comment_order_string);

	}

	function save_comment($input,$output) {

		$post_id = $input->get('/post_id');
		$reply_to = $input->get('/reply_to');
		$mark_as_already_readed = $input->get('/mark_as_already_readed');

		$user_id = $input->get('/user_id');
		$nominativo = $input->get('/nominativo');
		$e_mail = $input->get('/e_mail');
		$testo = $input->get('/testo');
		
		$comment = new CommentoDO();

		$comment->post_id = $post_id;
		if ($mark_as_already_readed && StaticAdminLogic::isAdminComment($comment)) {
			$comment->letto = 1;
			StaticAdminLogic::setupAdminCommentData($comment);
			$go_to_comment_admin = true;		
		}
		else {
			$comment->letto = 0;
			$comment->nominativo = $nominativo;
			$comment->e_mail = $e_mail;
			$go_to_comment_admin = false;
		}
		
		$comment->user_id = $user_id;
		$comment->likes = 0;
		$comment->testo = $testo;

		$comment->setOrderStringInReplyTo($reply_to,$post_id);

		$comment->saveOrUpdate();

		if (LConfigReader::simple('/misc/send_email_notifications')) {

			$db = db();

			$do = new CommentoDO();

			$previous_comment = $do->findFirstOrNull(_eq('post_id',$post_id),_eq('ordine_string',$reply_to))->go($db);

			if ($previous_comment && trim($previous_comment->e_mail)!=null && $previous_comment->e_mail!=StaticAdminLogic::getAdminEmail() && $previous_comment->e_mail!=$comment->e_mail) {
				$email = new EmailToSendDO();
				$email->sender = "no_reply@marcobedeschi.it";
				$email->rcpt_to = $previous_comment->e_mail;
				$email->text_subject = "[MarcoBedeschi.it] L'utente ".$comment->nominativo." ha risposto al tuo commento!";
				$email->sended = 0;
				$email->renderTwigTemplateToHtml('email/comment_reply.twig',["commento"=> $comment]);

				$email->saveOrUpdate();

				$output->set('/email_notification_id',$email->id);
			}
		}

		$output->set('/new_comment_id',$comment->id);
		$output->set('/go_to_comment_admin',$go_to_comment_admin);
	}

	function mark_readed($input,$output) {

		$comment_id = $input->get('/comment_id');

		$do = new CommentoDO($comment_id);

		$do->letto = 1;
		$do->saveOrUpdate();
		
	}

	function mark_deleted($input,$output) {

		$comment_id = $input->get('/comment_id');

		$do = new CommentoDO($comment_id);

		$do->letto = 1;
		$do->saveOrUpdate();

		$do->delete();

	}


}