<?php
    require_once __DIR__ . '/../controller/vendor/autoload.php';

    $vouchersPath = __DIR__ . '/vouchers.csv';

    $handler = new \App\Controller\Handler();
    if(! $handler->importVouchers($vouchersPath)) {
        print($handler->getLastError());
    }
