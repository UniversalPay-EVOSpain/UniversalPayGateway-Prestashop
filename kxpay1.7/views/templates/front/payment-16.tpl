{capture name=path}{l s='Pay with KXPay' mod='kxpay'}{/capture}
<div class="row" style="margin-left: 10%;">
    <a class="btn btn-md btn-info" href="{$link->getPageLink('order')}" style="margin-left: 10%;">
        <i class="fa-pts fa-pts-exchange"></i>
        {l s='Select another payment method' mod='kxpay'}
    </a>
    <script type="text/javascript">
        window.onload = function (){
            document.forms['pagoForm'].submit();
            document.getElementById('framePago').style.display = 'block';
            return false;
        };
    </script>

    <iframe id="framePago"
            name="ventana"
            scrolling="no"
            style="height: 800px; margin-left: 10%; border: none; text-align: center; padding: 0; display: none">
    </iframe>

    <form action="{$src}" name="pagoForm" target="ventana" method="post">
        <input type="hidden" name="TOKEN" value="{$token|escape:'htmlall':'UTF-8'}"/>
    </form>
</div>