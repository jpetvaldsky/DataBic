<?

class Folders extends Admin {

	function buildStructure($type,$subId='',$ajax=false){
		$action = $_GET["action"];
		if (isSet($_POST["action"])) $action = $_POST["action"];
		switch ($action){
			case "insert_folder":
				$res = $this->insertFolder($type);
				break;
			case "update_folder":
				$res = $this->updateFolder($type);
				break;
			case "add_sub":
				$res = $this->newFolder($type,$_GET["folder_id"]);
				break;
			case "edit":
				$res= $this->editFolder($type,$_GET["folder_id"]);
				break;
			case "del":
				//$res = $this->deleteFolder($type,$_GET["folder_id"]);
				break;
			default:
				break;
		}

		$tree = array(
			array("root",$GLOBALS["menu"][strtoupper($type)],0,-1)
		);
		$query = "SELECT * FROM `wa_".$type."_folders`";
		if ($result = mysql_query($query)){
			while($row=mysql_fetch_array($result)){
				array_push($tree,array("folder",$row["name"],$row["id"]+0,$row["parent_id"]+0));
			}
		}
		
		$output = Templates::FolderTree($type,$tree,-1,$ajax,$subId).$res;
		$output = Templates::Folder($output,$type);
		return $output;
	}

	function insertFolder($type){
		$query = "INSERT INTO `wa_".$type."_folders` (`id`,`parent_id`,`name`) VALUES (NULL,'".$_POST["parent_id"]."','".$_POST["name"]."')";
		if ($res = mysql_query($query)){
			return "";
		}
		return $GLOBALS["msg"]["ERR"];	
	}


	function updateFolder($type){
		$query = "UPDATE `wa_".$type."_folders` SET `parent_id`='".$_POST["parent_id"]."', `name`='".$_POST["name"]."' WHERE `id`='".$_POST["folder_id"]."'";
		if ($res = mysql_query($query)){
			return "";
		}
		return $GLOBALS["msg"]["ERR"];
	}

	function FillFolderSelect($type){
		$data = array();
		array_push($data,array($GLOBALS["menu"][strtoupper($type)],0,0));
		$subdata = $this->FolderSelectData($type,$data,1,0);
		foreach ($subdata as $item) array_push($data,$item);
		$option = array();
		foreach ($data as $fold) {
			$pad = "";
			$pad = str_pad($pad, ($fold[2]+0), "-", STR_PAD_LEFT);
			if ($pad != "") $pad .= " ";
			array_push($option,array($fold[1],$pad.$fold[0]));
		}
		return $option;
	}

	function FolderSelectData($type,$data,$level,$pid){
		$data = array();
		$query = "SELECT * FROM `wa_".$type."_folders` WHERE `parent_id`='".$pid."'";
		if ($res = mysql_query($query)){
			while($row=mysql_fetch_array($res)){					
					array_push($data,array($row["name"],$row["id"]+0,$level));
					$subdata = $this->FolderSelectData($type,$data,$level+1,$row["id"]);
					foreach ($subdata as $item) array_push($data,$item);
			}
		}
		return $data;
	}

	function newFolder($type,$id){
		$output = "<h3>".$GLOBALS["msg"]["FOLD-ADD"]."</h3>";
		$option = $this->FillFolderSelect($type);
		$output .= Forms::Select("parent_id",$id,255,$GLOBALS["msg"]["FOLD-P"],$option,"shortField");
		$output .= Forms::TextRow("name","",255,$GLOBALS["msg"]["FOLD-NAME"],"shortField",false);
		$output .= Forms::Hidden("folder_id",$id);
		$output .= Forms::Hidden("action","insert_folder");
		$output .= Forms::Hidden("type",$type);
		$output .= Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
		return Forms::Form($output);
	}


	function editFolder($type,$id){
		$query = "SELECT * FROM `wa_".$type."_folders` WHERE `id`='".$id."'";
		if ($res = mysql_query($query)){
			$row=mysql_fetch_array($res);
		}
		$option = $this->FillFolderSelect($type);
		$output = "<h3>".$GLOBALS["msg"]["FOLD-EDIT"]."</h3>";
		$output .= Forms::Select("parent_id",$row["parent_id"],255,$GLOBALS["msg"]["FOLD-P"],$option,"shortField");
		$output .= Forms::TextRow("name",$row["name"],255,$GLOBALS["msg"]["FOLD-NAME"],"shortField",false);
		$output .= Forms::Hidden("folder_id",$id);
		$output .= Forms::Hidden("action","update_folder");
		$output .= Forms::Hidden("type",$type);
		$output .= Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
		return Forms::Form($output);
	}

	function deleteFolder($type,$id){
		$query = "DELETE FROM `wa_".$type."_folders` WHERE `id`='".$id."'";
		if ($res=mysql_query($query)){		
			return "";
		} else {
			echo mysql_error();
		}
		return $GLOBALS["msg"]["ERR"];
	}
	

}