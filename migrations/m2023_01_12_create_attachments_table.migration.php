<?php


class m2023_01_12_create_attachments_table implements LIMigration {
	

	function execute() {

		$db = db();

		create_table('attachments')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('full_filename')->t_text128()->not_null())
		->column(col_def('file_extension')->t_text16()->not_null())
		->column(col_def('mime_type')->t_text32()->not_null())
		->column(col_def('size_bytes')->t_u_bigint()->not_null())
		->column(col_def('is_image')->t_boolean()->not_null())
		->column(col_def('owner_id')->t_external_id())
		->column(col_def('file_stored')->t_u_int()->not_null())
		->go($db);
	}


	function revert() {

		$db = db();

		drop_table('attachments')->if_exists()->go($db);

	}


}