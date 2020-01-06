<?php
	require_once __DIR__ . '/../../vendor/autoload.php';
	
	$handler = new App\Controller\Handler();
	$voucher_code = $handler->logic->claimCryptonBonus();
	if($voucher_code == '') {
		$handler->logic->printAPIError($handler->logic->last_error);
	} else {
		$handler->logic->printAPISuccess(['code' => $voucher_code]);
	}
	