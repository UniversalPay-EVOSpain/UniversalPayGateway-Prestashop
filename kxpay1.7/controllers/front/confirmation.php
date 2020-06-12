<?php
/**
* 2007-2020 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!function_exists ("writePaymentGatewayLog")) {
	require_once ('include/functions.php');
}

class KxpayConfirmationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if ((Tools::isSubmit('cart_id') == false) || (Tools::isSubmit('secure_key') == false)) {
            return false;
        }

        $cart_id = Tools::getValue('cart_id');
        $secure_key = Tools::getValue('secure_key');

        $cart = new Cart((int) $cart_id);
        $customer = new Customer((int) $cart->id_customer);

        /**
         * If the order has been validated we try to retrieve it
         */
        $order_id = Order::getOrderByCartId((int) $cart->id);

        if ($order_id && ($secure_key == $customer->secure_key)) {
            /**
             * The order has been placed so we redirect the customer on the confirmation page.
             */
            $module_id = $this->module->id;
            Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart_id . '&id_module=' . $module_id . '&id_order=' . $order_id . '&key=' . $secure_key);
        } else {
            /*
             * An error occured and is shown on a new page.
             */
            $this->errors[] = $this->module->l('An error occured. Please contact the merchant to have more informations', 'confirmation');

            $this->context->smarty->assign('errors', $this->errors);

            if (version_compare(_PS_VERSION_, '1.7') > 0) {
                return $this->setTemplate('error-17.tpl');
            } else {
                return $this->setTemplate('error-16.tpl');
            }
        }
		writePaymentGatewayLog("Order ID: '".$cart_id. "'", Configuration::get('PG_LOG'));
    }
}
