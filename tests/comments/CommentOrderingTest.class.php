<?php


class CommentOrderingTest extends LTestCase {


	function testSomething() {


		$this->assertTrue(true,"Il test non è andato a buon fine!");

	}

	function testGetFormattedNumber() {


		$this->assertEqual(CommentoDO::getFormattedNumber(3),'0000003',"Il numero non risulta formattato correttamente!");

	}

	function testGetNextFormattedNumber() {

		$this->assertEqual(CommentoDO::getNextFormattedNumber(3),'0000004',"Il numero non risulta formattato correttamente!");
	}

	function testConsecutiveNumberGenerator() {

		$this->assertEqual(CommentoDO::getNextConsecutiveOrderString(3),'0000004',"Il numero non risulta formattato correttamente!");
		$this->assertEqual(CommentoDO::getNextConsecutiveOrderString('3'),'0000004',"Il numero non risulta formattato correttamente!");	
		$this->assertEqual(CommentoDO::getNextConsecutiveOrderString('0000003'),'0000004',"Il numero non risulta formattato correttamente!");
		$this->assertEqual(CommentoDO::getNextConsecutiveOrderString('0000000'),'0000001',"Il numero non risulta formattato correttamente!");
		$this->assertEqual(CommentoDO::getNextConsecutiveOrderString(0),'0000001',"Il numero non risulta formattato correttamente!");
		$this->assertEqual(CommentoDO::getNextConsecutiveOrderString('0000001/0000002'),'0000001/0000003',"Il numero non risulta formattato correttamente!");
	}

	function testNestedNumberGenerator() {
		$this->assertEqual(CommentoDO::getNextNestedOrderString(3),'0000003/0000001',"Il numero non risulta formattato correttamente!");
		$this->assertEqual(CommentoDO::getNextNestedOrderString('3'),'0000003/0000001',"Il numero non risulta formattato correttamente!");	
		$this->assertEqual(CommentoDO::getNextNestedOrderString('0000003'),'0000003/0000001',"Il numero non risulta formattato correttamente!");
		$this->assertEqual(CommentoDO::getNextNestedOrderString('0000001/0000002'),'0000001/0000002/0000001',"Il numero non risulta formattato correttamente!");
	}

}