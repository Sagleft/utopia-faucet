<?php
	namespace App\Controller;
	//класс для связывания Logic, Database и User
	class Handler {
		public $logic = null;
		public $user  = null;
		public $renderT = null;
		public $last_error = "";
		
		private $db      = null;
		private $enviro  = null;
		
		public function __construct() {
			$this->enviro  = new \App\Model\Environment();
			//$this->db      = new \App\Model\DataBase();
			$this->logic   = new \App\Controller\Logic();
			$this->user    = new \App\Controller\User();
			$this->renderT = new \App\Controller\Render([]);
			
			//$this->logic->setdb($this->db);
			//$this->user->setdb($this->db);
			$this->logic->setUser($this->user);
		}
		
		public function render($data = []) {
			$this->renderT = new \App\Controller\Render($data);
			$this->renderT->twigRender();
		}
	}
