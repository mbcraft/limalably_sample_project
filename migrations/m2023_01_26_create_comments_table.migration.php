<?php




class m2023_01_26_create_comments_table implements LIMigration {
	
	function execute() {
		$db = db();

		create_table('post_comments')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('nominativo')->t_text128())
		->column(col_def('e_mail')->t_text128())
		->column(col_def('testo')->t_text()->not_null())
		->column(col_def('post_id')->t_external_id()->not_null())
		->column(col_def('ordine_string')->t_text128()->not_null())
		->column(col_def('letto')->t_boolean()->not_null())
		->column(col_def('user_id')->t_external_id())
		->column(col_def('likes')->t_u_int())
		->standard_operations_columns()
		->go($db);
		
	}

	function revert() {
		$db = db();

		drop_table('post_comments')->if_exists()->go($db);
	}


}