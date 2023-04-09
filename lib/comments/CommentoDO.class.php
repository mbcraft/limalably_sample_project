<?php



class CommentoDO extends LAbstractDataObject {
	
	const MY_TABLE = "post_comments";

	public $id;
	public $nominativo;
	public $e_mail;
	public $testo;
	public $post_id;
	public $ordine_string;
	public $letto;
	public $user_id;
	public $likes;

	use LStandardOperationsFieldsTrait;

	static function regenerateParts($parts) {
		
		$result = [];

		foreach ($parts as $p) {
			$result[] = self::getFormattedNumber($p);
		}

		return $result;
	}

	static function getFormattedNumber($num) {

		if (strpos($num,'X')===0) {
			$num = substr($num,1);
		}

		$value = (int) $num;

		return str_pad("".$value,7,'0',STR_PAD_LEFT);

	}

	static function getNextFormattedNumber($num) {

		$value = (int) $num;

		$value++;

		return self::getFormattedNumber($value);

	}

	static function getNextConsecutiveOrderString($order_string) {
		$parts = explode('/',substr($order_string,1));
		$parts = self::regenerateParts($parts);

		$last_part = array_pop($parts);

		$last_part_next = self::getNextFormattedNumber($last_part);

		array_push($parts,$last_part_next);

		$next_order_string = implode('/',$parts);

		return "X".$next_order_string;

	}

	static function getNextNestedOrderString($order_string) {
		$parts = explode('/',substr($order_string,1));
		$parts = self::regenerateParts($parts);

		array_push($parts, self::getNextFormattedNumber(0));

		return "X".implode('/',$parts);		
	}

	static function hasConsecutiveComment($comment) {

		$next_consecutive_order_string = self::getNextConsecutiveOrderString($comment->ordine_string);

		$do = new CommentoDO();

		$db = db();

		$comment = $do->findFirstOrNull(_eq('ordine_string',$next_consecutive_order_string),_eq('post_id',$comment->post_id))->with_soft_deleted()->go($db);

		return $comment!=null;
	}

	function getLevel() {
		$parts = explode('/',$this->ordine_string);

		return count($parts)-1;
	}

	function setOrderStringInReplyTo($order_string,$post_id) {

		if ($order_string==null) throw new \Exception("Order string can't be null!");

		$do = new CommentoDO();

		$db = db();

		$comment = $do->findFirstOrNull(_eq('ordine_string',$order_string),_eq('post_id',$post_id))->with_soft_deleted()->go($db);

		if ($comment) {
			if (self::hasConsecutiveComment($comment)) {
				$next_order_string = self::getNextNestedOrderString($comment->ordine_string);

			} else {
				$next_order_string = self::getNextConsecutiveOrderString($comment->ordine_string);
			}

		} else {
			$next_order_string = "X".self::getNextFormattedNumber(0);

			$comment = $do->findFirstOrNull(_eq('ordine_string',$next_order_string),_eq('post_id',$post_id))->with_soft_deleted()->go($db);
		}

		do {
			$found = $do->findFirstOrNull(_eq('ordine_string',$next_order_string),_eq('post_id',$post_id))->with_soft_deleted()->go($db);
			if ($found) {
				$next_order_string = self::getNextConsecutiveOrderString($next_order_string);
			}
		} while ($found!=null);
		
		$this->ordine_string = $next_order_string;

	}

}