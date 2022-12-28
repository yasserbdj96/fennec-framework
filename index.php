<?php
/*S*/
//
//header("HTTP/1.1 200 Success");

//Display errors:
//ini_set('error_reporting',E_ALL);
//ini_set('display_errors',0);

//Terms:
define("root_document",true);//To confirm that the file was not accessed from another place.
define('INCLUDE_DIR',dirname(__FILE__));

//Default settings:
$script_settings_info=array(
	"site_folder_patch"=>base64_encode("0"),//index.php place in server http://localhost/1/2/3/4/.../Infinity/index.php.
);

//Import plugins:
include_once(INCLUDE_DIR.'/inc/plugins/plugin_http_back.php');//plugin_http_back.
include_once(INCLUDE_DIR.'/inc/plugins/plugin_uri.php');//plugin_http_back.
//include_once(INCLUDE_DIR.'/inc/plugins/plugin_img_rsz.php');//plugin_img_rsz.

//Fix .htaccess file if not exist:
if(!file_exists("./.htaccess")){
	copy(INCLUDE_DIR."/inc/dist/htaccess.txt",INCLUDE_DIR."/.htaccess");
}

//Check uri:
$uri=rtrim(dirname($_SERVER["SCRIPT_NAME"]),'/');
$uri='/'.trim(str_replace($uri,'',$_SERVER['REQUEST_URI']),'/');
$uri=urldecode($uri);

//Display web page:
foreach($pages as $action=>$page){
    	if(preg_match('~^'.$page.'$~i',$uri,$params)){
    	        header("Content-type:".explode(":",$action)[1]);
    	        if (explode(":",$action)[1]=="text/html"){include_once(INCLUDE_DIR."/inc/plugins/plugin_html.php");}
    	        //include page path:
		include_once(explode(":",$action)[0]);
		if (explode(":",$action)[1]=="text/html"){echo $end_page;}
		exit;
    	}
}

//page not fund:
$error_n="404";
//include_once(INCLUDE_DIR."/inc/pages/errors/errors.php");
header("Location: ".$back."404");
exit();
/*E*/
?>
