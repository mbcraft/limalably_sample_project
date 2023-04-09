<?php



class PaginatorTest {
	

	function paginator_big($input,$output) {

		$paginator = new LPaginator('something_big','/test/paginator_big/item_list',4300);

		$items = $paginator->getPaginationItems();

		$sizes = $paginator->getPageSizesItems();

		$output->set('/paginator_items',$items);

		$output->set('/page_sizer_items',$sizes);

	}



	function paginator_small($input,$output) {

		$paginator = new LPaginator('something_small','/test/paginator_small/item_list',160);

		$items = $paginator->getPaginationItems();

		$sizes = $paginator->getPageSizesItems();

		$output->set('/paginator_items',$items);

		$output->set('/page_sizer_items',$sizes);

	}

	function paginator_one($input,$output) {

		$paginator = new LPaginator('something_one','/test/paginator_small/item_list',23);

		$items = $paginator->getPaginationItems();

		$sizes = $paginator->getPageSizesItems();

		$output->set('/paginator_items',$items);

		$output->set('/page_sizer_items',$sizes);

	}

}