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
 *  @copyright 2010-2020 Nubeser
 *  @license   license.txt
 *}
{if $token_tarjeta<>"" || $token_sofort<>"" || token_biocryptology<>"" || token_bizum<>"" || token_gpay<>"" || token_paypal<>"" || token_amazonpay<>"" || token_trustly<>"" || token_barzahlen<>"" || token_correos <> ""}
{if $smarty.const._PS_VERSION_ >= 1.6}
<a id="botonvolver" class="btn btn-md btn-info" href="{$link->getPageLink('order')}" style="margin-left: 10%;display: none">
        <i class="fa-pts fa-pts-exchange"></i>
        {l s='Volver a seleccionar otro medio de pago' mod='kxpay'}
</a>
<div class="row">
     {if $token_tarjeta<>""}
		<div class="col-xs-12">
		<p class="payment_module">
		<a id="continueKineox1" onclick="showKineoxPayment('{$token_tarjeta}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}" class="bankwire" style="padding: 1em 0; cursor:pointer; background: none;">	
					<img src="{$module_dir|escape:'htmlall'}views/img/tarjeta.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}" style="max-width: 25%; max-height: 48px;" />
					{l s='Pago con tarjeta' mod='kxpay'}
				</a>
		</p>
		</div>
	{/if}
	{if $token_sofort<>""}
		<div class="col-xs-12">
		<p class="payment_module">
			<a id="continueKineox2" onclick="showKineoxPayment('{$token_sofort}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}" class="bankwire" style="padding: 1em 0; cursor:pointer; background: none;">	
				<img src="{$module_dir|escape:'htmlall'}views/img/Klarna.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}" style="max-width: 25%; max-height: 48px;" />
				{l s='Pago con Sofort' mod='kxpay'}
			</a>
		</p>
	</div>
	{/if}
	{if $token_biocryptology<>""}
	<div class="col-xs-12">
		<p class="payment_module">
			<a id="continueKineox3" onclick="showKineoxPayment('{$token_biocryptology}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}" class="bankwire" style="padding: 1em 0; cursor:pointer; background: none;">	
				<img src="{$module_dir|escape:'htmlall'}views/img/Biocryptology.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}" style="max-width: 25%; max-height: 48px;" />
				{l s='Pago Seguro con Biocryptology' mod='kxpay'}
			</a>
		</p>
	</div>
	{/if}
	{if $token_bizum<>""}
	<div class="col-xs-12">
		<p class="payment_module">
			<a id="continueKineox4" onclick="showKineoxPayment('{$token_bizum}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}" class="bankwire" style="padding: 1em 0; cursor:pointer; background: none;">	
				<img src="{$module_dir|escape:'htmlall'}views/img/bizum.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}" style="max-width: 25%; max-height: 48px;" />
				{l s='Pago con Bizum' mod='kxpay'}
			</a>
		</p>
	</div>
	{/if}
	{if $token_gpay<>""}
	<div class="col-xs-12">
		<p class="payment_module">
			<a id="continueKineox5" onclick="showKineoxPayment('{$token_gpay}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}" class="bankwire" style="padding: 1em 0; cursor:pointer; background: none;">	
				<img src="{$module_dir|escape:'htmlall'}views/img/gpay.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}" style="max-width: 25%; max-height: 48px;" />
				{l s='Pago con Google Pay' mod='kxpay'}
			</a>
		</p>
	</div>
	{/if}
	{if $token_paypal<>""}
	<div class="col-xs-12">
		<p class="payment_module">
			<a id="continueKineox6" onclick="showKineoxPayment('{$token_paypal}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}" class="bankwire" style="padding: 1em 0; cursor:pointer; background: none;">	
				<img src="{$module_dir|escape:'htmlall'}views/img/paypal.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}" style="max-width: 25%; max-height: 48px;" />
				{l s='Pago con Paypal' mod='kxpay'}
			</a>
		</p>
	</div>
	{/if}
	{if $token_amazonpay<>""}
	<div class="col-xs-12">
		<p class="payment_module">
			<a id="continueKineox7" onclick="showKineoxPayment('{$token_amazonpay}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}" class="bankwire" style="padding: 1em 0; cursor:pointer; background: none;">	
				<img src="{$module_dir|escape:'htmlall'}views/img/amazonpay.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}" style="max-width: 25%; max-height: 48px;" />
				{l s='Pago con Amazon Pay' mod='kxpay'}
			</a>
		</p>
	</div>
	{/if}
	{if $token_trustly<>""}
	<div class="col-xs-12">
		<p class="payment_module">
			<a id="continueKineox8" onclick="showKineoxPayment('{$token_trustly}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}" class="bankwire" style="padding: 1em 0; cursor:pointer; background: none;">	
				<img src="{$module_dir|escape:'htmlall'}views/img/trustly.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}" style="max-width: 25%; max-height: 48px;" />
				{l s='Pago con Trustly' mod='kxpay'}
			</a>
		</p>
	</div>
	{/if}
	{if $token_barzahlen<>""}
	<div class="col-xs-12">
		<p class="payment_module">
			<a id="continueKineox9" onclick="showKineoxPayment('{$token_barzahlen}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}" class="bankwire" style="padding: 1em 0; cursor:pointer; background: none;">	
				<img src="{$module_dir|escape:'htmlall'}views/img/barzahlen.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}" style="max-width: 25%; max-height: 48px;" />
				{l s='Pago con Barzahlen' mod='kxpay'}
			</a>
		</p>
	</div>
	{/if}
	{if $token_correos<>""}
	<div class="col-xs-12">
		<p class="payment_module">
			<a id="continueKineox10" onclick="showKineoxPayment('{$token_correos}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}" class="bankwire" style="padding: 1em 0; cursor:pointer; background: none;">	
				<img src="{$module_dir|escape:'htmlall'}views/img/correos.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}" style="max-width: 25%; max-height: 48px;" />
				{l s='Pago en Correos' mod='kxpay'}
			</a>
		</p>
	</div>
	{/if}
