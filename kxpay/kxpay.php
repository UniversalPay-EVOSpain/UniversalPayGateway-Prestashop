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

if (!defined ('_PS_VERSION_')) {
	exit ();
}
if (!function_exists ("writePaymentGatewayLog")) {
	require_once ('include/functions.php');
}
if (!defined ('_CAN_LOAD_FILES_'))
	exit ();
	
	
class Kxpay extends PaymentModule {
	
	private $_html = '';
	private $_postErrors = array ();

	public function __construct() {
		
		$this->name = 'kxpay';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.2';
		$this->author = 'Nubeser';
		
		if(_PS_VERSION_>=1.6){
			$this->is_eu_compatible = 1;
			$this->ps_versions_compliancy = array ('min' => '1.6', 'max' => _PS_VERSION_);
			$this->bootstrap = true;
		}
		
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$config = Configuration::getMultiple(array(
			'PG_MERCHANT_IDENTIFIER',
			'PG_KEY',
			'PG_STYLE_BACK_BOTON',
			'PG_STYLE_COLOR_BOTON',
			'PG_STYLE_BACK_FRAME',
			'PG_STYLE_COLOR_LABEL',
			'PG_REQUIRE_CARDHOLDER',
			'PG_P1C',
			'PG_P1C_TEXT',
			'PG_P1C_LINK',
			'PG_LOCALE',
			'PG_ENV',
			'PG_LOG',
			'PG_ORDER_STATUS',
			'PG_ERROR',
			'PG_TARJETA',
			'PG_SOFORT',
			'PG_BIOCRYPTOLOGY',
			'PG_BIZUM',
			'PG_GPAY',
			'PG_PAYPAL',
			'PG_AMAZONPAY',
			'PG_TRUSTLY',
			'PG_BARZAHLEN',
			'PG_CORREOS'
		));
		
		$this->env = $config ['PG_ENV'];
		switch ($this->env) {
			case 1:
				$this->urltoken = 'https://test.imspagofacil.es/client2/token-pro';
				$this->urlenv = 'https://test.imspagofacil.es/client2/load';
				$this->urlstatus = 'https://test.imspagofacil.es/client2/rservices/checkOperation';
				break;
			case 2: 
				$this->urltoken = 'https://imspagofacil.es/client2/token-pro';
				$this->urlenv = 'https://imspagofacil.es/client2/load';
				$this->urlstatus = 'https://imspagofacil.es/client2/rservices/checkOperation';
				break;
		}

		if (isset ($config ['PG_MERCHANT_IDENTIFIER'])) // merchant_identifier
			$this->merchant_identifier = $config ['PG_MERCHANT_IDENTIFIER'];
		if (isset ($config ['PG_KEY'])) // secure_key
			$this->secure_key = $config ['PG_KEY'];
		if (isset ($config ['PG_STYLE_BACK_BOTON'])) // style_back_boton
			$this->style_back_boton = $config ['PG_STYLE_BACK_BOTON'];
		if (isset ($config ['PG_STYLE_COLOR_BOTON'])) // style_color_boton
			$this->style_color_boton = $config ['PG_STYLE_COLOR_BOTON'];
		if (isset ($config ['PG_STYLE_BACK_FRAME'])) // style_back_frame
			$this->style_back_frame = $config ['PG_STYLE_BACK_FRAME'];
		if (isset ($config ['PG_STYLE_COLOR_LABEL'])) // style_color_label
			$this->style_color_label = $config ['PG_STYLE_COLOR_LABEL'];
		if (isset ($config ['PG_REQUIRE_CARDHOLDER'])) // require_cardholder
			$this->require_cardholder = $config ['PG_REQUIRE_CARDHOLDER'];
		if (isset ($config ['PG_P1C'])) // p1c_text
			$this->p1c = $config ['PG_P1C'];
		if (isset ($config ['PG_P1C_TEXT'])) // p1c_text
			$this->p1c_text = $config ['PG_P1C_TEXT'];
		if (isset ($config ['PG_P1C_LINK'])) // p1c_link
			$this->p1c_link = $config ['PG_P1C_LINK'];
		if (isset ($config ['PG_LOCALE']))	// locale
			$this->locale = $config ['PG_LOCALE'];
		if (isset ($config ['PG_ENV'])) // test/prod
			$this->environment = $config ['PG_ENV'];
		if (isset ($config ['PG_LOG']))	// activate logs
			$this->active_log = $config ['PG_LOG'];
		if (isset ($config ['PG_ORDER_STATUS'])) // order status
			$this->order_status = $config ['PG_ORDER_STATUS'];
		if (isset ($config ['PG_ERROR'])) // continue on error
			$this->error = $config ['PG_ERROR'];
		if (isset ($config ['PG_TARJETA'])) // Pago con tarjeta
			$this->tarjeta = $config ['PG_TARJETA'];
		if (isset ($config ['PG_SOFORT'])) // Pago con sofort
			$this->sofort = $config ['PG_SOFORT'];
		if (isset ($config ['PG_BIOCRYPTOLOGY'])) // Pago con biocryptology
			$this->biocryptology = $config ['PG_BIOCRYPTOLOGY'];			
		if (isset ($config ['PG_BIZUM'])) // Pago con bizum
			$this->bizum = $config ['PG_BIZUM'];			
		if (isset ($config ['PG_GPAY'])) // Pago con google pay
			$this->gpay = $config ['PG_GPAY'];			
		if (isset ($config ['PG_PAYPAL'])) // Pago con Paypal
			$this->paypal = $config ['PG_PAYPAL'];			
		if (isset ($config ['PG_AMAZONPAY'])) // Pago con Amazon Pay
			$this->amazonpay = $config ['PG_AMAZONPAY'];			
		if (isset ($config ['PG_TRUSTLY'])) // Pago con Trustly
			$this->trustly = $config ['PG_TRUSTLY'];			
		if (isset ($config ['PG_CORREOS'])) // Pago con Correos
			$this->correos = $config ['PG_CORREOS'];			

		parent::__construct ();
		
		$this->page = basename (__FILE__, '.php');
		$this->displayName = $this->l('UniversalPay Gateway');
		$this->description = $this->l('A continuación podrás elegir tu forma de pago.');
		
		if (!isset($this->merchant_identifier) || 
		!isset($this->secure_key) || 
		!isset($this->style_back_boton) || 
		!isset($this->style_color_boton) || 
		!isset($this->style_back_frame) || 
		!isset($this->environment) || 
		!isset($this->active_log) || 
		!isset($this->order_status)){
			$this->warning = $this->l('Faltan datos por configurar en el módulo.');
		}
	}
	
	
	public function install() {
		if (!parent::install() || 
		!Configuration::updateValue('PG_MERCHANT_IDENTIFIER', '') || 
		!Configuration::updateValue('PG_KEY','') || 
		!Configuration::updateValue('PG_STYLE_BACK_BOTON', '1E315A') || 
		!Configuration::updateValue('PG_STYLE_COLOR_BOTON', 'ffffff') || 
		!Configuration::updateValue('PG_STYLE_BACK_FRAME', '') || 
		!Configuration::updateValue('PG_STYLE_COLOR_LABEL', '') || 
		!Configuration::updateValue('PG_REQUIRE_CARDHOLDER', 0) || 
		!Configuration::updateValue('PG_P1C', 0) || 
		!Configuration::updateValue('PG_P1C_TEXT', '') || 
		!Configuration::updateValue('PG_P1C_LINK', '') || 
		!Configuration::updateValue('PG_LOCALE', 'ES') || 
		!Configuration::updateValue('PG_ENV', 1) || 
		!Configuration::updateValue('PG_LOG', 0) || 
		!Configuration::updateValue('PG_ORDER_STATUS', 2) || 
		!Configuration::updateValue('PG_ERROR', 0) ||
		!Configuration::updateValue('PG_TARJETA', '1') ||
		!Configuration::updateValue('PG_SOFORT', '0') ||
		!Configuration::updateValue('PG_BIOCRYPTOLOGY', '0') ||
		!Configuration::updateValue('PG_BIZUM', '0') ||
		!Configuration::updateValue('PG_GPAY', '0') ||
		!Configuration::updateValue('PG_PAYPAL', '0') ||
		!Configuration::updateValue('PG_AMAZONPAY', '0') ||
		!Configuration::updateValue('PG_TRUSTLY', '0') ||
		!Configuration::updateValue('PG_BARZAHLEN', '0') ||
		!Configuration::updateValue('PG_CORREOS', '0') || !$this->registerHook('paymentReturn') || 
		(_PS_VERSION_ >= 1.7? !$this->registerHook ('paymentOptions') : !$this->registerHook ('payment'))) {
			return false;
			if ((_PS_VERSION_ > '1.5') && (!$this->registerHook('displayPaymentEU'))) {
				return false;
			}
		}
		
		return true;
	}
	
	
	public function uninstall() {
		if (!Configuration::deleteByName('PG_MERCHANT_IDENTIFIER') || 
		!Configuration::deleteByName('PG_KEY') || 
		!Configuration::deleteByName('PG_STYLE_BACK_BOTON') || 
		!Configuration::deleteByName('PG_STYLE_COLOR_BOTON') || 
		!Configuration::deleteByName('PG_STYLE_BACK_FRAME') || 
		!Configuration::deleteByName('PG_STYLE_COLOR_LABEL') || 
		!Configuration::deleteByName('PG_REQUIRE_CARDHOLDER') || 
		!Configuration::deleteByName('PG_P1C') || 
		!Configuration::deleteByName('PG_P1C_TEXT') || 
		!Configuration::deleteByName('PG_P1C_LINK') || 
		!Configuration::deleteByName('PG_LOCALE') || 
		!Configuration::deleteByName('PG_ENV') || 
		!Configuration::deleteByName('PG_LOG') || 
		!Configuration::deleteByName('PG_ORDER_STATUS') || 
		!Configuration::deleteByName('PG_ERROR') || 
		!Configuration::deleteByName('PG_TARJETA') || 
		!Configuration::deleteByName('PG_SOFORT') || 
		!Configuration::deleteByName('PG_BIOCRYPTOLOGY') || 
		!Configuration::deleteByName('PG_BIZUM') || 
		!Configuration::deleteByName('PG_GPAY') || 
		!Configuration::deleteByName('PG_PAYPAL') || 
		!Configuration::deleteByName('PG_AMAZONPAY') || 
		!Configuration::deleteByName('PG_TRUSTLY') || 
		!Configuration::deleteByName('PG_BARZAHLEN') || 
		!Configuration::deleteByName('PG_CORREOS') || 
		!parent::uninstall())
			return false;
		return true;
	}
	
