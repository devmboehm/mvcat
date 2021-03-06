<?php
namespace bomi\mvcat\demo\classes\controllers;

use bomi\mvcat\base\Controller;
use bomi\mvcat\demo\classes\repositories\UserRepository;

class UserController extends Controller  {
	
	private $_userRepo;
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \bomi\mvcat\base\Controller::beforeAction()
	 */
	public function beforeAction(array &$params): bool {
		$this->_userRepo = $this->getRepository("user");
		return parent::beforeAction($params);
	}
	
	public function apiAction(array $params) {
		$params["users"] = $this->_userRepo->getAll();
		echo $this->view("users/api.php", $params);
	}
	
	public function indexAction(array $params) {
		$params["users"] = $this->_userRepo->getAll();		
		echo $this->view("users/index.inc", $params, "main");
	}
	
	public function activeAction(array $params) {
		$params["users"] = $this->_userRepo->getActive(filter_var($params["active"], FILTER_VALIDATE_BOOLEAN));
		echo $this->view("users/index.inc", $params, "main");
	}
	
	public function userAction(array $params) {
		$params["user"] = $this->_userRepo->get($params["id"]);
		echo $this->view("users/user.inc", $params, "main");
	}
	
	public function formAction(array $params) {
		echo $this->view("users/form.inc", $params, "main");
	}

	public function createAction(array $params) {
		$form = $this->getRequestContext()->getPostData();
		$this->_userRepo->add($form["fname"], $form["lname"], isset($form["active"]));
		echo $this->indexAction($params);
	}
}
