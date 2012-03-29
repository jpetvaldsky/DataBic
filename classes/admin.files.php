<?

class Files {


	function ListByFolder($folder,$subId='',$ajax=false){
		$action = $_GET["do"];
		if (isSet($_POST["do"])) $action = $_POST["do"];
		switch ($action){
			case "new":
				$output = Templates::FileForm(Files::NewFile($folder));
				break;
			case "edit":
				$output = Templates::FileForm(Files::EditFile($folder));
				break;
			case "delete":
				$output = Files::DeleteFile($folder);
				break;
			case "insert_file":
				$output = Files::InsertFile($folder);
				break;
			case "update_file":
				$output = Files::UpdateFile($folder);
				break;
			default:
				break;
		}
		
		$query = "SELECT * FROM `wa_files_data` WHERE `folder_id`='".$folder."'";
		if ($res = mysql_query($query)){
			while($row=mysql_fetch_array($res)){
				if ($row["id"] != $_GET["id"]){
					$row["subId"] = $subId;
					$Thumbnails .= Files::AdminThumb($row,$ajax);
				}
			}
		}
		return $output.Templates::FileList($Thumbnails,$ajax);
	}

	function FileById($id){
		$query = "SELECT * FROM `wa_files_data` WHERE `id`='".$id."'";
		if ($res = mysql_query($query)){
			if (mysql_num_rows($res) > 0){
				return $row=mysql_fetch_array($res);
			}
		}
		return NULL;
	}

	function DeleteFile($folder){
		$row = Files::FileById($_GET["id"]);
		if (file_exists($GLOBALS["files_folder"].$row["filename"])){
			unlink($GLOBALS["files_folder"].$row["filename"]);
		}
		$query = "DELETE FROM `wa_files_data` WHERE `id`='".$_GET["id"]."'";
		$res = mysql_query($query);
	}


	function InsertFile($folder){
		$filename = Files::uploadFile("file_item");
		if ($filename != ""){
			$pathInfo = pathinfo($_FILES["file_item"]['name']);
			$query = "INSERT INTO `wa_files_data` (`id`,`folder_id`,`original_filename`,`filename`,`create_date`,`modify_date`,`title`) VALUES (NULL,'".$folder."','".$pathInfo["basename"]."','".$filename."','".time()."','0','".$_POST["title"]."')";
			if ($res = mysql_query($query)){
				return "";
			} else {
				unlink($GLOBALS["files_folder"].$filename);
			}
			return $GLOBALS["msg"]["ERR"].mysql_error();	
		}
	}

	function UpdateFile($folder){
		$filename = Files::uploadFile("file_item");
		$row = Files::FileById($_POST["file_id"]);
		if ($filename != ""){
			if (file_exists($GLOBALS["files_folder"].$row["filename"])){
				unlink($GLOBALS["files_folder"].$row["filename"]);
			}

			$pathInfo = pathinfo($_FILES["file_item"]['name']);
			$query = "UPDATE `wa_files_data` SET `original_filename`='".$pathInfo["basename"]."', `filename`='".$filename."', `modify_date`='".time()."' WHERE `id`='".$_POST["file_id"]."'";
			$res = mysql_query($query);
		}
		if ($row["title"] != $_POST["title"]){
			$query = "UPDATE `wa_files_data` SET `title`='".$_POST["title"]."' WHERE `id`='".$_POST["file_id"]."'";
			$res = mysql_query($query);
		}
		if ($row["folder_id"] != $_POST["folder_id"]){
			$query = "UPDATE `wa_files_data` SET `folder_id`='".$_POST["folder_id"]."' WHERE `id`='".$_POST["file_id"]."'";
			$res = mysql_query($query);
		}
		/*$query = "UPDATE `wa_files_data` SET `title`='".$_POST["title"]."' WHERE `id`='".$_POST["file_id"]."'";
		$res = mysql_query($query);*/
	}


	function NewFile($folder_id){
		$output .= "<h3>".$GLOBALS["msg"]["NEW"]."</h3>";
		$output .= Forms::FileUpload("file_item",$GLOBALS["msg"]["CHOOSE"]);
		$output .= Forms::TextRow("title","",255,$GLOBALS["msg"]["TITLE"],"iText",false);
		$output .= Forms::Hidden("folder_id",$folder_id);
		$output .= Forms::Hidden("action","list");
		$output .= Forms::Hidden("do","insert_file");
		$output .= Forms::Hidden("type","files");
		$output .= Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
		return Forms::Form($output)."<hr />";
	}

