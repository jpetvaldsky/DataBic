<?

class Urls {


	function ListByFolder($folder,$subId='',$ajax=false){
		$action = $_GET["do"];
		if (isSet($_POST["do"])) $action = $_POST["do"];
		switch ($action){
			case "new":
				$output = Templates::UrlForm(Urls::NewUrl($folder));
				break;
			case "edit":
				$output = Templates::UrlForm(Urls::EditUrl($folder));
				break;
			case "delete":
				$output = Urls::DeleteUrl($folder);
				break;
			case "insert_url":
				$output = Urls::InsertUrl($folder);
				break;
			case "update_url":
				$output = Urls::UpdateUrl($folder);
				break;
			default:
				break;
		}
		
		$query = "SELECT * FROM `wa_urls_data` WHERE `folder_id`='".$folder."'";
		if ($res = mysql_query($query)){
			while($row=mysql_fetch_array($res)){
				if ($row["id"] != $_GET["id"]){
					$row["subId"] = $subId;
					$Thumbnails .= Urls::AdminThumb($row,$ajax);
				}
			}
		}
		return $output.Templates::UrlList($Thumbnails,$ajax);
	}

	function UrlById($id){
		$query = "SELECT * FROM `wa_urls_data` WHERE `id`='".$id."'";
		if ($res = mysql_query($query)){
			if (mysql_num_rows($res) > 0){
				return $row=mysql_fetch_array($res);
			}
		}
		return NULL;
	}

	function DeleteUrl($folder){
		//$row = Urls::UrlById($_GET["id"]);
		$query = "DELETE FROM `wa_urls_data` WHERE `id`='".$_GET["id"]."'";
		$res = mysql_query($query);
	}


	function InsertUrl($folder){
		$query = "INSERT INTO `wa_urls_data` (`id`,`folder_id`,`url_data`,`create_date`,`modify_date`,`title`) VALUES (NULL,'".$folder."','".$_POST["url_data"]."','".time()."','0','".$_POST["title"]."')";
		if ($res = mysql_query($query)){
			return "";
		} else {
			return $GLOBALS["msg"]["ERR"].mysql_error();	
		}		
	}

	function UpdateUrl($folder){
		$query = "UPDATE `wa_urls_data` SET `url_data`='".$_POST["url_data"]."', `title`='".$_POST["title"]."', `modify_date`='".time()."' WHERE `id`='".$_POST["url_id"]."'";
		if ($res = mysql_query($query)){
			return "";
		} else {
			return $GLOBALS["msg"]["ERR"].mysql_error();	
		}		

	}


	function NewUrl($folder_id){
		$output .= "<h3>".$GLOBALS["msg"]["NEW"]."</h3>";
		$output .= Forms::TextRow("url_data","",255,$GLOBALS["msg"]["URL"],"iText",false);
		$output .= Forms::TextRow("title","",255,$GLOBALS["msg"]["TITLE"],"iText",false);
		$output .= Forms::Hidden("folder_id",$folder_id);
		$output .= Forms::Hidden("action","list");
		$output .= Forms::Hidden("do","insert_url");
		$output .= Forms::Hidden("type","urls");
		$output .= Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
		return Forms::Form($output)."<hr />";
	}

	function EditUrl($folder_id){
		$row = Urls::UrlById($_GET["id"]);
		if ($row != NULL){
			$output .= "<h3>".$GLOBALS["msg"]["EDIT"]."</h3>";
			$output .= Forms::TextRow("url_data",$row["url_data"],255,$GLOBALS["msg"]["URL"],"iText",false);
			$output .= Forms::TextRow("title",$row["title"],255,$GLOBALS["msg"]["TITLE"],"iText",false);
			$output .= Forms::Hidden("folder_id",$folder_id);
			$output .= Forms::Hidden("url_id",$_GET["id"]);
			$output .= Forms::Hidden("action","list");
			$output .= Forms::Hidden("do","update_url");
			$output .= Forms::Hidden("type","urls");
			$output .= Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
			return Forms::Form($output)."<hr />";
		}
	}

	function AdminThumb($data,$ajax=false){
		$output = Templates::UrlBox($data["url_data"],$data["title"],$data["id"],$data["folder_id"],$data["subId"],$ajax);
		return $output;
	}
	function AdminThumbById($id,$subId){
		$data = Urls::UrlById($id);
		$output = Templates::UrlBoxPreview($data["url_data"],$data["title"],$data["id"],$data["folder_id"],$subId);
		return $output;
	}

	

}