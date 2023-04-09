<?php




class m2023_02_04_create_email_table implements LIMigration {


	function execute() {

		$db = db();

		create_table('email_to_send')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('sender')->t_text128()->not_null())
		->column(col_def('rcpt_to')->t_text())
		->column(col_def('rcpt_bcc')->t_text())
		->column(col_def('text_subject')->t_text128()->not_null())
		->column(col_def('html')->t_text()->not_null())
		->column(col_def('sended')->t_u_tinyint()->default_value(0))
		->column(col_def('sended_at')->t_datetime())
		->go($db);

	}


	function revert() {

		$db = db();

		drop_table('email_to_send')->if_exists()
		->go($db);

	}

}