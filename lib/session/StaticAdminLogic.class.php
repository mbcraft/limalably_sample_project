<?php


class StaticAdminLogic {
	
	static function isAdminComment($comment) {
		if (LSession::has('/user/role') && LSession::get('/user/role') == 'admin') return true;
		else return false;
	}

	static function getAdminUserId() {
		return LConfigReader::simple("/misc/admin/id");
	}

	static function getAdminFullName() {
		return LConfigReader::simple("/misc/admin/full_name");
	}

	static function getAdminUsername() {
		return LConfigReader::simple("/misc/admin/username");
	}

	static function getAdminPassword() {
		return LConfigReader::simple("/misc/admin/password");
	}

	static function getAdminEmail() {
		return LConfigReader::simple("/misc/admin/e_mail");
	}

	static function setupAdminCommentData($comment) {
		$comment->nominativo = self::getAdminFullName();
		$comment->e_mail = self::getAdminEmail();
	}

	function login($input,$output) {

		$username = $input->get('/username');
		$password = $input->get('/password');

		if ($username==self::getAdminUsername() && $password==self::getAdminPassword()) {
			
			LSession::set('/user/id',self::getAdminUserId());
			LSession::set('/user/role','admin');

			LFlash::success("Login effettuato con successo!");

			return new LHttpRedirect('/user/post/list_table');
		} else {

			LFlash::error("Combinazione username/password errata!!");

			return new LHttpRedirect("/session/static/login_form");
		}

	}

	function logout($input,$output) {

		LSession::clear();

		LFlash::success("Logout effettuato con successo!");

		return new LHttpRedirect("/session/static/login_form");


	}

}