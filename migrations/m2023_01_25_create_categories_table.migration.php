<?php



class m2023_01_25_create_categories_table implements LIMigration {
	

	public function execute() {
		$db = db();

		create_table('categories')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('category_name')->t_text128()->not_null())
		->go($db);

		insert('categories',['category_name'],[['Cinema'],['Musica'],['Teatro'],['Letteratura']])->go($db);
	}


	public function revert() {
		$db = db();

		drop_table('categories')->if_exists()->go($db);
	}




}
