<?php



class CategoriaDO extends LAbstractDataObject {
	
	const MY_TABLE = "categories";

	const VIRTUAL_COLUMNS_LIST = ['my_image_url','selected'];

	public $id;
	public $category_name;

	public function loadVirtualColumns() {

		$this->my_image_url = '/assets/images/categories/'.$this->category_name.'.jpg';
		$this->selected = false;
	}

}