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


class KxpayValidationModuleFrontController extends ModuleFrontController  {

	public function postProcess() {
	
		try{
			$idLog = generateKineoxIdLog();
			$active_log = Configuration::get('PG_LOG');
            writePaymentGatewayLog($idLog.' Order validation started.', $active_log);
            
            $json = file_get_contents('php://input');
            $array = json_decode($json,false);
            
	        if (!empty($array)){
                if ( $array->PAYMENT_CODE == '000' or $array->PAYMENT_CODE == '010' ) {
                    
                    $received_signature	= $array->SIGNATURE;
                    $data = array (
                        "MERCHANT_IDENTIFIER" => $array->MERCHANT_IDENTIFIER,
                        "MERCHANT_OPERATION" => $array->MERCHANT_OPERATION,
                        "PAYMENT_AMOUNT" => $array->PAYMENT_AMOUNT,
                        "PAYMENT_OPERATION" => $array->PAYMENT_OPERATION,
                        "PAYMENT_CHANNEL" => $array->PAYMENT_CHANNEL,
                        "PAYMENT_DATE" => $array->PAYMENT_DATE,
                        "PAYMENT_CODE" => $array->PAYMENT_CODE
                    );
                        
                    $key = Configuration::get('PG_KEY');
                    $merchant_identifier = Configuration::get('PG_MERCHANT_IDENTIFIER');
                    $signature = hash ( "sha256", $data["MERCHANT_IDENTIFIER"].$data["PAYMENT_AMOUNT"].$data["MERCHANT_OPERATION"].$data["PAYMENT_OPERATION"].$data["PAYMENT_CHANNEL"].$data["PAYMENT_DATE"].$data["PAYMENT_CODE"].$key, FALSE );
                    
                    if ($signature == $received_signature) {   
                        writePaymentGatewayLog($idLog.' Merchant Operation: '.$data['MERCHANT_OPERATION'], $active_log);
                        $order_id = (int)substr($data['MERCHANT_OPERATION'], 0, -4);
						$cart = new Cart($order_id);
						$kxpay = new Kxpay();
                        $valid_cart = true;
                        
						if ($cart->id_customer == 0) {
                            writePaymentGatewayLog($idLog.' Error: Empty customer.', $active_log);
							$valid_cart = false;
						}
						if ($cart->id_address_delivery == 0) {
                            writePaymentGatewayLog($idLog.' Error: Empty address.', $active_log);
							$valid_cart = false;
						}
						if ($cart->id_address_invoice == 0){
                            writePaymentGatewayLog($idLog.' Error: Empty billing address.', $active_log);
							$valid_cart = false;
						}
						if (!$kxpay->active) {
                            writePaymentGatewayLog($idLog.' Error: Module is not active.', $active_log);
							$valid_cart = false;
                        }
                        if ($merchant_identifier != $data['MERCHANT_IDENTIFIER']){
                            writePaymentGatewayLog($idLog.' Error: Merchant identifier is not equal.', $active_log);
							$valid_cart = false;
                        }
						if (!$valid_cart){
                            die('KO');
                        }
                        
						$customer = new Customer((int)$cart->id_customer);
						
						Context::getContext()->customer = $customer;		
						
						if (!Validate::isLoadedObject($customer)) {
                            writePaymentGatewayLog($idLog.' Error: Customer does not exist.', $active_log);
                            die('OK');
                        }
                        
                        $mailvars['transaction_id'] = $data['PAYMENT_CHANNEL'].' - '.$data['MERCHANT_OPERATION'];
                        $kxpay->validateOrder($cart->id, Configuration::get("PG_ORDER_STATUS"), $data['PAYMENT_AMOUNT']/100, $kxpay->displayName, null, $mailvars, (int)$cart->id_currency, false, $customer->secure_key);
						
                        writePaymentGatewayLog($idLog.' Order finished and validated.', $active_log);
                        die('OK');
                    } else {
                        die('KO');
                    }
                }
                else{
                    writePaymentGatewayLog($idLog.' ERROR: Payment Code '.$array->PAYMENT_CODE, $active_log);
                    die('OK');
                }
            } else {
                writePaymentGatewayLog($idLog.' No data received.', $active_log);
                die('KO');
            }
		} catch (Exception $e){
            writePaymentGatewayLog('Exception: '.$e->getMessage(), $active_log);
            die('KO');
		}
	}
}