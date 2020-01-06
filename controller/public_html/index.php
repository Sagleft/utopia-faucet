<?php
	session_start();
	require_once __DIR__ . "/../vendor/autoload.php";
	
	$handler = new \App\Controller\Handler();
	
	$handler->render([
		'tag'   => 'home',
		'title' => 'Claim Cryptons',
		'user'  => $handler->user->data,
		'vouchers' => [
			'available' => $handler->logic->getFreshVouchersCount()
		]
	]);
	