<?

class Sitemap extends Admin {

	function init(&$sitemapTree){
		$this->nodeId = -1;
		if (isSet($_GET["nodeId"])) $this->nodeId = $_GET["nodeId"]+0;
		if (isSet($_POST["node_id"])) $this->nodeId = $_POST["node_id"]+0;
		$output = '';//Templates::SitemapHeader();
		$sitemapTree = $this->treeMenu();

		if ($this->nodeId != -1)
			$output .= $this->nodeContent();
		return $output;
	}

	function nodeContent(){
		if ($_GET["do"] != "delete"){
			$node = $this->getById($this->nodeId);
			$nodeArray = explode(",",$node["moduls"]);
			$output = "";
				foreach($nodeArray as $modulNum){
					$modulName = "Modul_".$modulNum;
					$modul = new $modulName;
					$ico = "Modul/".$modulNum.".gif";
					if (!file_exists("i/icon/".$ico)) $ico = "system-modul-default.gif";
					$output .= Templates::ModulItem($ico,$modul->name,"list",$modulNum,$this->nodeId);
				}
			$output = Templates::ModulMenu($output);
			$name = $_GET["name"];
			if (isSet($_POST["name"])) $name= $_POST["name"];
			if ($name != NULL){
				$action = $_GET["action"];
				if (isSet($_POST["action"])) $action = $_POST["action"];
				$modulName = "Modul_".$name;
				$modul = new $modulName;
				$output .= Templates::ModulListHeader($modul->name,$name,$this->nodeId);
				switch ($action){
					case "new":
						$output .= $GLOBALS["moduls"]->ModulDataForm($name,-1,$this->nodeId);
						break;
					case "edit":
						$output .= $GLOBALS["moduls"]->ModulDataForm($name,$_GET["id"],$this->nodeId);
						break;
					case "list":
					default:
						$output .= Templates::ModulList($GLOBALS["moduls"]->listModulData($name,$modul->orderBy,false,$this->nodeId));
						break;
				}
			}
		}
		return Templates::nodeContent($output);
	}

	function doAction(){
		$res = "&nbsp;";
		if (isSet($_POST["post_action"])){
			switch ($_POST["post_action"]){
				case "insert":
					$res = $this->insert();
					break;
				case "update":
					$res = $this->update();
					break;
				case "minsert":
					$res = $GLOBALS["moduls"]->insert();
					break;
				case "mupdate":
					$res = $GLOBALS["moduls"]->update();
					break;
				case "add_modul":
					$res = $this->addModul();
					break;
				default:
					//$res = $_POST["post_action"];
					break;
			}
		}
		if (isSet($_GET["do"])){
			switch ($_GET["do"]){
				case "delete":
					$res = $this->deleteRecord();
					break;
				case "remove_modul":
					$res = $this->removeModul();
				case "mactive":
					$res = $GLOBALS["moduls"]->activeRecord();
					break;
				case "mdelete":
					$res = $GLOBALS["moduls"]->deleteRecord();
					break;
				case "mpush":
					$res = $GLOBALS["moduls"]->reorderRecords();
					break;
				default:
					//$res = $_GET["do"];
					break;
			}
		}
		return $res;
	}

	function getById($id){
		$query = "SELECT * FROM `wa_sitemap` WHERE `id`='".$id."'";
		if ($res=mysql_query($query)){
			return mysql_fetch_array($res);
		}
		return NULL;
	}

	function addModul(){
		if ($_POST["modulId"] != "0"){
			$node = $this->getById($_POST["node_id"]);
			$nodeArray = split(",",$node["moduls"]);
			$add = true;
			foreach ($nodeArray as $mId){
				if ($_POST["modulId"] == $mId){
					$add = false;
					break;
				}
			}
			if ($add){
				array_push($nodeArray,$_POST["modulId"]);
				$nodeStr = "";
				foreach ($nodeArray as $mId){
					if ($nodeStr != "") $nodeStr .= ",";
					$nodeStr .= $mId;
				}
				$query = "UPDATE `wa_sitemap` SET `moduls`='".$nodeStr."' WHERE `id`='".$_POST["node_id"]."'";
				if ($res=mysql_query($query)){
					return $GLOBALS["msg"]["MADD-OK"];
				} else {
					return $GLOBALS["msg"]["ERR"];	
				}
			}
		}
		return $result;
	}

	function removeModul(){
		$node = $this->getById($_POST["nodeId"]);
		return $result;
	}

	function deleteRecord(){
		$row = $this->getById($_GET["nodeId"]);
		if ($_GET["nodeId"] != "1"){
			$query = "DELETE FROM `wa_sitemap` WHERE `id`='".$_GET["nodeId"]."'";
			if ($res=mysql_query($query)){			
				$result = $this->recurDelete($row["id"]);
				$result .= $GLOBALS["msg"]["DEL-OK"];
			} else {
				$result = $GLOBALS["msg"]["ERR"];	
			}
		}
		return $result;
	}

