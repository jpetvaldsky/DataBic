<?
$linked = true;
require_once ("classes/admin.init.php");

$a = new Admin();
$a->init();

$usr = new Users();

$usr->checkLogin();

$onlyData = true;

if ($usr->logged){
	$type = $_GET["type"];
	if (isSet($_POST["type"])) $type= $_POST["type"];
	$subId = $_GET["subLink"];
	if (isSet($_POST["subLink"])) $subId= $_POST["subLink"];
	if (strlen($subId) > 0) $subId = "_ID_".$subId;
	$output = "";
	$headline = '';
	switch ($type){
		case "images":
			$headline = $GLOBALS['msg']["IMAGE_REL"];
			if (!isSet($_GET["branchId"])){
				if ($_GET["do"] == "preview"){
					$onlyData = true;
					$link = $_GET["data"];
					if (strlen($link) > 0 && $link !='')
					{
						$link = str_replace("xx",",",$link);
						$link = str_replace("x","",$link);
						$iArr = explode(",",$link);
						$iData = '';
						foreach ($iArr as $i){
							if ($i+0 > 0)
							{
								$iPath = Images::AdminThumbById($i);
								$iData .= Templates::AdminPreviewImage($i,$iPath,$subId,$_GET["modul"],$_GET["uniq_id"]);
							}
						}
						if ($iData != '')
						{
							$output .= '<ul class="thumbnails">'.$iData.'</ul>';
						}
					}	
					if ($output == '')
					{
						$output = '<h6>'.$GLOBALS['msg']['REL_EMPTY'].'</h6>';
					}				
				} elseif ($_GET["do"] == "save_desc"){
					$output = "DESC SAVED ".$_GET["type"]." ".$_GET["modul"]." ".$_GET["uId"];
				} else {
					$onlyData = false;
					require_once ("classes/admin.folders.php");
					$folders = new Folders();
					$folderList = $folders->buildStructure($type,$subId,true);
					$fId = 0;
					if (isSet($_GET["folder_id"]))$fId = $_GET["folder_id"];
					if (isSet($_POST["folder_id"])) $fId = $_POST["folder_id"];
					//$FolderData = Templates::FolderListHeader($type,$fId,true);
					$imageData .= Images::ListByFolder($fId,$subId,true);			
					//$output .= Templates::FolderContent($imageData);
					$output = '
						<div class="container-fluid">
							<div class="row-fluid">
					      <div class="span4">'.$folderList.'</div>
					      <div class="span8" id="linkedBranchData">'.$imageData.'</div>
					    </div>
					  </div>';
					$saveLink = 'javascript:linkedSave(\''.$type.$subId.'\');';
				}
			} else {
					// UPDATE FOLDER CONTENT LIST
					$onlyData = true;
					$output = Images::ListByFolder($_GET["branchId"],$subId,true);			
			}
			break;
		case "files":
			$headline = $GLOBALS['msg']["FILES_REL"];
			if (!isSet($_GET["branchId"])){
				if ($_GET["do"] == "preview"){
					$link = $_GET["data"];
					if (strlen($link) > 0 && $link !='')
					{
						$link = str_replace("xx",",",$link);
						$link = str_replace("x","",$link);
						$iArr = explode(",",$link);
						$tableData = '';
						foreach ($iArr as $i){
							if ($i+0 > 0)
							{
								$tableData .= Files::AdminThumbById($i,$subId);
							}
						}
						
						if ($tableData != '')
						{
							$output = Templates::FileList($tableData,true);
						}
					
					}	
					if ($output == '')
					{
						$output = '<h6>'.$GLOBALS['msg']['REL_EMPTY'].'</h6>';
					}	
					
				} else {
					$onlyData = false;
					require_once ("classes/admin.folders.php");
					$folders = new Folders();
					$folderList = $folders->buildStructure($type,$subId,true);
					$fId = 0;
					if (isSet($_GET["folder_id"]))$fId = $_GET["folder_id"];
					if (isSet($_POST["folder_id"])) $fId = $_POST["folder_id"];
					$filesData .= Files::ListByFolder($fId,$subId,true);			
					
					$output = '
						<div class="container-fluid">
							<div class="row-fluid">
					      <div class="span4">'.$folderList.'</div>
					      <div class="span8" id="linkedBranchData">'.$filesData.'</div>
					    </div>
					  </div>';
					$saveLink = 'javascript:linkedSave(\''.$type.$subId.'\');';
					
				}
			} else {
					// UPDATE FOLDER CONTENT LIST
					$output = Files::ListByFolder($_GET["branchId"],$subId,true);			
			}
			break;
/*			
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
*/				
		default:	
			$moduls = new Modules(1);
			$modulName = "Modul_".$type;
			$modul = new $modulName;
			$headline = str_replace("%MODUL_NAME%",$modul->name,$GLOBALS['msg']["RELATED_DATA"]);
			if ($_GET["do"] == "preview"){
				$link = $_GET["data"];
				$link = str_replace("xx",",",$link);
				$link = str_replace("x","",$link);
				$iArr = explode(",",$link);
				$count=0;
				$tableData = '';
				foreach ($iArr as $i){
					if ($i+0 > 0)
					{
						$count++;
						$name = "";
						$row = $moduls->ModulDataByRowId($type,$i);
						if (is_array($modul->selectId)){
							foreach ($modul->selectId as $index) $name .= $row[$index]." ";
						} else {
							$name = $row[4];
						}
						$tableData .= Templates::AdminPreviewModulItem($type,$i,$name);
					}
				}
				if ($tableData != '')
				{
					$output = Templates::ModulPreviewTableList($tableData,true);
				}
				if ($output == '')
				{
					$output = '<h6>'.$GLOBALS['msg']['REL_EMPTY'].'</h6>';
				}
				

			} else {
				$onlyData = false;
				$output .= Templates::ModulList($moduls->modulContent($type,$modul->orderBy,true));
				$saveLink = "javascript:linkedSave(\''.$type.'\');";
				//$output .= '<div class="clear"></div><div class="linkBt"><a href="javascript:linkedSave(\''.$type.'\');" title="'.$GLOBALS["msg"]["SAVE"].'" class="linkBtSave">'.$GLOBALS["msg"]["SAVE"].'</a><a href="javascript:maskClose();" title="'.$GLOBALS["msg"]["CLOSE"].'" class="linkBtClose">'.$GLOBALS["msg"]["CLOSE"].'</a></div>';
			}
			break;
	}
	
	
} else {
	$headline = "ERROR";
	$output = "unauthorized access";
}

if ($onlyData){
	echo $output;
}
else
{
	echo '<div class="modal-header"><a class="close" data-dismiss="modal">×</a><h3>'.$headline.'</h3></div><div class="modal-body"><p>'.$output.'</p></div><div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">'.$GLOBALS['msg']['CLOSE'].'</a><a href="'.$saveLink.'" class="btn btn-primary">'.$GLOBALS['msg']['SAVE'].'</a></div>';
}