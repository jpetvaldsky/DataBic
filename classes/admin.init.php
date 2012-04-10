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
				$output .= $navigation;
				/*
				if ($res != '')
					$output .= Templates::Message($res);				
					*/
				if (strLen($name) > 0)
					$moduls->autoOrder($name);
				
				
				$modulNavigation = $moduls->listModulNavigation($name);
				$modulContent = $moduls->listModul($name,$header,$headerLink);
				$output .= Templates::mainContainerTemplate($header,$headerLink,$modulContent,$modulNavigation);
				
				echo $output;
				break;
			case "webmap":	
				$moduls = new Modules($usr->superadmin);
				$moduls->search = $s;
				$sitemap = new Sitemap();
				$res = $sitemap->doAction();
				$output .= $navigation;
				
				$output .= Templates::Message($res);				
				$modulNavigation = '';
				$modulContent = $sitemap->init($modulNavigation);
				
/*
				$headerLink = "?type=webmap";
				$header = Templates::topHeader($GLOBALS["menu"]["MAP"],$headerLink);				
				$headerLink .= "&amp;do=new";	
*/
				$header = '';				
				$output .= Templates::mainContainerTemplate($header,$headerLink,$modulContent,$modulNavigation);
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
				
				//$FolderData = Templates::FolderListHeader($type,$fId).$folderStructure;
				$FolderData = Templates::BreadcrumbNav($folders->treeItems($type,$fId));
				$headerLink = "?type=".$type;
				if ($type=="images"){
					$header = Templates::topHeader($GLOBALS["menu"]["IMAGES"],'');				
					$FolderData .= Images::ListByFolder($fId);			
				} elseif ($type=="videos"){ 
					$header = Templates::topHeader($GLOBALS["menu"]["VIDEOS"],'');
					$FolderData .= Videos::ListByFolder($fId);
				} elseif ($type=="files"){ 
					$header = Templates::topHeader($GLOBALS["menu"]["FILES"],'');
					$FolderData .= Files::ListByFolder($fId);
				} elseif ($type=="urls"){ 
					$header = Templates::topHeader($GLOBALS["menu"]["URLS"],'');
					$FolderData .= Urls::ListByFolder($fId);		
				}
				
				$headerLink .= "&amp;action=list&amp;folder_id=".$fId."&amp;do=new";								
				//$output .= Templates::FolderContent($FolderData);
				$output .= Templates::mainContainerTemplate($header,$headerLink,$FolderData,$folderStructure);

				echo $output;
				break;
			case "languages":
				$moduls = new Modules(true);
				$moduls->search = $s;
				require_once ("classes/admin.languages.php");
				$lang = new Languages();
				$res = $lang->doAction();
				$output .= $navigation;
				$output .= Templates::Message($res);				

				$headerLink = "?type=languages";
				$header = Templates::topHeader($GLOBALS["menu"]["LANG"],$headerLink);				
				$headerLink .= "&amp;do=new";				
				$output .= Templates::mainContainerTemplate($header,$headerLink,$lang->init(),'');
				
				echo $output;
				break;
			case "text":				
				$txt = new Text();
				$res = $txt->doAction();
				$output .= $navigation;
				$output .= Templates::Message($res);
				
				$headerLink = "?type=text";				
				$header = Templates::topHeader($GLOBALS["menu"]["TXT"],$headerLink);								
				$headerLink .= "&amp;do=new";
				$output .= Templates::mainContainerTemplate($header,$headerLink,$txt->init(),'');
				
				echo $output;
				break;
			case "search_fill":
				$res = $s->updateSearchContent();
				$output .= $navigation;
				$output .= Templates::Message($res);	
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
