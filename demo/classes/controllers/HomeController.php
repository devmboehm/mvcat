<?php
namespace bomi\mvcat\demo\classes\controllers;

use bomi\mvcat\base\Controller;

class HomeController extends Controller  {

	public function __construct() {
		parent::__construct();
	}
	
	public function indexAction(array $params) {
		$params["header"] = $this->_i18n->get("main.layout.head", ["demo"]);
		echo $this->view("index.inc", $params, "main");
	}
}

