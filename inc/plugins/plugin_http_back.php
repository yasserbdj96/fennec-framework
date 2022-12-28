<?php
/*S*/
//namespace:
namespace plugins;

//To confirm that the file was not accessed from another place:
if(!defined("root_document")){
	exit(header('Location: 404'));
}

//back:
class back{
	//function for fix template:
	public static function http_back($site_folder_patch){
		$site_folder=explode("/",$_SERVER['REQUEST_URI']);
		$site_folder_uri="/";
		for($i=1;$i<=base64_decode($site_folder_patch);$i++){
			$site_folder_uri=$site_folder_uri.$site_folder[$i]."/";
		}
		$actual_link="$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$new_actual_link=str_replace(array("$_SERVER[SERVER_NAME]$site_folder_uri"),array(''),$actual_link);
		$substr_count_new_actual_link=substr_count($new_actual_link, '/');
		$back="";
		for($i=1;$i<=$substr_count_new_actual_link;$i++){
			$back=$back."../";
		}
		return $back;
	}
}
//Call the class & make the value:
//$back=plugins\back::http_back($script_settings_info["site_folder_patch"]);
$back=back::http_back($script_settings_info["site_folder_patch"]);
//$back="./";
/*E*/
?>