	private function _postValidation() {
		if (Tools::isSubmit ('btnSubmit')) {
			if (!Tools::getValue ('merchant_identifier') || !checkAlphanumeric (Tools::getValue ('merchant_identifier')))
				$this->post_errors [] = $this->l('El identificador proporcionado por Kineox es obligatorio.');
			if (!Tools::getValue ('secure_key') || !checkAlphanumeric (Tools::getValue ('secure_key')))
				$this->post_errors[] = $this->l('La clave de seguridad proporcioanda por Kineox es obligatoria.');

			if (Tools::getValue ('p1c') != 1 && Tools::getValue ('p1c') != 0)
				$this->post_errors[] = $this->l('Debe seleccionarse si se desea o no solicitar permitir pago en un click.');
			if (Tools::getValue ('p1c') == 1 && (!Tools::getValue('p1c_text') || !Tools::getValue('p1c_link')))
				$this->post_errors[] = $this->l('Al configurar el pago en un click debe rellenarse su texto y su enlace de políticas.');
			
			if (Tools::getValue ('env') != 1 && Tools::getValue ('env') != 2)
				$this->post_errors[] = $this->l('Debe seleccionarse un entorno.');
			if (Tools::getValue ('log') != 1 && Tools::getValue ('log') != 0)
				$this->post_errors[] = $this->l('Debe seleccionarse si se desean logs o no.');
			if (Tools::getValue ('error') != 1 && Tools::getValue ('error') != 0)
				$this->post_errors[] = $this->l('Debe seleccionarse si se puede continuar tras error o no.');
			if (Tools::getValue ('require_cardholder') != 1 && Tools::getValue ('require_cardholder') != 0)
				$this->post_errors[] = $this->l('Debe seleccionarse si se desea o no solicitar el titular de tarjeta.');

			if (!Tools::getValue('order_status') || Tools::getValue('order_status') < 1)
				$this->post_errors[] = $this->l('El estado de pedido seleccionado debe ser válido.');
			if (!Tools::getValue('tarjeta') && !Tools::getValue('sofort') && !Tools::getValue('biocryptology') && !Tools::getValue('bizum') && !Tools::getValue('gpay') && !Tools::getValue('paypal') && !Tools::getValue('amazonpay') && !Tools::getValue('trustly') && !Tools::getValue('barzahlen') && !Tools::getValue('correos'))
				$this->post_errors[] = $this->l('Debe seleccionar al menos un método de pago.');
		}
	}
	