</div>
{else}
{if $token_tarjeta<>""}
<p class="payment_module">
	<a class="bankwire" href="" onclick="showKineoxPayment('{$token_tarjeta}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}">	
		<img src="{$module_dir|escape:'htmlall'}views/img/tarjeta.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}"  style="max-width: 25%; max-height: 48px;" />
		{l s='Pago con tarjeta' mod='kxpay'}
	</a>
</p>
{/if}
{if $token_sofort<>""}
<p class="payment_module">
	<a class="bankwire" href="" onclick="showKineoxPayment('{$token_sofort}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}">	
		<img src="{$module_dir|escape:'htmlall'}views/img/Klarna.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}"  style="max-width: 25%; max-height: 48px;" />
		{l s='Pago con Sofort' mod='kxpay'}
	</a>
</p>
{/if}
{if $token_biocryptology<>""}
<p class="payment_module">
	<a class="bankwire" href="" onclick="showKineoxPayment('{$token_biocryptology}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}">	
		<img src="{$module_dir|escape:'htmlall'}views/img/Biocryptology.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}"  style="max-width: 25%; max-height: 48px;" />
		{l s='Pago Seguro con Biocryptology' mod='kxpay'}
	</a>
</p>
{/if}
{if $token_bizum<>""}
<p class="payment_module">
	<a class="bankwire" href="" onclick="showKineoxPayment('{$token_bizum}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}">	
		<img src="{$module_dir|escape:'htmlall'}views/img/bizum.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}"  style="max-width: 25%; max-height: 48px;" />
		{l s='Pago con Bizum' mod='kxpay'}
	</a>
</p>
{/if}
{if $token_gpay<>""}
<p class="payment_module">
	<a class="bankwire" href="" onclick="showKineoxPayment('{$token_gpay}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}">	
		<img src="{$module_dir|escape:'htmlall'}views/img/gpay.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}"  style="max-width: 25%; max-height: 48px;" />
		{l s='Pago con Google Pay' mod='kxpay'}
	</a>
