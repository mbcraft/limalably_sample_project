<?php



class LikesLogic {



	function toggle_like($input,$output) {

		$like_type = $input->get('/like_type');
		$like_event = $input->get('/like_event');
		$like_element_id = $input->get('/like_element_id');

		$count_change = $like_event == 'on' ? 1 : -1;

		if ($like_type=='post') {
			$do = new PostDO($like_element_id);
		}
		
		if ($like_type=='post_comment') {
			$do = new CommentoDO($like_element_id);
		}
		
		$do->likes += $count_change;
		$do->saveOrUpdate();

		$output->set('/like_count',$do->likes);
	}


}