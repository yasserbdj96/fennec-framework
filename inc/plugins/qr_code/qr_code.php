<?php
include_once('plugin_qr_code.php');

if(realpath(__FILE__)==realpath($_SERVER['SCRIPT_FILENAME'])){
	$generator=new QRCode(base64_decode($_REQUEST['d']),$_REQUEST);
	$generator->output_image();
	exit(0);
}else{
	//check qr code data:
	$uri=explode('/',$uri);
	$uri=$uri[count($uri)-1];
	if(substr($uri,0,3)=="qr-"){
	    $uri=str_replace("qr-","",$uri);
	    $params = array("s"=>"qrh","fc"=>"000000","bc"=>"ffffff","wq"=>"1");
	    $generator=new QRCode($uri,$params);
	    $generator->output_image();
	}else{
	    header("Location: ".$back."404");
	    exit(0);
	}
}
?>