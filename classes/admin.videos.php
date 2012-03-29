<?

class Videos {


	function ListByFolder($folder,$subId='',$ajax=false){
		$action = $_GET["do"];
		if (isSet($_POST["do"])) $action = $_POST["do"];
		switch ($action){
			case "new":
				$output = Templates::VideoForm(Videos::NewVideo($folder));
				break;
			case "edit":
				$output = Templates::VideoForm(Videos::EditVideo($folder));
				break;
			case "insert_video":
				$output = Videos::InsertVideo($folder);
				break;
			case "update_video":
				$output = Videos::UpdateVideo($folder);
				break;
			default:
				break;
		}
		
		$query = "SELECT * FROM `wa_videos_data` WHERE `folder_id`='".$folder."'";
		if ($res = mysql_query($query)){
			while($row=mysql_fetch_array($res)){
				if ($row["id"] != $_GET["id"]){
					$row["subId"] = $subId;
					$Thumbnails .= Videos::AdminThumb($row,$ajax);
				}
			}
		}
		return $output.Templates::VideoList($Thumbnails,$ajax);
	}

	function VideoById($id){
		$query = "SELECT * FROM `wa_videos_data` WHERE `id`='".$id."'";
		if ($res = mysql_query($query)){			
			if (mysql_num_rows($res) > 0){
				return $row=mysql_fetch_array($res);
			}
		}
		return NULL;
	}

	/*
	function DeleteVideo($folder){
		$row = Images::ImageById($_GET["id"]);
		if (file_exists($GLOBALS["images_folder"]."Cache_Admin/".$row["filename"])){
			list($fName,$ext) = explode(".",$row["filename"]);
			Images::checkOldImages($GLOBALS["images_folder"],$row["filename"]);
            unlink($GLOBALS["images_folder"]."Cache_Admin/".$fName.".jpg");
		}
		if (file_exists($GLOBALS["images_folder"].$row["filename"])){
			unlink($GLOBALS["images_folder"].$row["filename"]);
		}
		$query = "DELETE FROM `wa_images_data` WHERE `id`='".$_GET["id"]."'";
		$res = mysql_query($query);
	}

	function DeleteAdminCache(){
		$d = dir($GLOBALS["images_folder"]."Cache_Admin/");
		while($entry=$d->read()) {
			if (($entry != ".") && ($entry != "..")){
				if (getimagesize($GLOBALS["images_folder"]."Cache_Admin/".$entry) !== false){
					if (unlink($GLOBALS["images_folder"]."Cache_Admin/".$entry)) echo $entry." deleted<br />";
				}
			}
		}
		$d->close(); 
	}
	*/
	
	function InsertVideo($folder,$fieldName=''){
		$title = '';
		if (isSet($_GET["title"])) $title = $_GET['title'];
		if (isSet($_POST["title"])) $title = $_POST['title'];
		if ($fieldName == '') $fieldName = 'file_item';
		$filename = Videos::uploadFile($fieldName);
		if ($filename == "") {
			$filename = "images/video-thumb.png";
			$pathInfo = pathinfo($filename);
			$filename = Videos::copyFile($filename);
		} else {
			$pathInfo = pathinfo($_FILES[$fieldName]['name']);
		}
		
		if ($_POST["server_video_id"] != "-1"){			
			$video_info = Videos::getVideoInfo($_POST["server_video_id"]);						
			$query = "INSERT INTO `wa_videos_data` (`id`,`folder_id`,`server_id`,`converted_filename`,`video_width`,`video_height`,`status`,`thumb_original_filename`,`thumb_filename`,`create_date`,`modify_date`,`title`) VALUES (NULL,'".$folder."','".$_POST["server_video_id"]."','".$video_info["converted_file"]."',".$video_info["width"].",".$video_info["height"].",'".$video_info["status"]."','".$pathInfo["basename"]."','".$filename."','".time()."','0','".$title."')";
			if ($res = mysql_query($query)){				
				return "";				
			} else {
				unlink($GLOBALS["images_folder"].$filename);
			}
			return $GLOBALS["msg"]["ERR"].mysql_error()."<br />".$query;	
		}
	}
	
