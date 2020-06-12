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
class KxpayRedirectModuleFrontController extends ModuleFrontController
{
    /**
     * Do whatever you have to before redirecting the customer on the website of your payment processor.
     */
    public function initContent()
    {
        if (version_compare(_PS_VERSION_, '1.7') < 0) {
            $this->display_column_left  = false;
            $this->display_column_right = false;
        }
        
        parent::initContent();

        $action = Tools::getValue('action');
        if ($action !== 'error') {
            $this->context->smarty->assign(array(
                'cart_id' => Context::getContext()->cart->id,
                'secure_key' => Context::getContext()->customer->secure_key,
            ));
        }

        $this->context->smarty->assign('action', $action);

        if (version_compare(_PS_VERSION_, '1.7') < 0) {
            return $this->setTemplate('redirect-16.tpl');
        } else {
            return $this->setTemplate('module:kxpay/views/templates/front/redirect-17.tpl');
        }
    }
}
