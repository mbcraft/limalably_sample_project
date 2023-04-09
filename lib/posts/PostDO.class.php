<?php



class PostDO extends LAbstractDataObject {
	
	const MY_TABLE = "posts";

	const VIRTUAL_COLUMNS_LIST = ['titolo_short','testo_short','immagini_count','immagini_list','category','unreaded_comments_count'];

	const MY_ORDER_COLUMN = 'order_val';

	const MY_ORDER_GROUP_COLUMNS = ['categoria_id','gruppo_categoria_id'];

	use LStandardOperationsFieldsTrait;

	public $id;
	public $proprietario_id;
	public $titolo;
	public $testo;
	public $stato_pubblicazione;
	public $link_video;
	public $categoria_id;
	public $gruppo_categoria_id;
	public $order_val;
	public $likes;

	public function loadVirtualColumns() {

		$this->titolo_short = substr($this->titolo,0,50);
		$this->testo_short = substr($this->testo,0,100)." ...";

		$do = new AttachmentInElementDO();

		$db = db();

		$this->immagini_count = $do->findAll(_eq('element_type','Post'),_eq('element_id',$this->id))->count()->go($db);

		$this->immagini_list = $do->findAll(_eq('element_type','Post'),_eq('element_id',$this->id))->orderBy(asc('order_val'))->go($db);
		$this->immagini_list->loadVirtualColumns();

		$this->category = new CategoriaDO($this->categoria_id);

		$this->category->loadVirtualColumns();

		$cdo = new CommentoDO();

		$this->unreaded_comments_count = $cdo->findAll(_eq('post_id',$this->id),_eq('letto',0))->count()->go($db);

	}
}