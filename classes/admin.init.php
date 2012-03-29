<?
class Admin{
	var $db;
	var $wiki;
	var $logged;
	var $user;
	var $appName;
	var $locked;


	function Admin(){
		$this->appName = $GLOBALS["app_name"];
		$this->dbName = $GLOBALS["db_name"];
		$this->host = $GLOBALS["host"];
		$this->user = $GLOBALS["user"];
		$this->pw = $GLOBALS["pw"];
	}

	function init(){
		$this->global_dbh = mysql_connect($this->host, $this->user, $this->pw);
		if ($this->global_dbh){
			if (!mysql_select_db($this->dbName, $this->global_dbh)){
				echo "DB Select Error";			
			}
		} else {
			echo "DB Connection Error";
			exit;
		}
		mysql_query("SET CHARACTER SET utf8");
		require_once ("classes/admin.text.".$GLOBALS["lang"].".php");
		//require_once ("classes/Text/Wiki.php");
		require_once ("classes/admin.users.php");
		require_once ("classes/admin.modul.php");
		require_once ("classes/admin.sitemap.php");
		require_once ("classes/admin.templates.php");
		require_once ("classes/admin.layout.php");
		require_once ("classes/admin.forms.php");
		require_once ("classes/admin.images.php");
		require_once ("classes/admin.videos.php");
		require_once ("classes/admin.files.php");
		require_once ("classes/admin.urls.php");
		require_once ("classes/admin.search.php");
		require_once ("classes/admin.text.php");
		require_once("classes/functions.php");		
		
	}
}
if (!$linked){
	$detail = false; $service=false;
	$a = new Admin();
	$a->init();
	mysql_query("SET NAMES 'utf-8'");
	mysql_query("SET character_set_client=utf8");
	mysql_query("SET character_set_connection=utf8");
	mysql_query("SET character_set_results=utf8");

	$usr = new Users();
	//$wiki = new Text_Wiki();
	$s = new Search();

	if ($_GET["type"] == "logout") $usr->logout();
	else $usr->checkLogin();
	//$usr->logged = true;


	switch ($_POST["do"]){
		default:
			break;
	}

	switch ($_GET["do"]){
		default:
			break;
	}


	include $GLOBALS["tpl_folder"]."header.tpl";
	include $GLOBALS["tpl_folder"]."login_head.tpl";

	$type = $_GET["type"];
	if (isSet($_POST["type"])) $type= $_POST["type"];

	if ($usr->logged){
		Images::FtpUpload();
		Files::FtpUpload();
		$navigation = Layout::buildNavigation();
		

		switch ($type){
			case "modul":
				$moduls = new Modules($usr->superadmin);
				$moduls->search = $s;
				$name = $_GET["name"];
				if (isSet($_POST["name"])) $name= $_POST["name"];
				$res = $moduls->doAction();
				$output = Templates::Message($res);
				$output .= $navigation;
				if (strLen($name) > 0)
					$moduls->autoOrder($name);
				$output .= $moduls->listModul($name);
				echo Templates::DataContainer($output);				
				echo "</div>";
				echo Forms::ContentDisable();
				break;
			case "webmap":	
				$moduls = new Modules($usr->superadmin);
				$moduls->search = $s;
				$sitemap = new Sitemap();
				$res = $sitemap->doAction();
				$output = Templates::Message($res);
				$output .= $navigation;
				$output .= $sitemap->init();
				echo $output;
				break;
			case "images":
			case "files":
			case "videos":
			case "urls":
				require_once ("classes/admin.folders.php");
				$folders = new Folders();
				$output .= $navigation;
				$folderStructure = $folders->buildStructure($type);
				$fId = 0;
				if (isSet($_GET["folder_id"]))$fId = $_GET["folder_id"];
				if (isSet($_POST["folder_id"])) $fId = $_POST["folder_id"];
				$FolderData = Templates::FolderListHeader($type,$fId).$folderStructure;
				if ($type=="images"){
					$FolderData .= Images::ListByFolder($fId);			
				} elseif ($type=="videos"){ 
					$FolderData .= Videos::ListByFolder($fId);
				} elseif ($type=="files"){ 
					$FolderData .= Files::ListByFolder($fId);
				} elseif ($type=="urls"){ 
					$FolderData .= Urls::ListByFolder($fId);		
				}
				$output .= Templates::FolderContent($FolderData);
				echo $output;
				echo "</div>";
				break;
			case "languages":
				$moduls = new Modules(true);
				$moduls->search = $s;
				require_once ("classes/admin.languages.php");
				$lang = new Languages();
				$res = $lang->doAction();
				$output = Templates::Message($res);
				$output .= $navigation;
				$output .= $lang->init();
				echo $output;
				break;
			case "text":				
				$txt = new Text();
				$res = $txt->doAction();
				$output = Templates::Message($res);
				$output .= $navigation;
				$output .= $txt->init();
				echo $output;
				break;
			case "search_fill":
				$res = $s->updateSearchContent();
				$output = Templates::Message($res);
				$output .= $navigation;
				echo $output;
				break;
			default:
				echo $navigation;
				echo "</div>";
				break;
		}
	}

	include $GLOBALS["tpl_folder"]."footer.tpl";
}
