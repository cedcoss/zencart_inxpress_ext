<?php
/**
 * @package shippingMethod
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: perweightunit.php 15616 2010-03-06 04:07:11Z ajeh $
 */
/**
 * "Per Weight Unit" shipping module, allowing you to offer per-unit-rate shipping options
 *
 */
class inxpress extends base {
  /**
   * $code determines the internal 'code' name used to designate "this" payment module
   *
   * @var string
   */
  var $code;
  /**
   * $title is the displayed name for this payment method
   *
   * @var string
   */
  var $title;
  /**
   * $description is a soft name for this payment method
   *
   * @var string
   */
  var $description;
  /**
   * module's icon
   *
   * @var string
   */
  var $icon;
  /**
   * $enabled determines whether this module shows or not... during checkout.
   *
   * @var boolean
   */
  var $enabled;
  /**
     * Constructor
   *
   * @return perweightunit
   */
  function inxpress() {
    global $order, $db;

    $this->code = 'inxpress';
    $this->title = INXPRESS_TEXT_TITLE;
    $this->description = INXPRESS_TEXT_DESCRIPTION;
    $this->sort_order = INXPRESS_SHIPPING_SORT_ORDER;
    $this->icon = '';
    $this->tax_class = INXPRESS_SHIPPING_TAX_CLASS;
    $this->tax_basis = 'Shipping';
	
    // disable only when entire cart is free shipping
    if (zen_get_shipping_enabled($this->code)) {
      $this->enabled = true;
    }
	
    if ($this->enabled) {
      // check MODULE_SHIPPING_PERWEIGHTUNIT_HANDLING_METHOD is in
     /*  $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'INXPRESS_HANDLING_ACTION'");
      if ($check_query->EOF) {
       $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Handling Action', 'INXPRESS_HANDLING_ACTION','', '', '6', '0', 'zen_cfg_select_option(array(\'Per Order\', \'Per Package\'), ', now())");
		} */
	}
	
    if ( ($this->enabled == true) && ((int)INXPRESS_SHIPPING_ZONE > 0) ) {
      $check_flag = false;
      $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . "
                             where geo_zone_id = '" . INXPRESS_SHIPPING_ZONE . "'
                             and zone_country_id = '" . $order->delivery['country']['id'] . "'
                             order by zone_id");
      while (!$check->EOF) {
        if ($check->fields['zone_id'] < 1) {
          $check_flag = true;
          break;
        } elseif ($check->fields['zone_id'] == $order->delivery['zone_id']) {
          $check_flag = true;
          break;
        }
        $check->MoveNext();
      }

      if ($check_flag == false) {
        $this->enabled = false;
      }
    }
	
	
  }
  /**
   * Obtain quote from shipping system/calculations
   *
   * @param string $method
   * @return array
   */
  function quote($method = '') {
    global $order, $shipping_weight, $shipping_num_boxes,$db;
	
	
	$shippingInfo=$this->collectRates($order,$db);
	$shippingPrice=$shippingInfo['price'];
	$title=$db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'INXPRESS_TITLE'");
	$title=$title->fields['configuration_value'];
    $total_weight_units = $shipping_weight;
    $this->quotes = array('id' => $this->code,
                          'module' => $title,
                          'methods' => array(array('id' => $this->code,
                                                   'title' => 'Transit Days:'.$shippingInfo['days'],
                                                   'cost' => $shippingPrice  ) ));


    if ($this->tax_class > 0) {
      $this->quotes['tax'] = zen_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
    }

    if (zen_not_null($this->icon)) $this->quotes['icon'] = zen_image($this->icon, $this->title);

    return $this->quotes;
  }
  public function collectRates($order,$db)
  {
  
	$account = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'INXPRESS_ACCOUNT'");
	$account=$account->fields['configuration_value'];
	$final_lbh = '';
	$weight=0;
	$shippingPrice=array('price'=>0,'days'=>'0');
	
	if ($order->products) 
	{
		foreach ($order->products as $item) {
			
			
			
			{

				$dimweight =$db->Execute('select * from '.INXPRESS_VARIENTS_TABLE.' where product_id='.(int)$item['id']);
				
				if(!empty($dimweight))
				{
					$variable=$dimweight->fields['variable'];	
					
					if(($variable!=''&&$variable!=0))						
					{	
						
						if($variable>=$item['qty'])							
						{								
							if($dimweight->fields['dim_weight'] > $item['weight'])
							{
								$final_lbh.=$dimweight->fields['length'].'|'.$dimweight->fields['width'].'|'.$dimweight->fields['height'].'|'.$item['weight'].';';
								$weight=$weight+$dimweight->fields['dim_weight'];
							}	
							else 
							{
								$final_lbh.=$dimweight->fields['length'].'|'.$dimweight->fields['width'].'|'.$dimweight->fields['height'].'|'.$item['weight'].';';
								$weight=$weight+$item['weight'];
							}					
						}							
						else if($variable<$item['qty'])							
						{								
							$qty=ceil(($item['qty'])/$variable);	
							$prod_weight=$item['weight']*$qty;
							$prod_dim_weight=$dimweight->fields['dim_weight']*$qty;
							if($prod_dim_weight > $prod_weight)
							{
								$final_lbh.=$dimweight->fields['length'].'|'.$dimweight->fields['width'].'|'.$dimweight->fields['height'].'|'.$prod_weight.';';
								$weight=$weight+$prod_dim_weight;
							}
							else 
							{	
								$final_lbh.=$dimweight->fields['length'].'|'.$dimweight->fields['width'].'|'.$dimweight->fields['height'].'|'.$prod_weight.';';
								$weight=$weight+($item['weight']*$qty);
							}	
														
						}						
					}						
					else						
					{
						
						$prod_weight=$item['weight']*$item['qty'];
						$prod_dim_weight=$dimweight->fields['dim_weight']*$item['qty'];
						if($prod_dim_weight > $prod_weight)
						{
							$final_lbh.=$dimweight->fields['length'].'|'.$dimweight->fields['width'].'|'.$dimweight->fields['height'].'|'.$prod_weight.';';
							$weight=$weight+$prod_dim_weight;
						}	
						else 
						{	
								$final_lbh.=$dimweight->fields['length'].'|'.$dimweight->fields['width'].'|'.$dimweight->fields['height'].'|'.$prod_weight.';';
								$weight=$weight+($item['weight']*$item['qty']);
						}			
					}	
				
				
					
				}
				else 
				{
					$weight=$weight+($item['weight']*$item['qty']);
				}
				
				
				
				$code='';
				if($weight>0.5)	{
				
					$code='P';
				}
				else if($weight!=0&&$weight<=0.5)
				{
					$code='X';
				}
				
			}
		}
		
		
		
		$price=$this->calcRate($order,$account,$code,$order->delivery['country']['iso_code_2'],$weight,$final_lbh,$order->delivery['postcode']);
		
		if($price)
		{
			$shippingPrice=array('price'=>$price['price'],'days'=>$price['days']);
		}
		else 
		{
			$shippingPrice=array('price'=>0,'days'=>'0');
		}
	}
	elseif(true)
	{
		if($_SESSION['cart']->contents)
		{
			
			foreach ($_SESSION['cart']->contents as $id=>$val) {
				
				$product = $db->Execute("select pd.products_name, pd.products_description, pd.products_url,
                                      p.products_id, p.products_quantity, p.products_model,
                                      p.products_image, p.products_price, p.products_virtual, p.products_weight,
                                      p.products_date_added, p.products_last_modified,
                                      date_format(p.products_date_available, '%Y-%m-%d') as
                                      products_date_available, p.products_status, p.products_tax_class_id,
                                      p.manufacturers_id,
                                      p.products_quantity_order_min, p.products_quantity_order_units, p.products_priced_by_attribute,
                                      p.product_is_free, p.product_is_call, p.products_quantity_mixed,
                                      p.product_is_always_free_shipping, p.products_qty_box_status, p.products_quantity_order_max,
                                      p.products_sort_order,
                                      p.products_discount_type, p.products_discount_type_from,
                                      p.products_price_sorter, p.master_categories_id
                              from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd
                              where p.products_id = '" . $id . "'
                              and p.products_id = pd.products_id
                              and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
				$item=array('id'=>$id,'qty'=>$val['qty'],'weight'=>$product->fields['products_weight']);
				
				{

					$dimweight =$db->Execute('select * from '.INXPRESS_VARIENTS_TABLE.' where product_id='.(int)$item['id']);
					
					if(!empty($dimweight))
					{
						$variable=$dimweight->fields['variable'];	
						
						if(($variable!=''&&$variable!=0))						
						{	
							
							if($variable>=$item['qty'])							
							{								
								if($dimweight->fields['dim_weight'] > $item['weight'])
								{
									$final_lbh.=$dimweight->fields['length'].'|'.$dimweight->fields['width'].'|'.$dimweight->fields['height'].'|'.$item['weight'].';';
									$weight=$weight+$dimweight->fields['dim_weight'];
								}	
								else 
								{
									$final_lbh.=$dimweight->fields['length'].'|'.$dimweight->fields['width'].'|'.$dimweight->fields['height'].'|'.$item['weight'].';';
									$weight=$weight+$item['weight'];
								}					
							}							
							else if($variable<$item['qty'])							
							{								
								$qty=ceil(($item['qty'])/$variable);	
								$prod_weight=$item['weight']*$qty;
								$prod_dim_weight=$dimweight->fields['dim_weight']*$qty;
								if($prod_dim_weight > $prod_weight)
								{
									$final_lbh.=$dimweight->fields['length'].'|'.$dimweight->fields['width'].'|'.$dimweight->fields['height'].'|'.$prod_weight.';';
									$weight=$weight+$prod_dim_weight;
								}
								else 
								{	
									$final_lbh.=$dimweight->fields['length'].'|'.$dimweight->fields['width'].'|'.$dimweight->fields['height'].'|'.$prod_weight.';';
									$weight=$weight+($item['weight']*$qty);
								}	
															
							}						
						}						
						else						
						{
							
							$prod_weight=$item['weight']*$item['qty'];
							$prod_dim_weight=$dimweight->fields['dim_weight']*$item['qty'];
							if($prod_dim_weight > $prod_weight)
							{
								$final_lbh.=$dimweight->fields['length'].'|'.$dimweight->fields['width'].'|'.$dimweight->fields['height'].'|'.$prod_weight.';';
								$weight=$weight+$prod_dim_weight;
							}	
							else 
							{	
									$final_lbh.=$dimweight->fields['length'].'|'.$dimweight->fields['width'].'|'.$dimweight->fields['height'].'|'.$prod_weight.';';
									$weight=$weight+($item['weight']*$item['qty']);
							}			
						}	
					
					
						
					}
					else 
					{
						$weight=$weight+($item['weight']*$item['qty']);
					}
					
					
					
					$code='';
					if($weight>0.5)	{
					
						$code='P';
					}
					else if($weight!=0&&$weight<=0.5)
					{
						$code='X';
					}
					
				}
			}
			
			
			
			$price=$this->calcRate($order,$account,$code,$order->delivery['country']['iso_code_2'],$weight,$final_lbh,$order->delivery['postcode']);
			
			if($price)
			{
				$shippingPrice=array('price'=>$price['price'],'days'=>$price['days']);
			}
			else 
			{
				$shippingPrice=array('price'=>0,'days'=>'0');
			}
		}
	}
	return $shippingPrice;
}
  public function calcRate($order,$account,$code,$country,$weight,$dimension,$zip)
    {
		
		$log=fopen('inxpress.log','a');
    	$dimension = rtrim($dimension, ';');
    	$url = 'http://www.ixpapi.com/ixpapp/rates.php?acc='.$account.'&dst='.$country.'&prd='.$code.'&wgt='.$weight.'&pst='.$zip.'&pcs='.$dimension;
    	fwrite($log,'TimeStamp'.date('y-m-d H:m:i').PHP_EOL);
		fwrite($log,$url.PHP_EOL);
    	
		
    	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
		$data = curl_exec ($ch);
		curl_close ($ch); 
		$xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $data);
		$xml = simplexml_load_string($xml);
		$json = json_encode($xml);
		$responseArray = json_decode($json,true);
		fwrite($log,print_r($responseArray,true).PHP_EOL);
		if(isset($responseArray['totalCharge']))
		{
			$response=array();
			$handling=$this->getHandlingFee($order);
			fwrite($log,'Handling:'.$handling.PHP_EOL);
			$response['price']=$responseArray['totalCharge']+$handling;
			$response['days']=$responseArray['info']['baseCountryTransitDays'];
			return $response;
		}
		else 
		{
			return false;
		}
		
    }
	public function getHandlingFee($order)
	{
		global $db;
		$fee=0;
		$hadlingfee = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'INXPRESS_HANDLING_FEE'");
		$hadlingfee=$hadlingfee->fields['configuration_value'];
		$action = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'INXPRESS_HANDLING_ACTION'");
		$action=$action->fields['configuration_value'];
		$type = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'INXPRESS_HANDLING_TYPE'");
		$type=$type->fields['configuration_value'];
		if($type=='Fixed')
		{
			if($action=='Per Order')
			{
				return $hadlingfee;
			}
			else
			{
				if ($order->products) 
				{
					foreach ($order->products as $item) {
						if($item['qty']>0)
						{
							$fee+=(float)($item['qty'])*$hadlingfee;
						}
					}
				}
				elseif($_SESSION['cart']->contents)
				{
					foreach ($_SESSION['cart']->contents as $id=>$val) {
				
						
						$item=array('id'=>$id,'qty'=>$val['qty']);
						if($item['qty']>0)
						{
							$fee+=(float)($item['qty'])*$hadlingfee;
						}
					}
				}
			}
		}
		else
		{
			
			if($action=='Per Order')
			{
				return ($order->info['subtotal']*$hadlingfee)/100;
			}
			else
			{
				if ($order->products) 
				{
					foreach ($order->products as $item) {
						if($item['qty']>0)
						{
							$fee+=(float)($item['qty'])*(($item['final_price']*$hadlingfee)/100);
						}
					}
				}
				elseif($_SESSION['cart']->contents)
				{
					foreach ($_SESSION['cart']->contents as $id=>$val) {
				
						$product = $db->Execute("select pd.products_name, pd.products_description, pd.products_url,
											  p.products_id, p.products_quantity, p.products_model,
											  p.products_image, p.products_price, p.products_virtual, p.products_weight,
											  p.products_date_added, p.products_last_modified,
											  date_format(p.products_date_available, '%Y-%m-%d') as
											  products_date_available, p.products_status, p.products_tax_class_id,
											  p.manufacturers_id,
											  p.products_quantity_order_min, p.products_quantity_order_units, p.products_priced_by_attribute,
											  p.product_is_free, p.product_is_call, p.products_quantity_mixed,
											  p.product_is_always_free_shipping, p.products_qty_box_status, p.products_quantity_order_max,
											  p.products_sort_order,
											  p.products_discount_type, p.products_discount_type_from,
											  p.products_price_sorter, p.master_categories_id
									  from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd
									  where p.products_id = '" . $id . "'
									  and p.products_id = pd.products_id
									  and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
						$item=array('id'=>$id,'qty'=>$val['qty'],'weight'=>$product->fields['products_weight'],'final_price'=>$product->fields['products_price']);
						if($item['qty']>0)
						{
							$fee+=(float)($item['qty'])*(($item['final_price']*$hadlingfee)/100);
						}
					}
				}
			}
		}
		return $fee;
	}
  /**
   * Check to see whether module is installed
   *
   * @return boolean
   */
  function check() {
    global $db;
	
    if (!isset($this->_check)) {
      $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'INXPRESS_ACTIVE'");
	  
      $this->_check = $check_query->RecordCount();
    }
    return $this->_check;
  }
  function getConfigurationValue($configuration)
  {
		if (zen_not_null($configuration->fields['use_function'])) {
		  $use_function = $configuration->fields['use_function'];
		  if (preg_match('/->/', $use_function)) {
			$class_method = explode('->', $use_function);
			if (!is_object(${$class_method[0]})) {
			  include(DIR_WS_CLASSES . $class_method[0] . '.php');
			  ${$class_method[0]} = new $class_method[0]();
			}
			$cfgValue = zen_call_function($class_method[1], $configuration->fields['configuration_value'], ${$class_method[0]});
		  } else {
			$cfgValue = zen_call_function($use_function, $configuration->fields['configuration_value']);
		  }
		} else {
		  $cfgValue = $configuration->fields['configuration_value'];
		}
		return  $cfgValue;
  }
  /**
   * Install the shipping module and its configuration settings
   *
   */
  function install() {
    global $db;
	$userList = zen_get_users();
	$firstname=$userList[0]['name'];
	$lastname=$userList[0]['name'];
	$countryName = $db->Execute("select configuration_id, configuration_title, configuration_value, configuration_key,
                                        use_function from " . TABLE_CONFIGURATION . "
                                        where configuration_group_id = '1' and configuration_key='STORE_COUNTRY'
                                        ");
	$countryName=$this->getConfigurationValue($countryName);
	$region_name = $db->Execute("select configuration_id, configuration_title, configuration_value, configuration_key,
                                        use_function from " . TABLE_CONFIGURATION . "
                                        where configuration_group_id = '1' and configuration_key='STORE_ZONE'
                                        ");
	$region_name=$this->getConfigurationValue($region_name);
	$city = $db->Execute("select configuration_id, configuration_title, configuration_value, configuration_key,
                                        use_function from " . TABLE_CONFIGURATION . "
                                        where configuration_group_id = '1' and configuration_key='STORE_ZONE'
                                        ");
	$city=$this->getConfigurationValue($city);
	$zip=zen_get_configuration_key_value('SHIPPING_ORIGIN_ZIP');

	$phone='';

	$email=$userList[0]['email'];

	
	$address = $db->Execute("select configuration_id, configuration_title, configuration_value, configuration_key,
                                        use_function from " . TABLE_CONFIGURATION . "
                                        where configuration_group_id = '1' and configuration_key='STORE_NAME_ADDRESS'
                                        ");
										
	$address=$this->getConfigurationValue($address);

	$website=HTTP_SERVER.DIR_WS_CATALOG;
	$company='Zencart Store';
	
	$url = 'http://inxpressaz.force.com/leadcreation?cmp='.$company.'&fn='.$firstname.'&ln='.$lastname.'&add='.$address.'&ct='.$city.'&st='.$region_name.'&cnt='.$countryName.'&zp='.$zip.'&ph='.$phone.'&em='.$email.'&ws='.$website.'&ls=Zencart';
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);

	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);

	$data = curl_exec ($ch);

	
	
	$key1=md5(substr ($website,8,6)."_".time().substr ($email,2,5));
	
	
	
	
	$post_string = '';
	$params = array(
	'firstname'=>$firstname,
	'lastname'=>$lastname,
	'company'=>$company,
	'phone'=>$phone,
	'email'=>$email,
	'address'=>$address,
	'city'=>$city,
	'state'=>$region_name,
	'country'=>$countryName,
	'zipcode'=>$zip,
	'website'=>$website,
	'framework'=>7,
	'key'=>$key1,
	);

	foreach($params as $key=>$value) { $post_string .= $key.'='.$value.'&'; }
	$post_string = rtrim($post_string, '&');
	$url="http://webilyst.com/projects/inxpress/admin/index.php/downloadInfo/create";
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
	
	
	$result = curl_exec($ch);
	curl_close($ch);
	
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Account number', 'INXPRESS_ACCOUNT', '', '', '6', '0', now())");
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enabled', 'INXPRESS_ACTIVE','True', '', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('InXpress Account Number', 'INXPRESS_ACCOUNT_NUMBER', '', '', '6', '0', now())");
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Title', 'INXPRESS_TITLE', 'DHL Express', '', '6', '0', now())");
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Handling Type', 'INXPRESS_HANDLING_TYPE','Fixed', '', '6', '0', 'zen_cfg_select_option(array(\'Fixed\', \'Percent\'), ', now())");
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Handling Action', 'INXPRESS_HANDLING_ACTION','Per Order', '', '6', '0', 'zen_cfg_select_option(array(\'Per Order\', \'Per Package\'), ', now())");
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Handling Fee', 'INXPRESS_HANDLING_FEE','', '', '6', '0', now())");
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Unit Of Measure', 'INXPRESS_UNIT_OF_MEASURE','', '', '6', '0', 'zen_cfg_select_option(array(\'Per Order\', \'Per Package\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Shipping Zone', 'INXPRESS_SHIPPING_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, 	configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'INXPRESS_SHIPPING_SORT_ORDER', '0', 'Sort order of display.', '6', '0', now())");
  
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'INXPRESS_SHIPPING_TAX_CLASS', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'zen_get_tax_class_title', 'zen_cfg_pull_down_tax_classes(', now())");
	
	
	$db->Execute("insert into " . TABLE_ADMIN_MENUS . " (menu_key, language_key, sort_order) values ('inxpress', 'INXPRESS_MENU_TITLE', '1')");
	$varientTable=DB_PREFIX.'inxpress_variant';
	$dhlTable=DB_PREFIX.'inxpress_dhl';
	$db->Execute("
		CREATE TABLE IF NOT EXISTS `{$varientTable}` (
			`id` int(11) NOT NULL auto_increment,
			`product_id` int(11),
			`website` int(11),
			`store` int(11),
			`variant` text,
			`modifieddate` datetime,
			`length` text,
			`width` text,
			`height` text,
			`dim_weight` text,
			`variable` text,
			`extra` text,
			PRIMARY KEY  (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
$db->Execute("
		CREATE TABLE IF NOT EXISTS `{$dhlTable}` (
			`id` int(11) NOT NULL auto_increment,
			`website` int(11),
			`store` int(11),
			`supplies` text,
			`modifieddate` datetime,
			`length` text,
			`width` text,
			`height` text,
			`dim_weight` text,
			`variable` text,
			`extra` text,
			PRIMARY KEY  (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
	$db->Execute("INSERT INTO {$dhlTable} (supplies,length,width,height,modifieddate) values ('Express Envelope','12.6','9.4','1',now());");
	$db->Execute("INSERT INTO {$dhlTable} (supplies,length,width,height,modifieddate) values ('Express Legal Envelope','15','9.4','1',now());");
	$db->Execute("INSERT INTO {$dhlTable} (supplies,length,width,height,modifieddate) values ('Small Padded Pouch','9.8','12','1',now());");
	$db->Execute("INSERT INTO {$dhlTable} (supplies,length,width,height,modifieddate) values ('Large Padded Pouch','11.9','14.8','1',now());");
	$db->Execute("INSERT INTO {$dhlTable} (supplies,length,width,height,modifieddate) values ('Standard Flyer (Small Express Pack)','11.8','15.7','1',now());");
	$db->Execute("INSERT INTO {$dhlTable} (supplies,length,width,height,modifieddate) values ('Large Flyer (Large Express Pack)','15','18.7','1',now());");
	$db->Execute("INSERT INTO {$dhlTable} (supplies,length,width,height,modifieddate) values ('Box #2 Cube','10.8','5.8','5.9',now());");
	$db->Execute("INSERT INTO {$dhlTable} (supplies,length,width,height,modifieddate) values ('Box #2 Small','12.5','11.1','1.5',now());");
	$db->Execute("INSERT INTO {$dhlTable} (supplies,length,width,height,modifieddate) values ('Box #2 Medium','13.2','12.6','2.0',now());");
	$db->Execute("INSERT INTO {$dhlTable} (supplies,length,width,height,modifieddate) values ('Box #3 Large','17.5','12.5','3.0',now());");
	$db->Execute("INSERT INTO {$dhlTable} (supplies,length,width,height,modifieddate) values ('Box #3 Small Tri-Tube','5','5','25',now());");
	$db->Execute("INSERT INTO {$dhlTable} (supplies,length,width,height,modifieddate) values ('Box #4 Large Tri-Tube','38.4','6.9','6.9',now());");
	if (function_exists('zen_register_admin_page')) {
		
		if (!zen_page_key_exists('inxpress_manage_varients')) {
			// Add Monthly Report link to Reports menu
			
				zen_register_admin_page('inxpress_manage_varients', 'INXPRESS_VARIENTS_TITLE','INXPRESS_FILE', 'set=varients', 'inxpress', 'Y', 17);
			
		}
		if (!zen_page_key_exists('inxpress_manage_dhl')) {
			// Add Monthly Report link to Reports menu
			
				zen_register_admin_page('inxpress_manage_dhl', 'INXPRESS_DHL_TITLE','INXPRESS_FILE', 'set=dhl', 'inxpress', 'Y', 17);
			
		}
	}
  }
  /**
   * Remove the module and all its settings
   *
   */
  function remove() {
    global $db;
    $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE  'INXPRESS%'");
	$db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key='INXPRESS_HANDLING_ACTION'");
	
	$db->Execute("delete from " . TABLE_ADMIN_MENUS . " where menu_key='inxpress'");
	$db->Execute("DROP TABLE  IF EXISTS ". INXPRESS_VARIENTS_TABLE );
	$db->Execute("DROP TABLE  IF EXISTS ". INXPRESS_DHL_TABLE );
  }
  /**
   * Internal list of configuration keys used for configuration of the module
   *
   * @return array
   */
  function keys() {
    return array('INXPRESS_ACCOUNT','INXPRESS_ACTIVE','INXPRESS_ACCOUNT_NUMBER','INXPRESS_TITLE', 'INXPRESS_HANDLING_TYPE', 'INXPRESS_HANDLING_TYPE', 'INXPRESS_HANDLING_ACTION', 'INXPRESS_HANDLING_FEE', 'INXPRESS_UNIT_OF_MEASURE', 'INXPRESS_SHIPPING_ZONE', 'INXPRESS_SHIPPING_SORT_ORDER','INXPRESS_SHIPPING_TAX_CLASS');
  }
}