<?php

require_once(dirname(__FILE__) . '/paytm-sdk/encdec_paytm.php');

function paytm_config(){
    $configarray = array(
		"FriendlyName" => array("Type" => "System", "Value"=>"Paytm"),
		"merchant_id" => array("FriendlyName" => "Merchant ID", "Type" => "text", "Size" => "20", ),
		"merchant_key" => array("FriendlyName" => "Merchant Key", "Type" => "text", "Size" => "16", ),
		"transaction_url" => array("FriendlyName" => "Transaction Url", "Type" => "text", "Size" => "90", ),
		"transaction_status_url" => array("FriendlyName" => "Transaction Status Url", "Type" => "text", "Size" => "90", ),
		"website" => array("FriendlyName" => "Website name", "Type" => "text", "Size" => "20", ),
		"industry_type" => array("FriendlyName" => "Industry Name", "Type" => "text", "Size" => "20", ),
	);		
	return $configarray;
}

function paytm_link($params) {	

	$merchant_id = $params['merchant_id'];
	$secret_key=$params['merchant_key'];
	$order_id = $params['invoiceid'].'-'.time(); // Prepare unique order id for PayTM
	$website= $params['website'];
	$industry_type= $params['industry_type'];
	$channel_id="WEB";	
	$transaction_url = $params['transaction_url'];		
	$amount = $params['amount']; 
	$email = $params['clientdetails']['email'];
	$callBackLink=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[SCRIPT_NAME]";
	$callBackLink=str_replace('cart.php', 'modules/gateways/callback/paytm.php', $callBackLink);
	$callBackLink=str_replace('viewinvoice.php', 'modules/gateways/callback/paytm.php', $callBackLink);
	
	$post_variables = Array(
          "MID" => $merchant_id,
          "ORDER_ID" => $order_id ,
          "CUST_ID" => $email,
          "TXN_AMOUNT" => $amount,
          "CHANNEL_ID" => $channel_id,
          "INDUSTRY_TYPE_ID" => $industry_type,
          "CALLBACK_URL" => $callBackLink,
          "WEBSITE" => $website
          );
	$checksum = getChecksumFromArray($post_variables, $secret_key);
	$companyname = 'paytm';

	$code='<form method="post" action='. $transaction_url .'>';
	foreach ($post_variables as $key => $value) {
		$code.='<input type="hidden" name="'.$key.'" value="'.$value. '"/>';
	}
	$code.='<input type="hidden" name="CHECKSUMHASH" value="'. $checksum . '"/><input type="submit" value="Pay with Paytm" /></form>';
	return $code;
}
?>