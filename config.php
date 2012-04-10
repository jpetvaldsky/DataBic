<?
	$GLOBALS["lang"] = "cs";
	$GLOBALS["app_name"] = "DataBic-V3";
	$GLOBALS["db_name"] = "db_absjets";
	$GLOBALS["modul_folder"] = "classes/Moduls/";
	$GLOBALS["tpl_folder"] = "templates/";

	$GLOBALS["images_folder"] = "Library/Images/";
	$GLOBALS["videos_folder"] = "Library/Videos/";
	$GLOBALS["files_folder"] = "Library/Files/";
	switch ($_SERVER['HTTP_HOST']) {
			case "absjets.sortof.info":
				$GLOBALS["db_name"] = "db_absjets";
				$GLOBALS["host"] = "localhost";
				$GLOBALS["user"] = "db_absjets";
				$GLOBALS["pw"] = "59ZeP72CYKuE";
				break;
			default:
				$GLOBALS["db_name"] = "db_absjets";
				$GLOBALS["host"] = "localhost";
				$GLOBALS["user"] = "root";
				$GLOBALS["pw"] = "";
				break;			
		}
		
	//	ADMIN USERS
	//sona.stejskalova
	//BEKKeVP8
?>