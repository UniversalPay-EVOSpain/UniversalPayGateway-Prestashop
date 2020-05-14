<?php

/**

 * NOTICE OF LICENSE

 *

 * This file is licenced under the Software License Agreement.

 * With the purchase or the installation of the software in your application

 * you accept the licence agreement.

 *

 * You must not modify, adapt or create derivative works of this source code

 *

 *  @author    Nubeser

 *  @copyright 2010-2019 Nubeser

 *  @license   license.txt

 */



function generateKineoxIdLog() {

    $vars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $stringLength = Tools::strlen($vars);

    $result = '';

    for ($i = 0; $i < 20; $i++) {

        $result .= $vars[rand(0, $stringLength - 1)];

    }

    return $result;

}



function writePaymentGatewayLog($text,$active) {

	if($active==1){

		$logfilename = dirname(__FILE__).'/../logs/payment_log.log';

		file_put_contents($logfilename, date('M d Y G:i:s') . ' -- ' . $text . "\r\n", is_file($logfilename)?FILE_APPEND:0);

	}

}



function checkAlphanumeric($text) {

	return preg_match("/^[a-zA-Z0-9-_]+$/", $text);

}

function checkHex($color) {

	return preg_match("/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/", $color);

}



?>