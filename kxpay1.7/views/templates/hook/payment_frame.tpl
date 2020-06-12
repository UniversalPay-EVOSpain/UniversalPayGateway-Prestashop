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
 
 <div class="js-kxpay js-payment-binary">
    <iframe id="kineoxFrame" name="kineoxWindow" scrolling="no"></iframe>
    <form action="{$urlenv}" name="kineoxForm" target="kineoxWindow" method="post">
        <input type="hidden" name="TOKEN" value="{$token}" />
    </form>
    <script type="text/javascript">
        var kineoxInitialized = false;
        function showKineoxPayment() {
            if (kineoxInitialized)
                return false;
            kineoxInitialized = true;
            document.forms['kineoxForm'].submit();
            document.getElementById("kineoxFrame").style.display = 'block';
            return false;
        }
        document.addEventListener("DOMContentLoaded", function(){
            $('body').on('change', '#conditions-to-approve input[type=checkbox], input.ps-shown-by-js[data-module-name=kxpay]', function(){
                if ($('#conditions-to-approve input[type=checkbox]').is(':checked') && $('input.ps-shown-by-js[data-module-name=kxpay]').is(':checked')){
                    window.setTimeout(function() {
                        $('.js-kxpay.js-payment-binary').attr('style','display:block;');
                        $([document.documentElement, document.body]).animate({
                            scrollTop: $("#kineoxFrame").offset().top
                        }, 800);
                    },400);
                    showKineoxPayment();
                }
            });
        });
    </script>
    <style>
        #kineoxFrame{
            border: none;
            min-height: 800px;
            margin: 2em auto;
            min-width: 300px;
            width: 80%;
        }
        .js-kxpay.disabled iframe {
            display: none;
        }
    </style>
</div>