	function recurDelete($pid){
		$q = "SELECT * FROM `wa_sitemap` WHERE `parent_id`='".$pid."'";
		if ($r=mysql_query($q)){
			if (mysql_num_rows($r) > 0){
				while ($row=mysql_fetch_array($r)){
					if ($_GET["nodeId"] != "1"){
						$query = "DELETE FROM `wa_sitemap` WHERE `id`='".$row["id"]."'";
						if ($res=mysql_query($query)){
							$result .= $this->recurDelete($row["id"]);
							//$result .= $GLOBALS["msg"]["DEL-OK"];
						} else {
							$result .= $GLOBALS["msg"]["ERR"];	
						}
					}
				}
			}
		}
		return $result;
	}

	function insert(){
		$query = "INSERT INTO `wa_sitemap` (`id`,`parent_id`,`def_name`) VALUES(NULL,'".$_POST["parent_id"]."','".$_POST["def_name"]."')";
		if ($res=mysql_query($query)){			
			return $GLOBALS["msg"]["INS-OK"];	
		} else {
			return $GLOBALS["msg"]["ERR"].mysql_error()."<br />".$query;	
		}
	}

	function update(){
		$query = "UPDATE `wa_sitemap` SET `def_name`='".$_POST["def_name"]."', `parent_id`='".$_POST["parent_id"]."' WHERE `id`='".$_POST["nodeId"]."'";
		if ($res=mysql_query($query)){
			return $GLOBALS["msg"]["UPD-OK"];
		} else {
			return $GLOBALS["msg"]["ERR"];	
		}
	}

	function treeMenu(){
		$query = "SELECT * FROM `wa_sitemap`";
		if ($res = mysql_query($query)){
			$nodes = "";
			while($node=mysql_fetch_array($res)){
				$nodes .= Templates::treeNode($node["parent_id"],$node["id"],$node["def_name"]);
			}			
			if ($nodes != ""){				
				switch ($_GET["do"]){
					case "new":
					case "edit":
						$cls = "visible";
						break;
					default:
						$cls = "hidden";
						break;
				}
				$editForm = $this->sitemapEdit($_GET["do"]);
				if ($this->nodeId != -1)
					$modulForm = $GLOBALS["moduls"]->getModulForm($this->nodeId);
				$output = Templates::webMap($nodes,$modulForm,$editForm,$this->nodeId,$cls);
			}			
		} else {
			$output = "error";
		}

		return $output;
	}

	function sitemapEdit($do){
		$query = "SELECT * FROM `wa_sitemap`";
		$option = array();
		$pId = 0;
		if (isSet($_GET["parentId"])) $pId = $_GET["parentId"];
		if ($res = mysql_query($query)){
			$nodes = "";
			while($node=mysql_fetch_array($res)){
				array_push($option,array($node["id"],$node["parent_id"],$node["def_name"]));
			}
		}
		$post = "insert";
		$output = '';
		if ($_GET["do"]=="edit" || $_GET["do"]=="new")
		{
			$form = '';
			if ($_GET["do"]=="edit"){
				$post = "update";
				$output = Templates::sitemapNodeHeader($GLOBALS['msg']['PAGE_EDIT']);
				$n = "SELECT * FROM `wa_sitemap` WHERE `id`='".$_GET["nodeId"]."'";
				if ($nres = mysql_query($n)){
					$nodeItem = mysql_fetch_array($nres);
					$pId = $nodeItem["parent_id"];
					$nodeName = $nodeItem["def_name"];
				} else {
					echo mysql_error();
				}
			}
			else
			{
				$output = Templates::sitemapNodeHeader($GLOBALS['msg']['PAGE_ADD']);
			}
			$opt = $this->nodeSelect($pId,0,$option,0);		
			$form .= '<label>'.$GLOBALS["msg"]["PAGE_PARENT"].':</label><select class="nodeSelect" name="parent_id">'.$opt.'</select>';
			$form .= Forms::TextRow("def_name",$nodeName,128,$GLOBALS["msg"]["PAGE_TITLE"],"iNodeName",false);
			$form .= Forms::Hidden("nodeId",$nodeItem["id"]);
			$form .= Forms::Hidden("post_action",$post);
			$form .= Forms::Hidden("type","webmap");
			$form .= Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
	
			$output .= '<div style=" padding: 0px 15px;">'.Forms::FormSimple($form).'</div>';
		}		
		return $output;
	}

	function nodeSelect($parentId,$id,$option,$count){
		foreach ($option as $nodeItem){
			if ($id == $nodeItem[1]){
				$sel = "";
				$addStr = "";
				$addStr = str_pad($addStr,$count*7*1,"&mdash;", STR_PAD_LEFT);  
				if ($parentId == $nodeItem[0]) $sel = 'selected="selected"';
				$o .= '<option value="'.$nodeItem[0].'" '.$sel.'>'.$addStr.$nodeItem[2].'</option>';
				$o .= $this->nodeSelect($parentId,$nodeItem[0],$option,$count+1);
			}
		}
		return $o;
	}

}