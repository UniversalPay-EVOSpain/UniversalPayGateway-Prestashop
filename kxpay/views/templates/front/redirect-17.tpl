{*
* 2007-2019 PrestaShop
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends "$layout"}
{block name="content"}
    <section id="main">
        <section id="content" class="page-content card card-block">
            {if $action eq 'error'}
                <div class="alert alert-danger">
                    {l s='Ocurrio un error al intentar ejecutar el pago.' mod='kxpay'} <a href="{$link->getPageLink('order', null, null, 'step=3')}">{l s='Volver a la página de pago' mod='kxpay'}</a>
                </div>
            {else}
                <div>
                    <h3>{l s='Se va a producir una redirección' mod='kxpay'}:</h3>
                    <ul class="alert alert-info">
                        <li>{l s='Hola ahora será redirigido a la pasarela de Pago' mod='kxpay'}.</li>
                    </ul>

                    <div class="alert alert-warning">
                        {l s='A You can redirect your customer with an error message' mod='kxpay'}:
                        <a href="{$link->getModuleLink('kxpay', 'redirect', ['action' => 'error'], true)|escape:'htmlall':'UTF-8'}" title="{l s='Look at the error' mod='kxpay'}">
                            <strong>{l s='A Look at the error message' mod='kxpay'}</strong>
                        </a>
                    </div>

                    <div class="alert alert-success">
                        {l s='A You can also redirect your customer to the confirmation page' mod='kxpay'}:
                        <a href="{$link->getModuleLink('kxpay', 'confirmation', ['cart_id' => $cart_id, 'secure_key' => $secure_key], true)|escape:'htmlall':'UTF-8'}" title="{l s='Confirm' mod='kxpay'}">
                            <strong>{l s='Go to the confirmation page' mod='kxpay'}</strong>
                        </a>
                    </div>
                </div>
            {/if}
        </section>
    </section>
{/block}
