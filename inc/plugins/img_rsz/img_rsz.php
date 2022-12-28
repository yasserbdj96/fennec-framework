<?php
include_once('plugin_img_rsz.php');

//check img code data:
$img=explode('/',$_SERVER["REQUEST_URI"]);
$img=$img[count($img)-1];

if(substr($img,0,4)=="img-"){
    $img=str_replace("img-","",$img);
    list($img,$xy)=explode(':',$img);
    list($x,$y)=explode('x',$xy);
    Img::resizeImage("./inc/img/".$img,null,array('x'=>$x,'y'=>$y));
}
?>