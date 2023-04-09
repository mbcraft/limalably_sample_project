<?php



class m2023_01_12_create_attachments_in_element_table implements LIMigration {
	

	function execute() {

		$db = db();

		create_table('attachments_in_element')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('element_type')->t_text64()->not_null())
		->column(col_def('element_id')->t_external_id()->not_null())
		->column(col_def('attachment_id')->t_external_id()->not_null())
		->column(col_def('order_val')->t_u_int())
		->go($db);
		
	}

	function revert() {

		$db = db();

		drop_table('attachment_to_element')->if_exists()->go($db);

	}


}