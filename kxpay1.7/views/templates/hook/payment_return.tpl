{*

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

 *}

 

{if $status == 'ok'}
	<div class="col-lg-12">

		<p style="text-align: center;"><b style="font-size: 1.2em;">{l s='Tu pedido se ha completado.' mod='kxpay'}</b>
			<br /><br />- {l s='Importe pagado:' mod='kxpay'} <span class="price"><strong>{$total_to_pay|escape:'htmlall'}</strong></span>
			<br /><br />- {l s='Método de pago:' mod='kxpay'} <span class="price"><strong>{$method}</strong></span>

			<br /><br />- N# <span class="price"><strong>{$id_order|escape:'htmlall'}</strong></span>

			<br /><br />{l s='Te hemos enviado un email con los datos del pedido.' mod='kxpay'}

			<br /><br />{l s='Si tienes cualquier duda,' mod='kxpay'} <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='Contacta con nosotros' mod='kxpay'}</a>.

		</p>

	</div>

{else}

	<p class="warning">

		{l s='Existe un problema con tu pedido.' mod='kxpay'} 

		<a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='Contacta con nosotros' mod='kxpay'}</a>

	</p>

{/if}

