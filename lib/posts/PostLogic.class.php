<?php

class PostLogic {
	
	function list_posts_admin($input,$output) {
		$db = db();

		$filters = LFiltersLogic::init('post');

		$do = new CategoriaDO();

		$category_list = $do->findAll()->go($db);

		if (LFiltersLogic::has_filter('/categoria_id')) {
			$category_id = LFiltersLogic::get_filter_value('/categoria_id');

			if ($category_id) {

				$category_list->select_by_id($category_id);
			
				$filters []= _eq('categoria_id',$category_id);

				$category = new CategoriaDO($category_id);

				$category->loadVirtualColumns();

				$output->set('/category',$category);
			}

		}

		$output->set('/category_list',$category_list);

		if (LFiltersLogic::has_filter('/solo_con_commenti_non_letti')) {
			$solo_commenti_non_letti = true;

			$result = select('post_id','post_comments',_eq('letto',0))->group_by('post_id')->go($db);

			$post_id_list = array_column($result,'post_id');

			$post_id_list [] = "!";

			$filters []= _in('id',$post_id_list);

			$output->set('/solo_con_commenti_non_letti',true);
		}

		$do = new PostDO();

		$query = $do->findAll(... $filters)->orderBy(asc('order_val')); 

		$count_query = clone $query;

		$items_count = $count_query->count()->go($db);

		$pagination = new LPaginator('post','/user/post/list_table',$items_count);

		$output->set('/pagination/pages/post',$pagination->getPaginationItems());

		$output->set('/pagination/page_sizes/post',$pagination->getPageSizesItems());

		$all_posts = $query->paginate($pagination->getPageSize(),$pagination->getCurrentPage())->go($db);

		$all_posts->loadVirtualColumns();

		$output->set('/posts',$all_posts);
	}

	function list_posts($input,$output) {

		$db = db();

		if ($input->has('/categoria_id')) {
			$category_id = $input->get('/categoria_id');

			if ($category_id) {
			
				$filters []= _eq('categoria_id',$category_id);

				$category = new CategoriaDO($category_id);

				$category->loadVirtualColumns();

				$output->set('/category',$category);
			}

		}

		$do = new PostDO();

		$query = $do->findAll(... $filters)->orderBy(asc('order_val')); 

		$all_posts = $query->paginate(25,1)->go($db);

		$all_posts->loadVirtualColumns();

		$output->set('/posts',$all_posts);

	}

	function delete_post($input,$output) {

		$post_id = $input->get('/post_id');

		$p = new PostDO($post_id);
		$p->delete();

		LFlash::success("Post eliminato correttamente dall'elenco");

	}

	function view_post($input,$output) {
		$db = db();

		$post_id = $input->get('/post_id');

		$p = new PostDO($post_id);

		$p->loadVirtualColumns();
		
		$output->set('/post',$p);

		if ($input->has('/new_comment_id')) {
			$output->set('/new_comment_id',$input->get('/new_comment_id'));
		}

		if ($input->has('/email_notification_id'))
		{
			$output->set('/email_notification_id',$input->get('/email_notification_id'));
		}
	}

	function go_to_view_post($input,$output) {
		$post_id = $input->get('/post_id');

		$new_comment_id = $output->get('/new_comment_id');

		$go_to_comment_admin = $output->get('/go_to_comment_admin');

		$email_notification_id_string = "";
		if ($output->has('/email_notification_id')) {
			$email_notification_id = $output->get('/email_notification_id');

			$email_notification_id_string = "&email_notification_id=".$email_notification_id;
		}
		if ($go_to_comment_admin) 
			return new LHttpRedirect('/view_post_unreaded_comments?post_id='.$post_id.$email_notification_id_string);
		else
			return new LHttpRedirect('/view_post?post_id='.$post_id.'&new_comment_id='.$new_comment_id.$email_notification_id_string."#new_comment");		
	}

	function load_post($input,$output) {

		$db = db();

		$post_id = $input->get('/post_id');
		
		if ($post_id) {
			$output->set('/is_new',false);
		} else {
			$output->set('/is_new',true);
		}

		$p = new PostDO($post_id);

		$p->loadVirtualColumns();
		
		$output->set('/post',$p);

		$do = new CategoriaDO();

		$category_list = $do->findAll()->go($db);

		foreach ($category_list as $category) {
			if ($category->id == $p->categoria_id) $category->selected = true;
		}

		$output->set('/category_list',$category_list);		
	}

	function edit_post($input,$output) {

		$post_id = $input->get('/post_id');
		
		$p = PostDO::loadOrNull($post_id);
		
		if ($p==null) $p = new PostDO();

		$categoria_id = $input->get('/categoria_id');
		$titolo = $input->get('/titolo');
		$testo = $input->get('/testo');
 		$link_video = $input->get('/link_video');

 		$this->setupDefaultAttributes($p);

 		$p->categoria_id = $categoria_id;
		$p->titolo = $titolo;
		$p->testo = $testo;
		$p->link_video = $link_video ? $link_video : null;
		
		$is_new = $p->isNew();

		$p->saveOrUpdate();

		if ($is_new) {

			$p->move_to_first();

			LFlash::success("Nuovo post aggiunto correttamente!");
		}
		else
			LFlash::success("Dati post modificati correttamente!");

		return new LHttpRedirect('/user/post/list_table');

	}

	private function setupDefaultAttributes($post) {
		$post->gruppo_categoria_id = 1;
		$post->proprietario_id = 2;
		$post->stato_pubblicazione = 1;
	}

	function prepare_add_image($input,$output) {
		$output->set('/post_id',$input->get('/post_id'));
	}

	function list_images($input,$output) {

		$post_id = $input->get('/post_id');

		$p = new PostDO($post_id);

		$output->set('/post',$p);

		$db = db();

		$do = new AttachmentInElementDO();

		$attachment_in_element_list = $do->findAll(_eq('element_type','Post'),_eq('element_id',$post_id))->orderBy(asc('order_val'))->go($db);

		$attachment_in_element_list->loadVirtualColumns();

		$output->set('/element_list',$attachment_in_element_list);

	}

	public function move_first($input,$output) {

		$post_id = $input->get('/post_id');

		$el = new PostDO($post_id);

		$el->move_to_first();

		LFlash::success("Elemento spostato correttamente all'inizio dell'elenco!");

		return new LHttpRedirect("/user/post/list_table");
	}

	public function move_previous($input,$output) {

		$post_id = $input->get('/post_id');

		$el = new PostDO($post_id);

		$el->move_to_previous();

		LFlash::success("Elemento spostato correttamente in posizione precedente!");

		return new LHttpRedirect("/user/post/list_table");
	}

	public function move_next($input,$output) {
		$post_id = $input->get('/post_id');

		$el = new PostDO($post_id);

		$el->move_to_next();

		LFlash::success("Elemento spostato correttamente in posizione successiva!");

		return new LHttpRedirect("/user/post/list_table");

	}

	public function move_last($input,$output) {

		$post_id = $input->get('/post_id');

		$el = new PostDO($post_id);

		$el->move_to_last();

		LFlash::success("Elemento spostato correttamente alla fine dell'elenco!");
		
		return new LHttpRedirect("/user/post/list_table");

	}



}