	private function _postProcess() {
		if (Tools::isSubmit ('btnSubmit')) {
			Configuration::updateValue ('PG_MERCHANT_IDENTIFIER', Tools::getValue ('merchant_identifier'));
			Configuration::updateValue ('PG_KEY', Tools::getValue ('secure_key'));
			Configuration::updateValue ('PG_STYLE_BACK_BOTON', Tools::getValue ('style_back_boton'));
			Configuration::updateValue ('PG_STYLE_COLOR_BOTON', Tools::getValue ('style_color_boton'));
			Configuration::updateValue ('PG_STYLE_BACK_FRAME', Tools::getValue ('style_back_frame'));
			Configuration::updateValue ('PG_STYLE_COLOR_LABEL', Tools::getValue ('style_color_label'));
			Configuration::updateValue ('PG_REQUIRE_CARDHOLDER', Tools::getValue ('require_cardholder'));
			Configuration::updateValue ('PG_P1C', Tools::getValue ('p1c'));
			Configuration::updateValue ('PG_P1C_TEXT', Tools::getValue ('p1c_text'));
			Configuration::updateValue ('PG_P1C_LINK', Tools::getValue ('p1c_link'));
			Configuration::updateValue ('PG_ENV', Tools::getValue ('env'));
			Configuration::updateValue ('PG_LOG', Tools::getValue ('log'));
			Configuration::updateValue ('PG_ORDER_STATUS', Tools::getValue ('order_status'));
			Configuration::updateValue ('PG_ERROR', Tools::getValue ('error'));
			Configuration::updateValue ('PG_TARJETA', Tools::getValue ('tarjeta'));
			Configuration::updateValue ('PG_SOFORT', Tools::getValue ('sofort'));
			Configuration::updateValue ('PG_BIOCRYPTOLOGY', Tools::getValue ('biocryptology'));
			Configuration::updateValue ('PG_BIZUM', Tools::getValue ('bizum'));
			Configuration::updateValue ('PG_GPAY', Tools::getValue ('gpay'));
			Configuration::updateValue ('PG_PAYPAL', Tools::getValue ('paypal'));
			Configuration::updateValue ('PG_AMAZONPAY', Tools::getValue ('amazonpay'));
			Configuration::updateValue ('PG_TRUSTLY', Tools::getValue ('trustly'));
			Configuration::updateValue ('PG_BARZAHLEN', Tools::getValue ('barzahlen'));
			Configuration::updateValue ('PG_CORREOS', Tools::getValue ('correos'));
			
		}
		$this->html .= $this->displayConfirmation ($this->l('Configuración actualizada con éxito.'));
	}
	
	private function _displayPayment(){
		$this->html .= '<div class=""><img src="../modules/kxpay/views/img/logoUp.png" style="display:block; margin: 0 auto; max-height: 120px;">'
		.'<h1 style="text-align: center;">'.$this->l('Este módulo le permite aceptar pagos mediante multiples opciones.').'</h1></div>';
	}
	
