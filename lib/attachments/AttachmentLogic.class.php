<?php


class AttachmentLogic {

	public function add_image($input,$output) {

		$element_type = $input->get('/element_type');
		$element_id = $input->get('/element_id');

		$image = $input->get('/immagine');

		if ($image->getErrorCode()==UPLOAD_ERR_OK) {

			$attach = AttachmentDO::persistAttachment($image);

			if (!$attach) {
				$error = "Unable to move image to stored images folder";

				LFlash::error($error);
			}
			else {
				

				$do = new AttachmentInElementDO();

				$db = db();

				$data_obj = new AttachmentInElementDO();

				$data_obj->element_type = $element_type;
				$data_obj->element_id = $element_id;
				$data_obj->attachment_id = $attach->id;

				$data_obj->saveOrUpdate();

				LFlash::success("Salvataggio immagine andato a buon fine!");

				return new LHttpRedirect("/user/post/images/list_table?post_id=".$element_id);
			}
		} else {
			$error = $image->getErrorString();

			LFlash::error($error);

			return new LHttpRedirect("/user/post/images/add_image_form?post_id=".$element_id);
		}

		
	}

	public function preview_image($input,$output) {
		$id = $input->get('/id');

		$attachment = new AttachmentDO($id);
		if ($attachment && $attachment->is_image) {

			$image_file = $attachment->findImage();

			$thumbnail_dir = $attachment->getThumbnailStorageDir();
			$thumbnail_dir->touch();

			
			$width = null;
			if ($input->has('/width')) $width = $input->get('/width');

			if ($width) {
				
				$dir = $thumbnail_dir->newSubdir('w'.$width);
				$dir->touch();

				$result_file = $dir->newFile($image_file->getFilename());
				if (!$result_file->exists()) {
					LImageUtils::resize_by_width($image_file,$result_file,$width);
				}

				return new LHttpFileResponse($result_file->getFullPath(),$attachment->full_filename,true,$attachment->mime_type);
			} else {
				$height = null;
				if ($input->has('/height')) $height = $input->get('/height');
				
				if ($height) {
					
					$dir = $thumbnail_dir->newSubdir('h'.$height);
					$dir->touch();

					$result_file = $dir->newFile($image_file->getFilename());
					if (!$result_file->exists()) {
						LImageUtils::resize_by_height($image_file,$result_file,$height);
					}

					return new LHttpFileResponse($result_file->getFullPath(),$attachment->full_filename,true,$attachment->mime_type);

				} else {
					throw new LHttpError(LHttpError::ERROR_BAD_REQUEST);
				}
			}
		} else {

			$preview_not_available_image = new LFile(FRAMEWORK_DIR_NAME.'/lib/images/preview_not_available.png');

			return new LHttpFileResponse($preview_not_available_image->getFullPath(),$preview_not_available_image->getFilename(),true,'image/png');
		}
	}

	public function delete_image($input,$output) {
		$post_id = $input->get('/post_id');
		$attach_el_id = $input->get('/attach_el_id');
		
		$db = db();

		$el = new AttachmentInElementDO($attach_el_id);

		$el->delete();

		LFlash::success("Eliminazione immagine andato a buon fine!");
	
		return new LHttpRedirect("/user/post/images/list_table?post_id=".$post_id);
	}

	public function move_first($input,$output) {

		$post_id = $input->get('/post_id');
		$attach_el_id = $input->get('/attach_el_id');

		$el = new AttachmentInElementDO($attach_el_id);

		$el->move_to_first();

		LFlash::success("Elemento spostato correttamente all'inizio dell'elenco!");

		return new LHttpRedirect("/user/post/images/list_table?post_id=".$post_id);
	}

	public function move_previous($input,$output) {

		$post_id = $input->get('/post_id');
		$attach_el_id = $input->get('/attach_el_id');

		$el = new AttachmentInElementDO($attach_el_id);

		$el->move_to_previous();

		LFlash::success("Elemento spostato correttamente in posizione precedente!");

		return new LHttpRedirect("/user/post/images/list_table?post_id=".$post_id);
	}

	public function move_next($input,$output) {
				$post_id = $input->get('/post_id');
		$attach_el_id = $input->get('/attach_el_id');

		$el = new AttachmentInElementDO($attach_el_id);

		$el->move_to_next();

		LFlash::success("Elemento spostato correttamente in posizione successiva!");

		return new LHttpRedirect("/user/post/images/list_table?post_id=".$post_id);

	}

	public function move_last($input,$output) {

		$post_id = $input->get('/post_id');
		$attach_el_id = $input->get('/attach_el_id');

		$el = new AttachmentInElementDO($attach_el_id);

		$el->move_to_last();

		LFlash::success("Elemento spostato correttamente alla fine dell'elenco!");
		
		return new LHttpRedirect("/user/post/images/list_table?post_id=".$post_id);

	}

	public function download_attachment($input,$output) {

		$id = $input->get('/id');

		$force_download = false;
		if ($input->has('/force_download') && $input->get('/force_download')==1) $force_download = true;

		if ($id) {
			$attachment = new AttachmentDO($id);

			$attachment->startDownload($force_download);
		} else {
			throw new LHttpError(LHttpError::ERROR_BAD_REQUEST);
		}

	}
	
}