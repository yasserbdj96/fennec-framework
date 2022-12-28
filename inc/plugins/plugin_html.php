<?php
	//To confirm that the file was not accessed from another place:
	if(!defined("root_document")){
		exit(header('Location: 404'));
	}
	
	$n3_space="\n			";
	
	$page_json=explode(":",$action)[0];
	$page_json=str_replace(".php",".json",$page_json);
	
	if (file_exists($page_json)){
	    $page_settings=json_decode(file_get_contents($page_json),true);
	    
	    $title=$n3_space."include('".base64_encode($page_settings["page_settings"]["title"].":title")."');";
	    $css=$page_settings["page_settings"]["css"];
	    $js=$page_settings["page_settings"]["js"];
	    
	    //css
	    $csss="";
	    foreach($css as $css_file){
	        $csss.=$n3_space."include('".base64_encode($back.$css_file.":css")."');";
	    }
	    
	    //js
	    $jss="";
	    foreach($js as $js_file){
	        $jss.=$n3_space."include('".base64_encode($back.$js_file.":js")."');";
	    }
	} else {
    	$csss="";
    	$jss="";
    	$title="";
    }
$end_page="\n	</body>\n</html>";
?>
<!DOCTYPE html>
<html lang="en" >
	<head>
		<title> </title>
		<meta charset="UTF-8">
		<!--meta id="viewport" name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover"-->
		<meta id="viewport" name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, viewport-fit=cover">
		<meta name="robots" content="noimageindex, noarchive">
		<meta content='yes' name='apple-mobile-web-app-capable'>
		<meta content='yes' name='mobile-web-app-capable'>
		<meta name="theme-color" media="(prefers-color-scheme: light)" content="#37abee">
		<meta name="theme-color" media="(prefers-color-scheme: dark)"  content="#202124">
		<meta name="description" content="Nothing" />
		<link rel="icon" type="image/ico" href="./img-logo.png:25x25">
		<link rel="apple-touch-icon" href="./img-logo.png:25x25" />
		<link rel="manifest" href="./manifest.webmanifest">
		<!--script src="./serviceworker.js"></script-->
		<script src="<?php echo $back;?>include.js"></script>
		<script>
			// icon:
		    include("<?php echo base64_encode($back.'logo.png:25x25:ico');?>");
			// font:
		    include("<?php echo base64_encode($back.'arial.css:css');?>");
			// style:
			include("<?php echo base64_encode($back.'css.css:css');?>");
			// message:
		    include("<?php echo base64_encode($back.'msg.js:js');?>");
			// serviceworker:
		    include("<?php echo base64_encode($back.'serviceworker.js:js');?>");
			// this page js & css:<?php echo $title.$csss.$jss."\n";?>
		</script>
	</head>
	<body>
