<?php



class AttachmentDO extends LAbstractDataObject {
	
	const IMAGES_ORIGINAL_DIR = "data/images/original/";
	const IMAGES_THUMBNAIL_DIR = "data/images/thumbnail/";
	const DOCUMENTS_ORIGINAL_DIR = "data/documents/";

	const ALLOWED_IMAGES_EXTENSIONS = ['.gif','.jpg','.jpeg','.jfif','.png'];

	const MY_TABLE = "attachments";

	const VIRTUAL_COLUMNS_LIST = ['my_file','my_original_image_url','my_preview_image_url'];

	public $id;
	public $full_filename;
	public $file_extension;
	public $mime_type;
	public $size_bytes;
	public $is_image;
	public $owner_id;
	public $file_stored;

	public static function persistAttachment($uploaded_file) {

		$owner_id = 0;

		if (LSession::has('/user/id')) $owner_id = LSession::get('/user/id');

		$data_obj = new AttachmentDO();

		$data_obj->full_filename = $uploaded_file->getName();
		$data_obj->size_bytes = $uploaded_file->getSize();
		$data_obj->mime_type = $uploaded_file->getMimeType();
		$data_obj->file_extension = strtolower($uploaded_file->getLastExtension());
		$data_obj->is_image = in_array($data_obj->file_extension,self::ALLOWED_IMAGES_EXTENSIONS);
		$data_obj->owner_id = $owner_id;
		$data_obj->file_stored = 0;
		$data_obj->saveOrUpdate();

		$storage_dir = $data_obj->getStorageDirectory();
		$storage_dir->touch();

		$result = $uploaded_file->moveFileTo($storage_dir,$data_obj->id.$data_obj->file_extension);

		if ($result) {
			$data_obj->file_stored = 1;
			$data_obj->saveOrUpdate();

			return $data_obj;
		} else {
			$data_obj->delete();
			return null;
		}

	}

	private function getStorageDirectory() {
		if ($this->is_image) return new LDir(self::IMAGES_ORIGINAL_DIR);
		else return new LDir(self::DOCUMENTS_ORIGINAL_DIR);
	}

	public function getThumbnailStorageDir() {
		return new LDir(self::IMAGES_THUMBNAIL_DIR);
	}

	public function findImage() {

		$image_dir = new LDir(self::IMAGES_ORIGINAL_DIR);
		$results = $image_dir->findFilesStartingWith($this->id.".");

		if (count($results)==1) return $results[0];
		else return null;
	}

	public function findDocument() {
		$document_dir = new LDir(self::DOCUMENTS_ORIGINAL_DIR);
		$results = $document_dir->findFilesStartingWith($this->id.'.');
	
		if (count($results)==1) return $results[0];
		else return null;
	}

	public function findGenericAttachment() {
		
		if ($this->is_image) {
			$image_file = $this->findImage();
			if ($image_file) $this->my_file = $image_file;
		} else {
			$doc_file = $this->findDocument();
			if ($doc_file) $this->my_file = $doc_file;
		}
	}

	public function loadVirtualColumns() {
		$this->findGenericAttachment();

		$this->my_original_image_url = "/attachments/download?id=".$this->id;

		$this->my_preview_image_url = "/attachments/preview?id=".$this->id."&width=200";
	}

	public function startDownload(bool $force_download=false) {

		$this->findGenericAttachment();

		if ($this->my_file!=null) {
			$response = new LHttpFileResponse($this->my_file->getFullPath(),$this->full_filename,!$force_download,$this->mime_type);
			$response->execute();
		} else {
			throw new LHttpError(LHttpError::ERROR_INTERNAL_SERVER_ERROR);
		}

	}
}