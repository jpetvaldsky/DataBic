<?
//	error_reporting(E_ALL & ~E_NOTICE);
	include "config.php";
	require_once ("classes/admin.images.php");
	require_once ("classes/admin.templates.php");
	require_once ("classes/functions.php");

	$global_dbh = mysql_connect($GLOBALS["host"], $GLOBALS["user"], $GLOBALS["pw"]);
	if ($global_dbh){
		if (!mysql_select_db($GLOBALS["db_name"], $global_dbh)){
			echo "DB Select Error";			
		}
	} else {
		echo "DB Connection Error";
		exit;
	}
	mysql_query("SET CHARACTER SET utf8");

	$idImages = str_replace("x","",str_replace("xx",",",$_GET['ids']));
	$imgArray = explode(",",$idImages);
	$output = '';
	foreach ($imgArray as $id){
		$img = Images::ImageById($id);
		if ($img["filename"] != ''){
			$output .= Templates::ImageDropBox($GLOBALS["images_folder"]."Cache_Admin/".$img["filename"],$img["title"],$img["id"],$img["folder_id"],0);			
		}
	}
	
	include_once("templates/small_header.tpl");
	echo '<div id="imagePicker">';
	echo $output;
	echo '<div class="clear">&nbsp;</div></div>';
	include_once("templates/footer.tpl"); 
	
?>