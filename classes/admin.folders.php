<?

class Folders extends Admin {

	function buildStructure($type,$subId='',$ajax=false){
		$action = $_GET["action"];
		if (isSet($_POST["action"])) $action = $_POST["action"];
		$res = "";
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
		
		$folderOptions = '';
		$id = 0;
		
		if (isSet($_GET["folder_id"])) $id = $_GET["folder_id"];
		if (isSet($_POST["folder_id"])) $id = $_POST["folder_id"];
		
		if ($id > 0)
		{
			$query = "SELECT * FROM `wa_".$type."_folders` WHERE `id`='".$id."'";
			if ($resData = mysql_query($query)){
				$data = mysql_fetch_array($resData);
			}
		}
		else
		{
			$data = array(
			"id" => $id,
			"name" => $GLOBALS["menu"][strtoupper($type)]);
		}
		
		$folderOptions = '';
		if (!$ajax)
			$folderOptions = Templates::FolderOptions($res,$data,$type,$action);
		
		$output = Templates::FolderTree($type,$tree,-1,$ajax,$subId,true).$folderOptions;
		
		//$output = Templates::Folder($output,$type);
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

	function treeItems($type,$folderID)
	{
		$items = array();
		$finalItems = array();
		$firstParent = -1;
		$query = "SELECT * FROM `wa_".$type."_folders`";
		if ($res = mysql_query($query)){
			while($row=mysql_fetch_array($res)){				
				if ($folderID == $row["id"])
				{
					$firstParent = $row["parent_id"];
					array_push($finalItems,array(
						"name" => $row["name"],
						"link" => "?type=".$type."&amp;action=list&amp;folder_id=".$row["id"],
						"is_last" => true
					));			
				}
				else
				{
					array_push($items,$row);
				}
			}
		}	
		if ($firstParent != -1)
		{
			$this->getItemsByParentID($items,$finalItems,$firstParent,$type);
		}
		$finalItems = array_reverse($finalItems);
		return $finalItems;
	}
	
	function getItemsByParentID($items,&$output,$parentID,$type)
	{
		foreach($items as $row)
		{
			if ($row['id'] == $parentID)
			{
					array_push($output,array(
						"name" => $row["name"],
						"link" => "?type=".$type."&amp;action=list&amp;folder_id=".$row["id"],
						"is_last" => false
					));						
					$this->getItemsByParentID($items,$output,$row["parent_id"],$type);
			}
		}
		if ($parentID == 0)
		{
			array_push($output,array(
				"name" => $GLOBALS["menu"][strtoupper($type)],
				"link" => "?type=".$type."&amp;action=list&amp;folder_id=0",
				"is_last" => false
			));									
		}
	}
	


	function newFolder($type,$id){
		//$output = "<h3>".$GLOBALS["msg"]["FOLD-ADD"]."</h3>";
		$option = $this->FillFolderSelect($type);
		$output .= Forms::Select("parent_id",$id,255,$GLOBALS["msg"]["FOLD-P"],$option,"shortField");
		$output .= Forms::TextRow("name","",255,$GLOBALS["msg"]["FOLD-NAME"],"shortField",false);
		$output .= Forms::Hidden("folder_id",$id);
		$output .= Forms::Hidden("action","insert_folder");
		$output .= Forms::Hidden("type",$type);
		$output .= Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
		return Forms::FormSimple($output);
	}


	function editFolder($type,$id){
		$query = "SELECT * FROM `wa_".$type."_folders` WHERE `id`='".$id."'";
		if ($res = mysql_query($query)){
			$row=mysql_fetch_array($res);
		}
		$option = $this->FillFolderSelect($type);
		//$output = "<h3>".$GLOBALS["msg"]["FOLD-EDIT"]."</h3>";
		$output .= Forms::Select("parent_id",$row["parent_id"],255,$GLOBALS["msg"]["FOLD-P"],$option,"shortField");
		$output .= Forms::TextRow("name",$row["name"],255,$GLOBALS["msg"]["FOLD-NAME"],"shortField",false);
		$output .= Forms::Hidden("folder_id",$id);
		$output .= Forms::Hidden("action","update_folder");
		$output .= Forms::Hidden("type",$type);
		$output .= Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
		return Forms::FormSimple($output);
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