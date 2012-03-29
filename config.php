<?
	$GLOBALS["lang"] = "en";
	$GLOBALS["app_name"] = "Databic";
	$GLOBALS["db_name"] = "db_databic";
	$GLOBALS["modul_folder"] = "classes/Moduls/";
	$GLOBALS["tpl_folder"] = "templates/";

	$GLOBALS["images_folder"] = "Library/Images/";
	$GLOBALS["videos_folder"] = "Library/Videos/";
	$GLOBALS["files_folder"] = "Library/Files/";
	switch ($_SERVER['HTTP_HOST']) {
			default:
				$GLOBALS["db_name"] = "db_databic";
				$GLOBALS["host"] = "localhost";
				$GLOBALS["user"] = "root";
				$GLOBALS["pw"] = "";
				break;
		}
?>