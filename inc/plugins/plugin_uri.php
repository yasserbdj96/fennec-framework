<?php
/*S*/
//Pages:
//'picture'   => "/picture/(?'text'[^/]+)/(?'id'\d+)",    // '/picture/some-text/51'
//'album'     => "/album/(?'album'[\w\-]+)",              // '/album/album-slug'
//'category'  => "/category/(?'category'[\w\-]+)",        // '/category/category-slug'
//'page'      => "/page/(?'page'about|contact)",          // '/page/about', '/page/contact'
//'post'      => "/(?'post'[\w\-]+)",                     // '/post-slug'
//'home'      => "/"                                      // '/'

//Pages:
/*$pages+=array(
	INCLUDE_DIR.'/inc/pages/home/home.php:text/html'=>"/(?'page'index.php||home)",
	INCLUDE_DIR.'/inc/pages/home/home.js:application/javascript'=>"/(?'page'home.js)",
	INCLUDE_DIR.'/inc/pages/home/btc.js:application/javascript'=>"/(?'page'btc.js)",
	INCLUDE_DIR.'/inc/pages/home/home.css:text/css'=>"/(?'page'home.css)",
);*/

namespace plugins;
class uri{
	//public static function get_uris(){
	public static function getDirContents($dir,$filter='',&$results=array()){
    		$files=scandir($dir);
    		foreach($files as $key=>$value){
        		$path=realpath($dir.DIRECTORY_SEPARATOR.$value); 
        		if(!is_dir($path)){
            			if(empty($filter)||preg_match($filter,$path))$results[]=$path;
        		}elseif($value!="." && $value!=".."){
            			uri::getDirContents($path,$filter,$results);
        		}
    		}
    		return $results;
	}

        public static function getUriContents($INCLUDE_DIR){
		// Regex Call: List json files only
		$pages=array();
		$jsons=uri::getDirContents($INCLUDE_DIR,'/\.json$/');
		foreach ($jsons as $json) {
    			$path=explode("/",$json);
    			$x=count($path);
    			$path=array_slice($path, 0,$x-1);
    			$path_r="";
    			foreach ($path as $f){
        			$path_r.=$f."/";
    			}
    
    			$string = file_get_contents($json);
    			$json_a = json_decode($string,true);

	    		foreach ($json_a as $key => $value){
	        		if ($key=="pages"){
	            			foreach ($value as $page_info => $page){
	                			$pages+=array($path_r.$page_info=>$page);
	            			}
	        		}
	    		}
		}
		return $pages;
	}
}

//
$pages=uri::getUriContents(INCLUDE_DIR);
/*E*/
?>