	private function _displayForm(){
		$env = Tools::getValue('env', $this->env);
		$p1c = Tools::getValue('p1c', $this->p1c);
		$log = Tools::getValue('log', $this->log);
		$error = Tools::getValue('error', $this->error);
		$order_status = Tools::getValue('order_status', $this->order_status);
		$require_cardholder = Tools::getValue('require_cardholder', $this->require_cardholder);
		$tarjeta = Tools::getValue('tarjeta', $this->tarjeta);
		$sofort = Tools::getValue('sofort', $this->sofort);
		$biocryptology = Tools::getValue('biocryptology', $this->biocryptology);
		$bizum = Tools::getValue('bizum', $this->bizum);
		$gpay = Tools::getValue('gpay', $this->gpay);
		$paypal = Tools::getValue('paypal', $this->paypal);
		$amazonpay = Tools::getValue('amazonpay', $this->amazonpay);
		$trustly = Tools::getValue('trustly', $this->trustly);
		$barzahlen = Tools::getValue('barzahlen', $this->barzahlen);
		$correos = Tools::getValue('correos', $this->correos);
		ob_start(); ?>
		<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" style="padding: 2em;">
			<h3 style="text-align: center;"><?php echo $this->l('Completa los campos de configuración');?></h3>
			<div class="row" style="max-width: 1200px; margin: 0 auto;">
				<fieldset>
					<legend><?php echo $this->l('Parámetros generales');?></legend>
					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6">
						<label for="merchant_identifier" class=" control-label">
							<?php echo $this->l('Identificador del comercio');?>
						</label>
						<div class="">
							<input type="text" placeholder="<?php echo $this->l('Escribe el identificador único de tu tienda online.');?>" class="form-control" name="merchant_identifier" id="merchant_identifier" value="<?php echo Tools::getValue('merchant_identifier', $this->merchant_identifier); ?>">
						</div>
					</div>
					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6">
						<label for="secure_key" class=" control-label">
							<?php echo $this->l('Clave del comercio');?>
						</label>
						<div class="">
							<input type="text" placeholder="<?php echo $this->l('Escribe la clave proporcionada de tu tienda online.');?>" class="form-control" name="secure_key" id="secure_key" value="<?php echo Tools::getValue('secure_key', $this->secure_key); ?>">
						</div>
					</div>
					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6">
						<label for="env" class=" control-label">
							<?php echo $this->l('Entorno');?>
						</label>
						<div class="">
							<select class="form-control" id="env" name="env">
								<option <?php if ($env == 1) echo 'selected'; ?> value="1"><?php echo $this->l('Test - Entorno de pruebas');?></option>
								<option <?php if ($env == 2) echo 'selected'; ?> value="2"><?php echo $this->l('Producción - Entorno real');?></option>
							</select>
						</div>
					</div>
					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6">
						<label for="p1c" class=" control-label">
							<?php echo $this->l('Permitir pago en 1 click');?>
						</label>
						<div class="">
							<select class="form-control" id="p1c" name="p1c">
								<option <?php if ($p1c == 0) echo 'selected'; ?> value="0"><?php echo $this->l('No');?></option>
								<option <?php if ($p1c == 1) echo 'selected'; ?> value="1"><?php echo $this->l('Sí');?></option>
							</select>
						</div>
					</div>
					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6">
						<label for="p1c_text" class=" control-label">
							<?php echo $this->l('Texto de condiciones de pago');?>
						</label>
						<div class="">
							<input type="text" <?php if($p1c == 0) echo 'readonly';?> class="form-control" name="p1c_text" id="p1c_text" value="<?php echo Tools::getValue('p1c_text', $this->p1c_text); ?>">
						</div>
					</div>
					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6">
						<label for="p1c_link" class=" control-label">
							<?php echo $this->l('Enlace de condiciones de pago');?>
						</label>
						<div class="">
							<input type="text" <?php if($p1c == 0) echo 'readonly';?> class="form-control" name="p1c_link" id="p1c_link" value="<?php echo Tools::getValue('p1c_link', $this->p1c_link); ?>">
						</div>
					</div>
				</fieldset>
				
				<fieldset>
					<legend><?php echo $this->l('Personalización');?></legend>
					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6">
						<label for="style_back_boton" class="mColorPickerTrigger control-label" id="icp_style_back_boton" data-mcolorpicker="true">
							<?php echo $this->l('Color del fondo del botón');?><img src="../img/admin/color.png" />
						</label>
						<div class="">
							<input type="text" readonly class="form-control mColorPicker" data-hex="true" autocomplete="off" name="style_back_boton" id="style_back_boton" value="<?php echo Tools::getValue('style_back_boton', $this->style_back_boton); ?>">
						</div>
					</div>
					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6">
						<label for="style_color_boton" class="mColorPickerTrigger control-label" id="icp_style_color_boton" data-mcolorpicker="true">
							<?php echo $this->l('Color de las letras del botón');?><img src="../img/admin/color.png" />
						</label>
						<div class="">
							<input type="text" readonly class="form-control mColorPicker"  data-hex="true" autocomplete="off"  name="style_color_boton" id="style_color_boton" value="<?php echo Tools::getValue('style_color_boton', $this->style_color_boton); ?>">
						</div>
					</div>
					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6">
						<label for="style_back_frame" class="mColorPickerTrigger control-label" id="icp_style_back_frame" data-mcolorpicker="true">
							<?php echo $this->l('Color del fondo del iframe');?><img src="../img/admin/color.png" />
						</label>
						<div class="">
							<input type="text" readonly class="form-control mColorPicker"  data-hex="true" autocomplete="off" name="style_back_frame" id="style_back_frame" value="<?php echo Tools::getValue('style_back_frame', $this->style_back_frame); ?>">
						</div>
					</div>
					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6" style="display:none;">
						<label for="style_color_label" class="mColorPickerTrigger control-label" id="icp_style_color_label"data-mcolorpicker="true">
							<?php echo $this->l('Color de las letras del iframe');?><img src="../img/admin/color.png" />
						</label>
						<div class="">
							<input type="text" readonly class="form-control mColorPicker" data-hex="true" autocomplete="off" name="style_color_label" id="style_color_label" value="<?php echo Tools::getValue('style_color_label', $this->style_color_label); ?>">
						</div>
					</div>
				</fieldset>	
				<fieldset>
					<legend><?php echo $this->l('Parámetros avanzados');?></legend>
					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6">
						<label for="require_cardholder" class=" control-label">
							<?php echo $this->l('Solicitar titular de tarjeta');?>
						</label>
						<div class="">
							<select class="form-control" id="require_cardholder" name="require_cardholder">
								<option <?php if ($require_cardholder == 0) echo 'selected'; ?> value="0"><?php echo $this->l('No');?></option>
								<option <?php if ($require_cardholder == 1) echo 'selected'; ?> value="1"><?php echo $this->l('Sí');?></option>
							</select>
						</div>
					</div>
					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6">
						<label for="log" class=" control-label">
							<?php echo $this->l('Activar log');?>
						</label>
						<div class="">
							<select class="form-control" id="log" name="log">
								<option <?php if ($log == 0) echo 'selected'; ?> value="0"><?php echo $this->l('No');?></option>
								<option <?php if ($log == 1) echo 'selected'; ?> value="1"><?php echo $this->l('Sí');?></option>
							</select>
						</div>
					</div>

					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6">
						<label for="order_status" class=" control-label">
							<?php echo $this->l('Estado de pedido una vez pagado');?>
						</label>
						<div class="">
							<select class="form-control" id="order_status" name="order_status">
								<?php 
								$order_states = OrderState::getOrderStates($this->context->language->id);
								foreach ($order_states as $state) {
									if($state['unremovable'] == '1') { ?>
								<option value="<?php echo $state['id_order_state'] ?>" <?php if($order_status==$state['id_order_state']) echo 'selected' ?>><?php echo $state['name'] ?></option>
								<?php }} ?>
							</select>
						</div>
					</div>
				</fieldset>	
				<fieldset>
					<legend><?php echo $this->l('Métodos de pago');?></legend>
					<div class="form-group col-lg-4 col-sm-6 col-xs-12  col-md-6">
						<div class="">
						    <input type="checkbox" name="tarjeta" value="1" <?php if ($tarjeta == '1') echo 'checked'; ?> >Pago con tarjeta de credito y debito<br>
						<!--	
							<input type="checkbox" name="sofort" value="1" <?php //if ($sofort == '1') echo 'checked'; ?>>Pago con Sofort/Klarna, transferencias online<br>
							<input type="checkbox" name="correos" value="1" <?php// if ($correos == '1') echo 'checked'; ?>>Pago en oficina de Correos<br>
							<input type="checkbox" name="trustly" value="1" <?php //if ($trustly == '1') echo 'checked'; ?>>Pago con Trustly, transferencias online<br>
							<input type="checkbox" name="biocryptology" value="1" <?php //if ($biocryptology == '1') echo 'checked'; ?>>Pago seguro con Biocryptology<br>
							<input type="checkbox" name="paypal" value="1" <?php// if ($paypal == '1') echo 'checked'; ?>>Pago con Paypal<br>
							<input type="checkbox" name="barzahlen" value="1" <?php //if ($barzahlen == '1') echo 'checked'; ?>>Pago con Barzahlen (solo alemania)<br>
							<input type="checkbox" name="amazonpay" value="1" <?php //if ($amazonpay == '1') echo 'checked'; ?>>Pago con Amazon Pay<br>
							<input type="checkbox" name="gpay" value="1" <?php //if ($gpay == '1') echo 'checked'; ?>>Pago con Google Pay<br>
							<input type="checkbox" name="bizum" value="1" <?php //if ($bizum == '1') echo 'checked'; ?>>Pago con Bizum<br>
							-->
						</div>
					</div>
				</fieldset>	
				<div class="col-12 buttons-modal-container">
					<input style="color: white;background: #555;border: none;padding: 5px 15px;margin: 2em auto;display: block;font-weight: bold;text-transform: uppercase;" class="button" name="btnSubmit" value="<?php echo $this->l('Guardar configuración'); ?>" type="submit" />
				</div>
				<script type="text/javascript" src="<?php echo __PS_BASE_URI__ ?>js/jquery/plugins/jquery.colorpicker.js"></script>
				<style>
				label.control-label{
					cursor:pointer;
				}
				label.control-label img{
					margin-left: 1em;
				}
				#mColorPickerInput{
					color: #333 !important;
					text-align: center;
				}
				</style>
				<script>
					$(function(){
						$('#p1c').on('change', function(){
							if ($(this).val() == 1){
								$('#p1c_link, #p1c_text').attr('readonly', false);
							} else {
								$('#p1c_link, #p1c_text').attr('readonly', true);
							}
						});
						$.each($('.mColorPicker'), function(i,v){
							$(v).attr('style', 'background:'+$(v).val())
						});
					})
				</script>
			</div>
		</form>
		<?php 
		$this->html .= ob_get_contents();
		ob_end_clean();
	}

	public function getContent()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postValidation();
			if (!count($this->post_errors))
				$this->_postProcess();
				else
					foreach ($this->post_errors as $err)
						$this->html .= $this->displayError($err);
		}
		else{
			$this->html .= '<br />';
		}
		
		$this->_html .= $this->_displayPayment();
		$this->_html .=	$this->_displayForm();
		
		return $this->html;
	}
	
	private function createParameter($parameters){
		
		$customer = new Customer ($parameters ['cart']->id_customer);

		$currency = new Currency($parameters['cart']->id_currency);
		$currency_decimals = is_array($currency) ? (int) $currency['decimals'] : (int) $currency->decimals;
		$cart_details = $parameters['cart']->getSummaryDetails(null, true);
		$decimals = $currency_decimals * _PS_PRICE_DISPLAY_PRECISION_;

		$shipping = $cart_details['total_shipping_tax_exc'];
		$subtotal = $cart_details['total_price_without_tax'] - $cart_details['total_shipping_tax_exc'];
		$tax = $cart_details['total_tax'];
		$total_price = Tools::ps_round($shipping + $subtotal + $tax, $decimals);
		$address = new Address((int)$parameters['cart']->id_address_invoice);
		
		$orderId = $parameters ['cart']->id;

		$data = array();
		$params = array();

		
		$data['AMOUNT'] = (int)number_format($total_price, 2, '', '');

		$protocol = (empty ($_SERVER ['HTTPS']))? 'http://': 'https://';
		$data['URL_RESPONSE'] = $protocol.$_SERVER['HTTP_HOST'].__PS_BASE_URI__ .'index.php?fc=module&module=kxpay&controller=validation';
		$data['URL_OK'] = $protocol . $_SERVER ['HTTP_HOST'].__PS_BASE_URI__.'index.php?controller=order-confirmation&id_cart='.$orderId.'&id_module='.$this->id.'&id_order='.$this->currentOrder.'&key='.$customer->secure_key;
		$data['URL_KO'] = $this->context->link->getModuleLink('kxpay', 'redirect', ['action' => 'error'], true);

		$params['CURRENCY'] = (int)$currency->iso_code_num;
		$params['LOCALE'] = Tools::substr ($_SERVER ['HTTP_ACCEPT_LANGUAGE'], 0, 2);

		if ($this->style_back_boton)
			$params['STYLE_BACK_BOTON'] = str_replace('#','', $this->style_back_boton);
		if ($this->style_color_boton)
			$params['STYLE_COLOR_BOTON'] = str_replace('#','', $this->style_color_boton);
		if ($this->style_back_frame)
			$params['STYLE_BACK_FRAME'] = str_replace('#','', $this->style_back_frame);
		if ($this->style_color_label)
			$params['STYLE_COLOR_LABEL'] = str_replace('#','', $this->style_color_label);
		$params['TARGET'] = '_parent';
		
		if ($this->require_cardholder){
			$params['REQUIRE_CARDHOLDER'] = 'true';
		}
		else
			$params['REQUIRE_CARDHOLDER'] = 'false';
		$params['AMOUNT_MAX'] = $data['AMOUNT'];
		$params['AMOUNT_MIN'] = $data['AMOUNT'];

		if ($this->p1c && $address->dni){
			$params['P1C'] = 'true';
		}
		else
			$params['P1C'] = 'false';
		$params['P1C_TEXT'] = $this->p1c_text;
		$params['P1C_LINK'] = $this->p1c_link;

		$params['IDENTIFIER'] = $customer->id;
		$params['PERSONAL_IDENTITY_NUMBER'] = $address->dni;

		$telefono = $address->phone_mobile ? $address->phone_mobile : $address->phone;
		
		$params['MAIL_BC'] = (string) $this->context->cookie->email;
		$params['TELEFONO_BC'] = (string) $telefono;
		
		$data['MERCHANT_IDENTIFIER'] = $this->merchant_identifier;

		if ($this->tarjeta == '1')
		{
			$data['OPERATION'] = str_pad ($orderId.str_pad(time()%1000, 3, '0', STR_PAD_LEFT), 11, "0", STR_PAD_LEFT).'T';
		writePaymentGatewayLog("Order ID: '".$orderId."'. Order Number Gateway: '" . $data['OPERATION'] . "'", $this->active_log);
			$params['PAYMENT_CHANNELS']="CARD";

			$data['PARAMS'] = $params;
			$data['SIGNATURE'] = hash("sha256",$data["MERCHANT_IDENTIFIER"].$data["AMOUNT"].$data['OPERATION'].$data["URL_RESPONSE"].$data["URL_OK"].$data["URL_KO"].$this->secure_key, FALSE );
			
			$data_string = json_encode($data);
			$ch = curl_init($this->urltoken);
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
			
			writePaymentGatewayLog("Order ID: '".$orderId."'. Datos enviados: '" . $data_string . "'", $this->active_log);
			writePaymentGatewayLog("Order ID: '".$orderId."'. respuesta token: '" . $response .  "'", $this->active_log);			
		}
		else
			$token_tarjeta = "";
		if ($this->sofort == '1')
		{
			$data['OPERATION'] = str_pad ($orderId.str_pad(time()%1000, 3, '0', STR_PAD_LEFT), 11, "0", STR_PAD_LEFT).'S';
		writePaymentGatewayLog("Order ID: '".$orderId."'. Order Number Gateway: '" . $data['OPERATION'] . "'", $this->active_log);
			$params['PAYMENT_CHANNELS']="SOFORT";

			$data['PARAMS'] = $params;
			$data['SIGNATURE'] = hash("sha256",$data["MERCHANT_IDENTIFIER"].$data["AMOUNT"].$data['OPERATION'].$data["URL_RESPONSE"].$data["URL_OK"].$data["URL_KO"].$this->secure_key, FALSE );
			
			$data_string = json_encode($data);
			$ch = curl_init($this->urltoken);
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
			$token_sofort = $result_string ["TOKEN"];
			
			writePaymentGatewayLog("Order ID: '".$orderId."'. Datos enviados: '" . $data_string . "'", $this->active_log);
			writePaymentGatewayLog("Order ID: '".$orderId."'. respuesta token: '" . $response .  "'", $this->active_log);			
		}
		else
			$token_sofort = "";
		if ($this->biocryptology == '1')
		{
			$data['OPERATION'] = str_pad ($orderId.str_pad(time()%1000, 3, '0', STR_PAD_LEFT), 11, "0", STR_PAD_LEFT).'B';
		writePaymentGatewayLog("Order ID: '".$orderId."'. Order Number Gateway: '" . $data['OPERATION'] . "'", $this->active_log);
			$params['PAYMENT_CHANNELS']="BIOCRYPTOLOGY";

						$data['PARAMS'] = $params;
			$data['SIGNATURE'] = hash("sha256",$data["MERCHANT_IDENTIFIER"].$data["AMOUNT"].$data['OPERATION'].$data["URL_RESPONSE"].$data["URL_OK"].$data["URL_KO"].$this->secure_key, FALSE );

			$data_string = json_encode($data);
			$ch = curl_init($this->urltoken);
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
			$token_biocryptology = $result_string ["TOKEN"];
			
			writePaymentGatewayLog("Order ID: '".$orderId."'. Datos enviados: '" . $data_string . "'", $this->active_log);
			writePaymentGatewayLog("Order ID: '".$orderId."'. respuesta token: '" . $response .  "'", $this->active_log);			
		}
		else
			$token_biocryptology = "";
		if ($this->bizum == '1')
		{
			$data['OPERATION'] = str_pad ($orderId.str_pad(time()%1000, 3, '0', STR_PAD_LEFT), 11, "0", STR_PAD_LEFT).'I';
		writePaymentGatewayLog("Order ID: '".$orderId."'. Order Number Gateway: '" . $data['OPERATION'] . "'", $this->active_log);
			$params['PAYMENT_CHANNELS']="BIZUM";

			$data['PARAMS'] = $params;
			$data['SIGNATURE'] = hash("sha256",$data["MERCHANT_IDENTIFIER"].$data["AMOUNT"].$data['OPERATION'].$data["URL_RESPONSE"].$data["URL_OK"].$data["URL_KO"].$this->secure_key, FALSE );
			
			$data_string = json_encode($data);
			$ch = curl_init($this->urltoken);
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
			$token_bizum = $result_string ["TOKEN"];
			
			writePaymentGatewayLog("Order ID: '".$orderId."'. Datos enviados: '" . $data_string . "'", $this->active_log);
			writePaymentGatewayLog("Order ID: '".$orderId."'. respuesta token: '" . $response .  "'", $this->active_log);			
		}
		else
			$token_bizum = "";
		if ($this->gpay == '1')
		{
			$data['OPERATION'] = str_pad ($orderId.str_pad(time()%1000, 3, '0', STR_PAD_LEFT), 11, "0", STR_PAD_LEFT).'G';
		writePaymentGatewayLog("Order ID: '".$orderId."'. Order Number Gateway: '" . $data['OPERATION'] . "'", $this->active_log);
			$params['PAYMENT_CHANNELS']="GOOGLE";

			$data['PARAMS'] = $params;
			$data['SIGNATURE'] = hash("sha256",$data["MERCHANT_IDENTIFIER"].$data["AMOUNT"].$data['OPERATION'].$data["URL_RESPONSE"].$data["URL_OK"].$data["URL_KO"].$this->secure_key, FALSE );

			$data_string = json_encode($data);
			$ch = curl_init($this->urltoken);
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
			$token_gpay = $result_string ["TOKEN"];
			
			writePaymentGatewayLog("Order ID: '".$orderId."'. Datos enviados: '" . $data_string . "'", $this->active_log);
			writePaymentGatewayLog("Order ID: '".$orderId."'. respuesta token: '" . $response .  "'", $this->active_log);
		}
		else
			$token_gpay = "";
		if ($this->paypal == '1')
		{
			$data['OPERATION'] = str_pad ($orderId.str_pad(time()%1000, 3, '0', STR_PAD_LEFT), 11, "0", STR_PAD_LEFT).'P';
		writePaymentGatewayLog("Order ID: '".$orderId."'. Order Number Gateway: '" . $data['OPERATION'] . "'", $this->active_log);
			$params['PAYMENT_CHANNELS']="PAYPAL";

			$data['PARAMS'] = $params;
			$data['SIGNATURE'] = hash("sha256",$data["MERCHANT_IDENTIFIER"].$data["AMOUNT"].$data['OPERATION'].$data["URL_RESPONSE"].$data["URL_OK"].$data["URL_KO"].$this->secure_key, FALSE );

			$data_string = json_encode($data);
			$ch = curl_init($this->urltoken);
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
			$token_paypal = $result_string ["TOKEN"];
			
			writePaymentGatewayLog("Order ID: '".$orderId."'. Datos enviados: '" . $data_string . "'", $this->active_log);
			writePaymentGatewayLog("Order ID: '".$orderId."'. respuesta token: '" . $response .  "'", $this->active_log);
		}
		else
			$token_paypal = "";
		if ($this->amazonpay == '1')
		{
			$data['OPERATION'] = str_pad ($orderId.str_pad(time()%1000, 3, '0', STR_PAD_LEFT), 11, "0", STR_PAD_LEFT).'A';
		writePaymentGatewayLog("Order ID: '".$orderId."'. Order Number Gateway: '" . $data['OPERATION'] . "'", $this->active_log);
			$params['PAYMENT_CHANNELS']="AMAZON";

			$data['PARAMS'] = $params;
			$data['SIGNATURE'] = hash("sha256",$data["MERCHANT_IDENTIFIER"].$data["AMOUNT"].$data['OPERATION'].$data["URL_RESPONSE"].$data["URL_OK"].$data["URL_KO"].$this->secure_key, FALSE );

			$data_string = json_encode($data);
			$ch = curl_init($this->urltoken);
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
			$token_amazonpay = $result_string ["TOKEN"];
			
			writePaymentGatewayLog("Order ID: '".$orderId."'. Datos enviados: '" . $data_string . "'", $this->active_log);
			writePaymentGatewayLog("Order ID: '".$orderId."'. respuesta token: '" . $response .  "'", $this->active_log);			
		}
		else
			$token_amazonpay = "";
		if ($this->trustly == '1')
		{
			$data['OPERATION'] = str_pad ($orderId.str_pad(time()%1000, 3, '0', STR_PAD_LEFT), 11, "0", STR_PAD_LEFT).'T';
		writePaymentGatewayLog("Order ID: '".$orderId."'. Order Number Gateway: '" . $data['OPERATION'] . "'", $this->active_log);
			$params['PAYMENT_CHANNELS']="TRUSTLY";

			$data['PARAMS'] = $params;
			$data['SIGNATURE'] = hash("sha256",$data["MERCHANT_IDENTIFIER"].$data["AMOUNT"].$data['OPERATION'].$data["URL_RESPONSE"].$data["URL_OK"].$data["URL_KO"].$this->secure_key, FALSE );

			$data_string = json_encode($data);
			$ch = curl_init($this->urltoken);
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
			$token_trustly = $result_string ["TOKEN"];
			
			writePaymentGatewayLog("Order ID: '".$orderId."'. Datos enviados: '" . $data_string . "'", $this->active_log);
			writePaymentGatewayLog("Order ID: '".$orderId."'. respuesta token: '" . $response .  "'", $this->active_log);			
		}
		else
			$token_trustly = "";
		if ($this->barzahlen == '1')
		{
			$data['OPERATION'] = str_pad ($orderId.str_pad(time()%1000, 3, '0', STR_PAD_LEFT), 11, "0", STR_PAD_LEFT).'B';
		writePaymentGatewayLog("Order ID: '".$orderId."'. Order Number Gateway: '" . $data['OPERATION'] . "'", $this->active_log);
			$params['PAYMENT_CHANNELS']="BARZAHLEN";

			$data['PARAMS'] = $params;
			$data['SIGNATURE'] = hash("sha256",$data["MERCHANT_IDENTIFIER"].$data["AMOUNT"].$data['OPERATION'].$data["URL_RESPONSE"].$data["URL_OK"].$data["URL_KO"].$this->secure_key, FALSE );

			
			$data_string = json_encode($data);
			$ch = curl_init($this->urltoken);
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
			$token_barzahlen = $result_string ["TOKEN"];
			
			writePaymentGatewayLog("Order ID: '".$orderId."'. Datos enviados: '" . $data_string . "'", $this->active_log);
			writePaymentGatewayLog("Order ID: '".$orderId."'. respuesta token: '" . $response .  "'", $this->active_log);			
		}
		else
			$token_barzahlen = "";
		if ($this->correos == '1')
		{
			$data['OPERATION'] = str_pad ($orderId.str_pad(time()%1000, 3, '0', STR_PAD_LEFT), 11, "0", STR_PAD_LEFT).'C';
		writePaymentGatewayLog("Order ID: '".$orderId."'. Order Number Gateway: '" . $data['OPERATION'] . "'", $this->active_log);
			$params['PAYMENT_CHANNELS']="CORREOS";

			$data['PARAMS'] = $params;
			$data['SIGNATURE'] = hash("sha256",$data["MERCHANT_IDENTIFIER"].$data["AMOUNT"].$data['OPERATION'].$data["URL_RESPONSE"].$data["URL_OK"].$data["URL_KO"].$this->secure_key, FALSE );

			$data_string = json_encode($data);
			$ch = curl_init($this->urltoken);
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
			$token_correos = $result_string ["TOKEN"];
			
			writePaymentGatewayLog("Order ID: '".$orderId."'. Datos enviados: '" . $data_string . "'", $this->active_log);
			writePaymentGatewayLog("Order ID: '".$orderId."'. respuesta token: '" . $response .  "'", $this->active_log);			
		}
		else
			$token_correos = "";


		
		$this->smarty->assign (array (
				'urlenv' => $this->urlenv,
				'token_tarjeta' => $token_tarjeta,
				'token_sofort' => $token_sofort,
				'token_biocryptology' => $token_biocryptology,
				'token_bizum' => $token_bizum,
				'token_gpay' => $token_gpay,
				'token_paypal' => $token_paypal,
				'token_amazonpay' => $token_amazonpay,
				'token_trustly' => $token_trustly,
				'token_barzahlen' => $token_barzahlen,
				'token_correos' => $token_correos,
				'key' => $this->secure_key,
		));
	}
	
	public function hookPayment($params) {
		if (!$this->active) {
			return;
		}
		if (!$this->checkCurrency ($params ['cart'])) {
			return;
		}
		$this->createParameter($params);
		return $this->display(__FILE__, 'payment.tpl');
	}
	
