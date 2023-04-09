<?php

class m2023_01_11_create_posts_table implements LIMigration {

	function execute() {

		$db = db();

		create_table('posts')->if_not_exists()
			->column(col_def('id')->t_id())
			->column(col_def('proprietario_id')->t_external_id()->not_null())
			->column(col_def('titolo')->t_text128())
			->column(col_def('testo')->t_text())
			->column(col_def('stato_pubblicazione')->t_u_int()->not_null())
			->column(col_def('link_video')->t_text512())
			->column(col_def('categoria_id')->t_external_id())
			->column(col_def('gruppo_categoria_id')->t_external_id())
			->column(col_def('order_val')->t_u_int())
			->column(col_def('likes')->t_u_int())
			->standard_operations_columns()->go($db);
	}

	function revert() {
		drop_table('posts')->if_exists()->go($db);
	}
	
}