<?php
ini_set('display_errors','On');
error_reporting(E_ALL);

/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
/**
 * @since 1.5.0
 */
 if (!function_exists ("writePaymentGatewayLog")) {
	require_once ('include/functions.php');
}

class kxpaypaymentModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        if (!$this->context->customer->isLogged() && !$this->context->customer->isGuest()) {
            Tools::redirect('index');
        }

        if (version_compare(_PS_VERSION_, '1.7') < 0) {
            $this->display_column_left  = false;
            $this->display_column_right = false;
        }

        parent::initContent();
		
		
		$customer = new Customer ($this->context->cookie->id_customer);

		$currency = new Currency ($this->context->cart->id_currency);
		$currency_decimals = is_array($currency) ? (int) $currency['decimals'] : (int) $currency->decimals;
		$cart_details = $this->context->cart->getSummaryDetails(null, true);
		$decimals = $currency_decimals * _PS_PRICE_DISPLAY_PRECISION_;

		$shipping = $cart_details['total_shipping_tax_exc'];
		$subtotal = $cart_details['total_price_without_tax'] - $cart_details['total_shipping_tax_exc'];
		$tax = $cart_details['total_tax'];
		$total_price = Tools::ps_round($shipping + $subtotal + $tax, $decimals);
		$address = new Address((int)$this->context->cart->id_address_invoice);
		
		$orderId = $this->context->cart->id;

		$data = array();
		$params = array();

		
		$data['AMOUNT'] = (int)number_format($total_price, 2, '', '');

		$protocol = (empty ($_SERVER ['HTTPS']))? 'http://': 'https://';
		$data['URL_RESPONSE'] = $protocol.$_SERVER['HTTP_HOST'].__PS_BASE_URI__ .'index.php?fc=module&module=kxpay&controller=validation';
		$data['URL_OK'] = $this->context->link->getModuleLink('kxpay', 'confirmation',
            ['cart_id' => $this->context->cart->id, 'secure_key' => $this->context->cart->secure_key], true);
		$data['URL_KO'] = $this->context->link->getModuleLink('kxpay', 'redirect', ['action' => 'error'], true);

		$params['CURRENCY'] = (int)$currency->iso_code_num;
		$params['LOCALE'] = Tools::substr ($_SERVER ['HTTP_ACCEPT_LANGUAGE'], 0, 2);

		if (Configuration::get('PG_STYLE_BACK_BOTON') <> null)
			$params['STYLE_BACK_BOTON'] = str_replace('#','', Configuration::get('PG_STYLE_BACK_BOTON'));
		if (Configuration::get('PG_STYLE_COLOR_BOTON') <> null)
			$params['STYLE_COLOR_BOTON'] = str_replace('#','', Configuration::get('PG_STYLE_COLOR_BOTON'));
		if (Configuration::get('style_back_frame') <> null)
			$params['PG_STYLE_BACK_FRAME'] = str_replace('#','', Configuration::get('PG_STYLE_BACK_FRAME'));
		if (Configuration::get('PG_STYLE_COLOR_LABEL') <> null)
			$params['style_color_label'] = str_replace('#','', Configuration::get('PG_STYLE_COLOR_LABEL'));
		$params['TARGET'] = '_parent';
		
		if (Configuration::get('PG_REQUIRE_CARDHOLDER')){
			$params['REQUIRE_CARDHOLDER'] = 'true';
		}
		else
			$params['REQUIRE_CARDHOLDER'] = 'false';
		$params['AMOUNT_MAX'] = $data['AMOUNT'];
		$params['AMOUNT_MIN'] = $data['AMOUNT'];

		if (Configuration::get('PG_P1C') <> null && $address->dni){
			$params['P1C'] = 'true';
		}
		else
			$params['P1C'] = 'false';
		$params['P1C_TEXT'] = Configuration::get('PG_P1C_TEXT');
		$params['P1C_LINK'] = Configuration::get('PG_P1C_LINK');

		$params['IDENTIFIER'] = $customer->id;
		$params['PERSONAL_IDENTITY_NUMBER'] = $address->dni;

		$telefono = $address->phone_mobile ? $address->phone_mobile : $address->phone;
		
		$params['MAIL_BC'] = (string) $this->context->cookie->email;
		$params['TELEFONO_BC'] = (string) $telefono;
		
		$data['MERCHANT_IDENTIFIER'] = Configuration::get('PG_MERCHANT_IDENTIFIER');

		$data['OPERATION'] = str_pad ($orderId.str_pad(time()%1000, 3, '0', STR_PAD_LEFT), 11, "0", STR_PAD_LEFT).'T';
		writePaymentGatewayLog("Order ID: '".$orderId."'. Order Number Gateway: '" . $data['OPERATION'] . "'", Configuration::get('PG_LOG'));
		
		$metodo = "";
		if (Configuration::get('PG_TARJETA') == '1')
			$metodo = "CARD";
		if (Configuration::get('PG_SOFORT') == '1')
		{
			if ($metodo == "")
				$metodo = "SOFORT";
			else
				$metodo .= ";SOFORT";
		}
		if (Configuration::get('PG_BIOCRYPTOLOGY') == '1')
		{
			if ($metodo == "")
				$metodo = "BIOCRYPTOLOGY";
			else
				$metodo .= ";BIOCRYPTOLOGY";
		}
		if (Configuration::get('PG_BIZUM') == '1')
		{
			if ($metodo == "")
				$metodo = "BIZUM";
			else
				$metodo .= ";BIZUM";
		}		
		if (Configuration::get('PG_GPAY') == '1')
		{
			if ($metodo == "")
				$metodo = "GOOGLE";
			else
				$metodo .= ";GOOGLE";
		}				
		if (Configuration::get('PG_PAYPAL') == '1')
		{
			if ($metodo == "")
				$metodo = "PAYPAL";
			else
				$metodo .= ";PAYPAL";
		}
		if (Configuration::get('PG_AMAZONPAY') == '1')
		{
			if ($metodo == "")
				$metodo = "AMAZON";
			else
				$metodo .= ";AMAZON";
		}
		if (Configuration::get('PG_TRUSTLY') == '1')
		{
			if ($metodo == "")
				$metodo = "TRUSTLY";
			else
				$metodo .= ";TRUSTLY";
		}
		if (Configuration::get('PG_CORREOS') == '1')
		{
			if ($metodo == "")
				$metodo = "CORREOS";
			else
				$metodo .= ";CORREOS";
		}
		$params['PAYMENT_CHANNELS']=$metodo;
		$data['PARAMS'] = $params;
		$data['SIGNATURE'] = hash("sha256",$data["MERCHANT_IDENTIFIER"].$data["AMOUNT"].$data['OPERATION'].$data["URL_RESPONSE"].$data["URL_OK"].$data["URL_KO"].Configuration::get('PG_KEY'), FALSE );
	
		$data_string = json_encode($data);



		if (Configuration::get('PG_ENV') == 1)
		{
			$ch = curl_init('https://test.imspagofacil.es/client2/token-pro');
			$urlenv = 'https://test.imspagofacil.es/client2/load';
		}
		else
		{
			$ch = curl_init('https://imspagofacil.es/client2/token-pro');
			$urlenv = 'https://imspagofacil.es/client2/load';
		}
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array (
			'Content-Type: application/json',
			'Content-Length: ' . strlen ( $data_string ) 
		));
		$response = curl_exec($ch);
		curl_close($ch);
		$result_string = json_decode($response, TRUE);
		$token_tarjeta = $result_string ["TOKEN"];
		
		
		writePaymentGatewayLog("Order ID: '".$orderId."'. Datos enviados: '" . $data_string . "'", Configuration::get('PG_LOG'));
		writePaymentGatewayLog("Order ID: '".$orderId."'. respuesta token: '" . $response .  "'", Configuration::get('PG_LOG'));
		
		$this->context->smarty->assign ([
				'urlenv' => $urlenv,
				'token_tarjeta' => $token_tarjeta,
				'key' => Configuration::get('PG_KEY')
		]);		
		

        if (version_compare(_PS_VERSION_, '1.7') > 0) {
            $this->setTemplate('module:kxpay/views/templates/front/payment-17.tpl');
        } else {
            $this->setTemplate('payment-16.tpl');
        }
    }
}