/* Version nubeser */
/*
	public function hookPaymentOptions($params) {
		$cardPaymentOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
		$cardPaymentOption->setCallToActionText($this->l('Pulsa para ver los métodos de pago disponibles'))
			->setBinary(true)
			->setAdditionalInformation($this->l('Acepta las condiciones y términos del servicio inferiores para continuar.'))
			->setModuleName($this->name);
		return [$cardPaymentOption];
	}
*/
/* version presteamshop*/
    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $option = new \PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $option->setModuleName($this->name)
            ->setCallToActionText($this->l('Pulsa para ver los métodos de pago disponibles'))
            ->setAction($this->context->link->getModuleLink($this->name, 'payment', array(), true));

        return [
            $option
        ];
    }
	

	public function hookDisplayPaymentByBinaries($params){
		if (!$this->active) {
			return;
		}
		if (!$this->checkCurrency ($params ['cart'])) {
			return;
		}
		$this->createParameter($params);
		return $this->display(__FILE__, 'payment_frame.tpl');
	}

/*
	public function hookDisplayPaymentEU($params){
		if ($this->hookPayment($params) == null) {
			return null;
		}
		return array(
				'cta_text' => ($this->l('Pagar mediante UniversalPay Gateway')),
				'logo' => _MODULE_DIR_."/kxpay/views/img/logoUp.png",
				'form' => $this->display(__FILE__, "views/templates/hook/payment_eu.tpl"),
		);
	}
*/
	public function hookPaymentReturn($params) {
		$totaltoPay = null;
		$idOrder = null;
		if(_PS_VERSION_ >= 1.7){
			$totaltoPay = Tools::displayPrice ($params ['order']->getOrdersTotalPaid (), new Currency ($params ['order']->id_currency), false);
			$idOrder = $params ['order']->id;
		}else{
			$totaltoPay = Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false);
			$idOrder = $params['objOrder']->id;
		}
		if (!$this->active) {
			return;
		}
		
		$order_payment = OrderPayment::getByOrderId($idOrder);
		$current_method = $order_payment[0]->transaction_id;
		$method = explode(' - ', $current_method)[0];
		switch($method){
			case 'CARD':
				$method = $this->l('Tarjeta de débito/crédito');
			break;
			case 'SOFORT':
				$method = $this->l('Transferencia bancaria');
			break;
			case 'PAYPAL':
				$method = $this->l('Paypal');
			break;
			case 'CASH':
				$method = $this->l('Efectivo');
			break;
			case 'Trustly':
				$method = $this->l('Trustly');
			break;
			default:
				$method = $method;
		}
		/*
		This WS obtains the payment data. But it's unnecessary if we already have de payment_channel.

		$data = array (
			"merchantidentifier" => $this->merchant_identifier,
			"merchantoperation" => explode(' - ', $current_method)[1],
			"signature" => hash ( "sha256", $this->merchant_identifier . explode(' - ', $current_method)[1] . $this->secure_key, FALSE )
		);

		$url = curl_init ( $this-> );
					
		$data_string = json_encode ( $data );
		echo print_r($data_string,true);
		
		curl_setopt ( $url, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt ( $url, CURLOPT_POSTFIELDS, $data_string );
		curl_setopt ( $url, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $url, CURLOPT_HTTPHEADER, array (
			'Content-Type: application/json',
			'Content-Length: ' . strlen ( $data_string ),
			'authorization: Basic YmlvY3J5cHRvbG9neTo3NTgzOGE2NzQ4MDY='));
		$response = curl_exec ( $url );
		curl_close ( $url );
		*/
		$this->smarty->assign(array(
				'total_to_pay' => $totaltoPay,
				'status' => 'ok',
				'id_order' => $idOrder,
				'method' => $method
		));
		return $this->display (__FILE__, 'payment_return.tpl');
	}
	
	
	public function checkCurrency($cart) {
		$currency_order = new Currency ($cart->id_currency);
		$currencies_module = $this->getCurrency ($cart->id_currency);
		if (is_array ($currencies_module)) {
			foreach ($currencies_module as $currency_module) {
				if ($currency_order->id == $currency_module ['id_currency']) {
					return true;
				}
			}
		}
		return false;
	}

}