	function EditFile($folder_id){
		$row = Files::FileById($_GET["id"]);
		if ($row != NULL){
			$output .= "<h3>".$GLOBALS["msg"]["EDIT"]."</h3>";
//			$output .= Images::AdminThumb($row)."<hr class=\"clear\" />";
			$output .= Forms::FileUpload("file_item",$GLOBALS["msg"]["CHOOSE"]);
			$output .= Forms::TextRow("title",$row["title"],255,$GLOBALS["msg"]["TITLE"],"iText",false);
			$folders = new Folders();
			$option = $folders->FillFolderSelect("files");
			$output .= Forms::Select("folder_id",$folder_id,255,$GLOBALS["msg"]["FOLD-P"],$option,"shortField");
			//$output .= Forms::Hidden("folder_id",$folder_id);
			$output .= Forms::Hidden("file_id",$_GET["id"]);
			$output .= Forms::Hidden("action","list");
			$output .= Forms::Hidden("do","update_file");
			$output .= Forms::Hidden("type","files");
			$output .= Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
			return Forms::Form($output)."<hr />";
		}
	}

	function AdminThumb($data,$ajax=false){
		$filePath = $GLOBALS["files_folder"]."/".$data["filename"];
		$output = Templates::FileBox($filePath,$data["original_filename"],$data["title"],$data["id"],$data["folder_id"],$data["subId"],$ajax);
		return $output;
	}

	function AdminThumbById($id,$subId){
		$data = Files::FileById($id);
		$filePath = $GLOBALS["files_folder"]."/".$data["filename"];
		$output = Templates::FileBoxPreview($filePath,$data["original_filename"],$data["title"],$data["id"],$data["folder_id"],$subId);
		return $output;
	}

	function uploadFile($inpName)
	{
		$pathInfo = pathinfo($_FILES[$inpName]['name']);
		$newFName = urlize(str_replace(".".$pathInfo["extension"],"",$pathInfo["basename"])).".".strtolower($pathInfo["extension"]);
		$new_file = md5(time())."_".$newFName;
		if (is_uploaded_file ($_FILES[$inpName]['tmp_name'])) {
			if (file_exists($GLOBALS["files_folder"].$new_file)){
				unlink($GLOBALS["files_folder"].$new_file);
				Images::checkOldImages($GLOBALS["files_folder"],$new_file);
			}
			if (move_uploaded_file($_FILES[$inpName]['tmp_name'],$GLOBALS["files_folder"].$new_file)){
				chmod($GLOBALS["files_folder"].$new_file, octdec('0777'));
				$fName = $new_file;
			} else {
				$fName = "";
			}
		} else {
			$fName = "";
		}	
		return $fName;
	}

	function FormatBytes($val, $digits = 3, $mode = "SI", $bB = "B"){ //$mode == "SI"|"IEC", $bB == "b"|"B"
        $si = array("", "k", "M", "G", "T", "P", "E", "Z", "Y");
        $iec = array("", "Ki", "Mi", "Gi", "Ti", "Pi", "Ei", "Zi", "Yi");
        switch(strtoupper($mode)) {
            case "SI" : $factor = 1000; $symbols = $si; break;
            case "IEC" : $factor = 1024; $symbols = $iec; break;
            default : $factor = 1000; $symbols = $si; break;
        }
        switch($bB) {
            case "b" : $val *= 8; break;
            default : $bB = "B"; break;
        }
        for($i=0;$i<count($symbols)-1 && $val>=$factor;$i++)
            $val /= $factor;
        $p = strpos($val, ".");
        if($p !== false && $p > $digits) $val = round($val);
        elseif($p !== false) $val = round($val, $digits-$p);
        return round($val, $digits) . " " . $symbols[$i] . $bB;
    }

	function FtpUpload(){
		$dir = $GLOBALS["files_folder"]."Upload/";
		$d = dir($dir);
		while($entry=$d->read()) {			
			if (($entry != ".") &&  ($entry != "..")){
				$filename = Files::uploadDirFile($dir.$entry);
				if ($filename != ""){
					$pathInfo = pathinfo($dir.$entry);
					$query = "INSERT INTO `wa_files_data` (`id`,`folder_id`,`original_filename`,`filename`,`create_date`,`modify_date`,`title`) VALUES (NULL,'0','".$pathInfo["basename"]."','".$filename."','".time()."','0','".$pathInfo["basename"]."')";
					if ($res = mysql_query($query)){
						unlink($dir.$entry);
						echo "Soubour ".$entry." vlozen uspesne<br>";
					} else {
						unlink($GLOBALS["files_folder"].$filename);
						echo "Soubor ".$entry." chyba: ".$GLOBALS["msg"]["ERR"].mysql_error()."<br>";	
					}					
				}
			}
		}
	}

	function uploadDirFile($filePath)
	{
		$pathInfo = pathinfo($filePath);
		$newFName = urlize(str_replace(".".$pathInfo["extension"],"",$pathInfo["basename"])).".".strtolower($pathInfo["extension"]);
		$new_file = md5(time())."_".$newFName;
		if (file_exists ($filePath)) {
			if (file_exists($GLOBALS["files_folder"].$new_file)){
				unlink($GLOBALS["files_folder"].$new_file);
			}
			echo $filePath;
			if (copy($filePath,$GLOBALS["files_folder"].$new_file)){
				chmod($GLOBALS["files_folder"].$new_file, octdec('0777'));
				$fName = $new_file;
			} else {
				$fName = "";
			}
		} else {
			$fName = "";
		}	
		return $fName;
	}
	

}