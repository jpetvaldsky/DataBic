<?
$linked = true;
require_once ("classes/admin.init.php");

$a = new Admin();
$a->init();

$usr = new Users();

$usr->checkLogin();


if ($usr->logged){
	$type = $_GET["type"];
	if (isSet($_POST["type"])) $type= $_POST["type"];
	$subId = $_GET["subLink"];
	if (isSet($_POST["subLink"])) $subId= $_POST["subLink"];
	if (strlen($subId) > 0) $subId = "_ID_".$subId;
	$output = "";
	switch ($type){
		case "images":
			if (!isSet($_GET["branchId"])){
				if ($_GET["do"] == "preview"){
					$link = $_GET["data"];
					$link = str_replace("xx",",",$link);
					$link = str_replace("x","",$link);
					$iArr = explode(",",$link);
					foreach ($iArr as $i){
						$iPath = Images::AdminThumbById($i);
						$output .= Templates::AdminPreviewImage($i,$iPath,$subId,$_GET["modul"],$_GET["uniq_id"]);
					}
					$output .= '<div class="clear"></div>';
				} elseif ($_GET["do"] == "save_desc"){
					$output = "DESC SAVED ".$_GET["type"]." ".$_GET["modul"]." ".$_GET["uId"];

				} else {
					require_once ("classes/admin.folders.php");
					$folders = new Folders();
					$output = $folders->buildStructure($type,$subId,true);
					$fId = 0;
					if (isSet($_GET["folder_id"]))$fId = $_GET["folder_id"];
					if (isSet($_POST["folder_id"])) $fId = $_POST["folder_id"];
					//$FolderData = Templates::FolderListHeader($type,$fId,true);
					$FolderData .= Images::ListByFolder($fId,$subId,true);			
					$output .= Templates::FolderContent($FolderData);

					$output .= '<div class="clear"></div><div class="linkBt"><a href="javascript:linkedSave(\''.$type.$subId.'\');" title="'.$GLOBALS["msg"]["SAVE"].'" class="linkBtSave">'.$GLOBALS["msg"]["SAVE"].'</a><a href="javascript:maskClose();" title="'.$GLOBALS["msg"]["CLOSE"].'" class="linkBtClose">'.$GLOBALS["msg"]["CLOSE"].'</a></div>';
				}
			} else {
					$output = Images::ListByFolder($_GET["branchId"],$subId,true);			
			}
			break;
		case "videos":
			if (!isSet($_GET["branchId"])){
				if ($_GET["do"] == "preview"){
					$link = $_GET["data"];
					$link = str_replace("xx",",",$link);
					$link = str_replace("x","",$link);
					$iArr = explode(",",$link);
					foreach ($iArr as $i){
						$iPath = Videos::AdminThumbById($i);
						$output .= Templates::AdminPreviewVideo($i,$iPath,$subId,$_GET["modul"],$_GET["uniq_id"]);
					}
					$output .= '<div class="clear"></div>';
				} elseif ($_GET["do"] == "save_desc"){
					$output = "DESC SAVED ".$_GET["type"]." ".$_GET["modul"]." ".$_GET["uId"];

				} else {
					require_once ("classes/admin.folders.php");
					$folders = new Folders();
					$output = $folders->buildStructure($type,$subId,true);
					$fId = 0;
					if (isSet($_GET["folder_id"]))$fId = $_GET["folder_id"];
					if (isSet($_POST["folder_id"])) $fId = $_POST["folder_id"];
					$FolderData .= Videos::ListByFolder($fId,$subId,true);			
					$output .= Templates::FolderContent($FolderData);

					$output .= '<div class="clear"></div><div class="linkBt"><a href="javascript:linkedSave(\''.$type.$subId.'\');" title="'.$GLOBALS["msg"]["SAVE"].'" class="linkBtSave">'.$GLOBALS["msg"]["SAVE"].'</a><a href="javascript:maskClose();" title="'.$GLOBALS["msg"]["CLOSE"].'" class="linkBtClose">'.$GLOBALS["msg"]["CLOSE"].'</a></div>';
				}
			} else {
					$output = Videos::ListByFolder($_GET["branchId"],$subId,true);			
			}
			break;
		case "files":
			if (!isSet($_GET["branchId"])){
				if ($_GET["do"] == "preview"){
					$link = $_GET["data"];
					$link = str_replace("xx",",",$link);
					$link = str_replace("x","",$link);
					$iArr = explode(",",$link);
					foreach ($iArr as $i){
						$output .= Files::AdminThumbById($i,$subId);
					}
					$output .= '<div class="clear"></div>';
				} else {
					require_once ("classes/admin.folders.php");
					$folders = new Folders();
					$output = $folders->buildStructure($type,$subId,true);
					$fId = 0;
					if (isSet($_GET["folder_id"]))$fId = $_GET["folder_id"];
					if (isSet($_POST["folder_id"])) $fId = $_POST["folder_id"];
					$FolderData .= Files::ListByFolder($fId,$subId,true);			
					$output .= Templates::FolderContent($FolderData);

					$output .= '<div class="clear"></div><div class="linkBt"><a href="javascript:linkedSave(\''.$type.$subId.'\');" title="'.$GLOBALS["msg"]["SAVE"].'" class="linkBtSave">'.$GLOBALS["msg"]["SAVE"].'</a><a href="javascript:maskClose();" title="'.$GLOBALS["msg"]["CLOSE"].'" class="linkBtClose">'.$GLOBALS["msg"]["CLOSE"].'</a></div>';
				}
			} else {
					$output = Files::ListByFolder($_GET["branchId"],$subId,true);			
			}
			break;
		case "urls":
			if (!isSet($_GET["branchId"])){
				if ($_GET["do"] == "preview"){
					$link = $_GET["data"];
					$link = str_replace("xx",",",$link);
					$link = str_replace("x","",$link);
					$iArr = explode(",",$link);
					foreach ($iArr as $i){
						$output .= Urls::AdminThumbById($i,$subId);
					}
					$output .= '<div class="clear"></div>';				
				} else {
					require_once ("classes/admin.folders.php");
					$folders = new Folders();
					$output = $folders->buildStructure($type,$subId,true);
					$fId = 0;
					if (isSet($_GET["folder_id"]))$fId = $_GET["folder_id"];
					if (isSet($_POST["folder_id"])) $fId = $_POST["folder_id"];
					$FolderData .= Urls::ListByFolder($fId,$subId,true);			
					$output .= Templates::FolderContent($FolderData);

					$output .= '<div class="clear"></div><div class="linkBt"><a href="javascript:linkedSave(\''.$type.$subId.'\');" title="'.$GLOBALS["msg"]["SAVE"].'" class="linkBtSave">'.$GLOBALS["msg"]["SAVE"].'</a><a href="javascript:maskClose();" title="'.$GLOBALS["msg"]["CLOSE"].'" class="linkBtClose">'.$GLOBALS["msg"]["CLOSE"].'</a></div>';
				}
			} else {
					$output = Urls::ListByFolder($_GET["branchId"],$subId,true);			
			}
			break;
		default:	
			$moduls = new Modules(1);
			$modulName = "Modul_".$type;
			$modul = new $modulName;
			if ($_GET["do"] == "preview"){
				$link = $_GET["data"];
				$link = str_replace("xx",",",$link);
				$link = str_replace("x","",$link);
				$iArr = explode(",",$link);
				$count=0;
				foreach ($iArr as $i){
					$count++;
					$name = "";
					$row = $moduls->ModulDataByRowId($type,$i);
					if (is_array($modul->selectId)){
						foreach ($modul->selectId as $index) $name .= $row[$index]." ";
					} else {
						$name = $row[4];
					}
					$output .= Templates::AdminPreviewModulItem($type,$i,$name);
				}
			} else {
				$output .= Templates::ModulList($moduls->modulContent($type,$modul->orderBy,true));
				$output .= '<div class="clear"></div><div class="linkBt"><a href="javascript:linkedSave(\''.$type.'\');" title="'.$GLOBALS["msg"]["SAVE"].'" class="linkBtSave">'.$GLOBALS["msg"]["SAVE"].'</a><a href="javascript:maskClose();" title="'.$GLOBALS["msg"]["CLOSE"].'" class="linkBtClose">'.$GLOBALS["msg"]["CLOSE"].'</a></div>';
			}
			break;
	}
	echo $output;
} else {
	echo "unauthorized access";
}