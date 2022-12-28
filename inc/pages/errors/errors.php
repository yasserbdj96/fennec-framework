<?php
	//To confirm that the file was not accessed from another place.
	if(!defined("root_document")){
		header("HTTP/1.0 404 Not found!");
		exit(header('Location: 404'));
	}
	
	$errors=json_decode(file_get_contents(INCLUDE_DIR."/inc/pages/errors/errors.json"),true);
	
	if(!isset($error_n)){
	    $error_n="404";
	    header("HTTP/1.0 404 Not found!");
	}
	
	header($errors["e$error_n"]['header']);
?>
		<div id="main">
			<div class="fof">
				<h1><?php echo $errors["e$error_n"]['msg'];?></h1>
			</div>
		</div>
