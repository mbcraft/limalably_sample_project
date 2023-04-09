<?php


class AttachmentInElementDO extends LAbstractDataObject {
	
	const MY_TABLE = "attachments_in_element";

	const VIRTUAL_COLUMNS_LIST = ['attachment'];

	const MY_ORDER_COLUMN = 'order_val';

	const MY_ORDER_GROUP_COLUMNS = ['element_type','element_id'];

	public $id;
	public $element_type;
	public $element_id;
	public $attachment_id;
	public $order_val;

	function loadVirtualColumns() {
		$do = new AttachmentDO($this->attachment_id);
		$do->loadVirtualColumns();
		
		$this->attachment = $do;
	}


}