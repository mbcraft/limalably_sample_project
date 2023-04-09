<?php


class CategoryLogic {
	
	function list_categories($input,$output) {

		$db = db();

		$do = new CategoriaDO();

		$categories = $do->findAll()->go($db);

		$categories->loadVirtualColumns();

		$output->set('/categories',$categories);


	}


}