	function UpdateVideo($folder){
		$filename = Videos::uploadFile("file_item");
		$row = Videos::VideoById($_POST["video_id"]);
		if ($filename != ""){
			if (file_exists($GLOBALS["images_folder"].$row["thumb_filename"])){
				unlink($GLOBALS["images_folder"].$row["thumb_filename"]);
			}

			$pathInfo = pathinfo($_FILES["file_item"]['name']);
			$query = "UPDATE `wa_videos_data` SET `thumb_original_filename`='".$pathInfo["basename"]."', `thumb_filename`='".$filename."', `modify_date`='".time()."' WHERE `id`='".$_POST["video_id"]."'";
			$res = mysql_query($query);
		}
		if ($row["title"] != $_POST["title"]){
			$query = "UPDATE `wa_videos_data` SET `title`='".$_POST["title"]."' WHERE `id`='".$_POST["video_id"]."'";
			$res = mysql_query($query);
		}
		if ($row["folder_id"] != $_POST["folder_id"]){
			$query = "UPDATE `wa_videos_data` SET `folder_id`='".$_POST["folder_id"]."' WHERE `id`='".$_POST["video_id"]."'";
			$res = mysql_query($query);
		}
		if ($_POST["server_video_id"] != "-1"){			
			$video_info = Videos::getVideoInfo($_POST["server_video_id"]);
			$query = "UPDATE `wa_videos_data` SET `server_id`= '".$_POST["server_video_id"]."', `converted_filename`='".$video_info["converted_file"]."', `video_width`='".$video_info["width"]."',`video_height`='".$video_info["height"]."',`status`='".$video_info["status"]."' WHERE `id`='".$_POST["video_id"]."'";
			$res = mysql_query($query);
		}

	}
	
	function updateVideoStatus($id,$video_id)
	{
		$video_info = Videos::getVideoInfo($id);
		$query = "UPDATE `wa_videos_data` SET `converted_filename`='".$video_info["converted_file"]."', `video_width`='".$video_info["width"]."',`video_height`='".$video_info["height"]."',`status`='".$video_info["status"]."' WHERE `id`='".$video_id."'";		
		$res = mysql_query($query);
		return $video_info;
	}


	function NewVideo($folder_id){		
		$output .= "<h3>".$GLOBALS["msg"]["NEW"]."</h3>";
		$output .= Forms::VideoUploader()."<hr style=\"margin: 5px 0px;\" />";
		$output .= Forms::FileUpload("file_item","Thumbnail, ".$GLOBALS["msg"]["CHOOSE"]);
		$output .= Forms::TextRow("title","",255,$GLOBALS["msg"]["TITLE"],"iText",false);
		$output .= Forms::Hidden("folder_id",$folder_id);
		$output .= Forms::Hidden("action","list");
		$output .= Forms::Hidden("do","insert_video");
		$output .= Forms::Hidden("type","videos");
		//$output .= Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
		$output .= '<hr style="margin: 5px 0px;" /><input id="videoSubmit" type="submit" disabled="disabled" value="'.$GLOBALS["msg"]["SAVE"].'" />';
		return Forms::Form($output);
	}

	function EditVideo($folder_id){
		$row = Videos::VideoById($_GET["id"]);		
		if ($row != NULL){
			$status = $row['status'];
			if ($row['status'] != 'converted'){
				$video_info = Videos::updateVideoStatus($row['server_id'],$_GET["id"]);
				$status = $video_info['status'];
			} else {
				$video_info = Videos::getVideoInfo($row['server_id']);
			}
			$output .= "<h3>".$GLOBALS["msg"]["EDIT"]."</h3>";
			$output .= Forms::VideoUploader().'<br />';
			if ($status == 'converted')
			{
				$output .= 'Video file: '.$video_info['converted_file'].'<br />';
				$output .= 'Status: '.$video_info['status'].'<br />';
				$output .= 'Size: '.$video_info['width'].'x'.$video_info['height'].'<br />';
				$output .= '<a href="javascript:$(\'#videoPreview\').show();">zobrazit video</a><br />';
				$output .= Forms::VideoPreview($video_info).'<br />';
			} 
			else 
			{
				$output .= 'Status: '.$video_info['status'].'<br />';
			}
			$output .= '<hr style="margin: 5px 0px;" />';
			$output .= 'Video Thumbnail:<br />'.Videos::AdminThumb($row)."<hr class=\"clear\" />";
			$output .= Forms::FileUpload("file_item","Thumbnail, ".$GLOBALS["msg"]["CHOOSE"]);
			$output .= Forms::TextRow("title",$row["title"],255,$GLOBALS["msg"]["TITLE"],"iText",false);
			$folders = new Folders();
			$option = $folders->FillFolderSelect("videos");
			$output .= Forms::Select("folder_id",$folder_id,255,$GLOBALS["msg"]["FOLD-P"],$option,"shortField");
			$output .= Forms::Hidden("video_id",$_GET["id"]);
			$output .= Forms::Hidden("action","list");
			$output .= Forms::Hidden("do","update_video");
			$output .= Forms::Hidden("type","videos");
			$output .= '<hr style="margin: 5px 0px;" /><input id="videoSubmit" type="submit" value="'.$GLOBALS["msg"]["SAVE"].'" />';
			return Forms::Form($output)."<hr />";
		}
	}