</p>
{/if}
{if $token_paypal<>""}
<p class="payment_module">
	<a class="bankwire" href="" onclick="showKineoxPayment('{$token_paypal}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}">	
		<img src="{$module_dir|escape:'htmlall'}views/img/paypal.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}"  style="max-width: 25%; max-height: 48px;" />
		{l s='Pago con Paypal' mod='kxpay'}
	</a>
</p>
{/if}
{if $token_amazonpay<>""}
<p class="payment_module">
	<a class="bankwire" href="" onclick="showKineoxPayment('{$token_amazonpay}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}">	
		<img src="{$module_dir|escape:'htmlall'}views/img/amazonpay.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}"  style="max-width: 25%; max-height: 48px;" />
		{l s='Pago con Amazon Pay' mod='kxpay'}
	</a>
</p>
{/if}
{if $token_trustly<>""}
<p class="payment_module">
	<a class="bankwire" href="" onclick="showKineoxPayment('{$token_trustly}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}">	
		<img src="{$module_dir|escape:'htmlall'}views/img/trustly.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}"  style="max-width: 25%; max-height: 48px;" />
		{l s='Pago con Trustly' mod='kxpay'}
	</a>
</p>
{/if}
{if $token_barzahlen<>""}
<p class="payment_module">
	<a class="bankwire" href="" onclick="showKineoxPayment('{$token_barzahlen}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}">	
		<img src="{$module_dir|escape:'htmlall'}views/img/barzahlen.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}"  style="max-width: 25%; max-height: 48px;" />
		{l s='Pago con Barzahlen' mod='kxpay'}
	</a>
</p>
{/if}
{if $token_correos<>""}
<p class="payment_module">
	<a class="bankwire" href="" onclick="showKineoxPayment('{$token_correos}')" title="{l s='Comenzar proceso de pago' mod='kxpay'}">	
		<img src="{$module_dir|escape:'htmlall'}views/img/correos.png" alt="{l s='Comenzar proceso de pago' mod='kxpay'}"  style="max-width: 25%; max-height: 48px;" />
		{l s='Pago en Correos' mod='kxpay'}
	</a>
</p>
{/if}
{/if}
<iframe id="kineoxFrame" name="kineoxWindow" scrolling="no"></iframe>
<form action="{$urlenv}" name="kineoxForm" target="kineoxWindow" method="post">
	<input type="hidden" name="TOKEN" id="TOKEN" value="" />
</form>
<script type="text/javascript">
	function showKineoxPayment(token) {
	    var token;
	    document.getElementById("TOKEN").value = token;
		document.forms['kineoxForm'].submit();
		document.getElementById("kineoxFrame").style.display = 'block';
		{if $token_tarjeta<>""}
		document.getElementById("continueKineox1").style.display = 'none';
		{/if}
		{if $token_sofort<>""}
		document.getElementById("continueKineox2").style.display = 'none';
		{/if}
		{if $token_biocryptology<>""}
		document.getElementById("continueKineox3").style.display = 'none';
		{/if}
		{if $token_bizum<>""}
		document.getElementById("continueKineox4").style.display = 'none';
		{/if}
		{if $token_gpay<>""}
		document.getElementById("continueKineox5").style.display = 'none';
		{/if}
		{if $token_paypal<>""}
		document.getElementById("continueKineox6").style.display = 'none';
		{/if}
		{if $token_amazonpay<>""}
		document.getElementById("continueKineox7").style.display = 'none';
		{/if}
		{if $token_trustly<>""}
		document.getElementById("continueKineox8").style.display = 'none';
		{/if}
		{if $token_barzahlen<>""}
		document.getElementById("continueKineox9").style.display = 'none';
		{/if}
		{if $token_correos<>""}
		document.getElementById("continueKineox10").style.display = 'none';
		{/if}
		document.getElementById("botonvolver").style.display = 'block';
		return false;
	}
</script>
	<style>
	#kineoxFrame{
		display: none;
		border: none;
		min-height: 800px;
		margin: 2em auto;
		min-width: 300px;
		width: 80%;
	}
</style>
{else}
	<p class="warning">

		{l s='Existe un problema para pagar.' mod='kxpay'} 

		<a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='Contacta con nosotros' mod='kxpay'}</a>

	</p>
{/if}