	function AdminThumb($data,$ajax=false){
		list($fName,$extension) = explode(".",$data["thumb_filename"]);
		$tResult =  Images::createThumbnail($data["thumb_filename"],$fName.".jpg","Cache_VideoAdmin/",80,80,80);
		$imgPath = $GLOBALS["images_folder"]."Cache_VideoAdmin/".$fName.".jpg";//$data["filename"];
		$output = Templates::VideoBox($imgPath,$data["title"],$data["id"],$data["folder_id"],$data["subId"],$ajax);
		return $output;
	}

	function AdminThumbById($id){
		$data = Videos::VideoById($id);
		list($fName,$extension) = explode(".",$data["thumb_filename"]);
		$tResult =  Images::createThumbnail($data["thumb_filename"],$fName.".jpg","Cache_VideoAdmin/",80,80,80);
		$imgPath = $GLOBALS["images_folder"]."Cache_VideoAdmin/".$fName.".jpg";//$data["filename"];
		return $imgPath;
	}


	function uploadFile($inpName)
	{
		$pathInfo = pathinfo($_FILES[$inpName]['name']);
		$newFName = urlize(str_replace(".".$pathInfo["extension"],"",$pathInfo["basename"])).".".strtolower($pathInfo["extension"]);
		$new_file = "videothumb_".md5(time())."_".$newFName;
		if (is_uploaded_file ($_FILES[$inpName]['tmp_name'])) {
			if (file_exists($GLOBALS["images_folder"].$new_file)){
				unlink($GLOBALS["images_folder"].$new_file);
			}
			if (move_uploaded_file($_FILES[$inpName]['tmp_name'],$GLOBALS["images_folder"].$new_file)){
				chmod($GLOBALS["images_folder"].$new_file, octdec('0777'));
				$fName = $new_file;
			} else {
				$fName = "";
			}
		} else {
			$fName = "";
		}	
		return $fName;
	}
	
	function copyFile($fileName)
	{
		$pathInfo = pathinfo($fileName);
		$newFName = urlize(str_replace(".".$pathInfo["extension"],"",$pathInfo["basename"])).".".strtolower($pathInfo["extension"]);
		$new_file = "videothumb_".md5(time())."_".$newFName;

		if (copy($fileName,$GLOBALS["images_folder"].$new_file)){
			chmod($GLOBALS["images_folder"].$new_file, octdec('0777'));
			$fName = $new_file;
		} else {
			$fName = "";
		}
		return $fName;
	}

	function getVideoInfo($videoId)
	{
		define(API_KEY,'45a17d84e');
		$url = "http://magic.glow.cz/api/";
		$method = "?method=status&API_KEY=%s&file_id=%s";
		
		$api_url = sprintf($url.$method,API_KEY,$videoId);

	    $curl = curl_init($api_url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
		curl_setopt ($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt ($curl, CURLOPT_POST, 1);
		
	    $response = curl_exec($curl);
		$err_no = curl_errno($curl);
		$err_msg = curl_error($curl);
		curl_close($curl);
		//echo $response;
		//exit;
		$xml = new SimpleXMLElement($response);
		$output = array();
		$output["converted_file"] = $xml->videofile->converted_filename.'';
		$output["status"] = $xml->videofile->status.'';
		$output["width"] = $xml->videofile->width+0;
		$output["height"] = $xml->videofile->height+0;
		return $output;
	}
	
	function LastInsertedId(){
		$query = "SELECT LAST_INSERT_ID() FROM `wa_videos_data`";
		if ($res = mysql_query($query)){				
			$row = mysql_fetch_array($res);
			return $row[0];			
		} else {
			return -1;
		}
		
	}


}