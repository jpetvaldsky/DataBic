<?

class Images {


	function ListByFolder($folder,$subId='',$ajax=false){
		$action = $_GET["do"];
		if (isSet($_POST["do"])) $action = $_POST["do"];
		switch ($action){
			case "new":
				$output = Templates::ImageForm(Images::NewImage($folder));
				break;
			case "edit":
				$output = Templates::ImageForm(Images::EditImage($folder));
				break;
			case "delete":
				//$output = Images::DeleteImage($folder);
				break;
			case "deleteCache":
				$output = Images::DeleteAdminCache();
				break;
			case "insert_image":
				$output = Images::InsertImage($folder);
				break;
			case "update_image":
				$output = Images::UpdateImage($folder);
				break;
			default:
				break;
		}
		
		$query = "SELECT * FROM `wa_images_data` WHERE `folder_id`='".$folder."'";
		if ($res = mysql_query($query)){
			while($row=mysql_fetch_array($res)){
				if ($row["id"] != $_GET["id"]){
					$row["subId"] = $subId;
					$Thumbnails .= Images::AdminThumb($row,$ajax);
				}
			}
		}
		return $output.Templates::ImageList($Thumbnails,$ajax);
	}

	function ImageById($id){
		$query = "SELECT * FROM `wa_images_data` WHERE `id`='".$id."'";
		if ($res = mysql_query($query)){			
			if (mysql_num_rows($res) > 0){
				return $row=mysql_fetch_array($res);
			}
		}
		return NULL;
	}

	function DeleteImage($folder){
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

	function InsertImage($folder,$fieldName=''){
		$title = '';
		if (isSet($_GET["title"])) $title = $_GET['title'];
		if (isSet($_POST["title"])) $title = $_POST['title'];
		if ($fieldName == '') $fieldName = 'file_item';
		$filename = Images::uploadFile($fieldName);
		if ($filename != ""){
			$pathInfo = pathinfo($_FILES[$fieldName]['name']);
			$query = "INSERT INTO `wa_images_data` (`id`,`folder_id`,`original_filename`,`filename`,`create_date`,`modify_date`,`title`) VALUES (NULL,'".$folder."','".$pathInfo["basename"]."','".$filename."','".time()."','0','".$title."')";
			if ($res = mysql_query($query)){				
				return "";
			} else {
				unlink($GLOBALS["images_folder"].$filename);
			}
			return $GLOBALS["msg"]["ERR"].mysql_error();	
		}
	}
	
	function LastInsertedId(){
		$query = "SELECT LAST_INSERT_ID() FROM `wa_images_data`";
		if ($res = mysql_query($query)){				
			$row = mysql_fetch_array($res);
			return $row[0];			
		} else {
			return -1;
		}
		
	}

	function UpdateImage($folder){
		$filename = Images::uploadFile("file_item");
		$row = Images::ImageById($_POST["image_id"]);
		if ($filename != ""){
			Images::checkOldImages($GLOBALS["images_folder"],$row["filename"]);
			/*if (file_exists($GLOBALS["images_folder"]."Cache_Admin/".$row["filename"])){
				unlink($GLOBALS["images_folder"]."Cache_Admin/".$row["filename"]);
			}*/
			if (file_exists($GLOBALS["images_folder"].$row["filename"])){
				unlink($GLOBALS["images_folder"].$row["filename"]);
			}

			$pathInfo = pathinfo($_FILES["file_item"]['name']);
			$query = "UPDATE `wa_images_data` SET `original_filename`='".$pathInfo["basename"]."', `filename`='".$filename."', `modify_date`='".time()."' WHERE `id`='".$_POST["image_id"]."'";
			$res = mysql_query($query);
		}
		if ($row["title"] != $_POST["title"]){
			$query = "UPDATE `wa_images_data` SET `title`='".$_POST["title"]."' WHERE `id`='".$_POST["image_id"]."'";
			$res = mysql_query($query);
		}
		if ($row["folder_id"] != $_POST["folder_id"]){
			$query = "UPDATE `wa_images_data` SET `folder_id`='".$_POST["folder_id"]."' WHERE `id`='".$_POST["image_id"]."'";
			$res = mysql_query($query);
		}
	}


	function NewImage($folder_id){
		$output .= "<h3>".$GLOBALS["msg"]["NEW"]."</h3>";
		$output .= Forms::FileUpload("file_item",$GLOBALS["msg"]["CHOOSE"]);
		$output .= Forms::TextRow("title","",255,$GLOBALS["msg"]["TITLE"],"iText",false);
		$output .= Forms::Hidden("folder_id",$folder_id);
		$output .= Forms::Hidden("action","list");
		$output .= Forms::Hidden("do","insert_image");
		$output .= Forms::Hidden("type","images");
		$output .= Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
		return Forms::Form($output)."<hr />";
	}

	function EditImage($folder_id){
		$row = Images::ImageById($_GET["id"]);
		if ($row != NULL){
			$output .= "<h3>".$GLOBALS["msg"]["EDIT"]."</h3>";
			$output .= Images::AdminThumb($row)."<hr class=\"clear\" />";
			$output .= Forms::FileUpload("file_item",$GLOBALS["msg"]["CHOOSE"]);
			$output .= Forms::TextRow("title",$row["title"],255,$GLOBALS["msg"]["TITLE"],"iText",false);
			$folders = new Folders();
			$option = $folders->FillFolderSelect("images");
			$output .= Forms::Select("folder_id",$folder_id,255,$GLOBALS["msg"]["FOLD-P"],$option,"shortField");
			//$output .= Forms::Hidden("folder_id",$folder_id);
			$output .= Forms::Hidden("image_id",$_GET["id"]);
			$output .= Forms::Hidden("action","list");
			$output .= Forms::Hidden("do","update_image");
			$output .= Forms::Hidden("type","images");
			$output .= Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
			return Forms::Form($output)."<hr />";
		}
	}

	function AdminThumb($data,$ajax=false){
		list($fName,$extension) = explode(".",$data["filename"]);
		$tResult =  Images::createThumbnail($data["filename"],$fName.".jpg","Cache_Admin/",80,80,80);
		$imgPath = $GLOBALS["images_folder"]."Cache_Admin/".$fName.".jpg";//$data["filename"];
		$output = Templates::ImageBox($imgPath,$data["title"],$data["id"],$data["folder_id"],$data["subId"],$ajax);
		return $output;
	}

	function AdminThumbById($id){
		$data = Images::ImageById($id);
		list($fName,$extension) = explode(".",$data["filename"]);
		$tResult =  Images::createThumbnail($data["filename"],$fName.".jpg","Cache_Admin/",80,80,80);
		$imgPath = $GLOBALS["images_folder"]."Cache_Admin/".$fName.".jpg";//$data["filename"];
		return $imgPath;
	}


	function FtpUpload(){
		$dir = $GLOBALS["images_folder"]."Upload/";
		$d = dir($dir);
		while($entry=$d->read()) {			
			if (($entry != ".") &&  ($entry != "..")){
				$filename = Images::uploadDirFile($dir.$entry);
				if ($filename != ""){
					$pathInfo = pathinfo($dir.$entry);
					$query = "INSERT INTO `wa_images_data` (`id`,`folder_id`,`original_filename`,`filename`,`create_date`,`modify_date`,`title`) VALUES (NULL,'0','".$pathInfo["basename"]."','".$filename."','".time()."','0','".$pathInfo["basename"]."')";
					if ($res = mysql_query($query)){
						unlink($dir.$entry);
						echo "Obrazek ".$entry." vlozen uspesne<br>";
					} else {
						unlink($GLOBALS["images_folder"].$filename);
						echo "Obrazek ".$entry." chyba: ".$GLOBALS["msg"]["ERR"].mysql_error()."<br>";	
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
			//chmod($filePath, octdec('0777'));
			if (file_exists($GLOBALS["images_folder"].$new_file)){
				unlink($GLOBALS["images_folder"].$new_file);
			}
			if (copy($filePath,$GLOBALS["images_folder"].$new_file)){
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


	function uploadFile($inpName)
	{
		$pathInfo = pathinfo($_FILES[$inpName]['name']);
		$newFName = urlize(str_replace(".".$pathInfo["extension"],"",$pathInfo["basename"])).".".strtolower($pathInfo["extension"]);
		$new_file = md5(time())."_".$newFName;
		if (is_uploaded_file ($_FILES[$inpName]['tmp_name'])) {
			if (file_exists($GLOBALS["images_folder"].$new_file)){
				unlink($GLOBALS["images_folder"].$new_file);
				Images::checkOldImages($GLOBALS["images_folder"],$new_file);
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

	function checkOldImages($dir,$name){
		$d = dir($dir);
		while($entry=$d->read()) {
			if (is_dir($dir.$entry) && ($entry != ".") &&  ($entry != "..")){
				$sub = dir($dir.$entry);
				while($filesInDir=$sub->read()) {
					//echo $dir.$entry.'/'.$filesInDir.' - '.$name.'<br />';
					if ($filesInDir == $name){
						//echo 'mam ho -> '.$dir.$entry.'/'.$name.'<br />';
						unlink($dir.$entry.'/'.$name);
					}
				}
				$sub->close(); 
			}
		}
		$d->close(); 
	}


	function createThumbnail($original,$filename,$output,$width,$height,$quality,$cropW=false,$cropH=false,$bw=false,$cropCentered=false){
		if (!file_exists($GLOBALS["images_folder"].$output))
			mkdir($GLOBALS["images_folder"].$output,0777);
			
		if (@chmod($GLOBALS["images_folder"].$output,0777)){ }
		
		if (file_exists($GLOBALS["images_folder"].$output)){
			if (file_exists($GLOBALS["images_folder"].$original)){				
				$target = $GLOBALS["images_folder"].$output.$filename;
				$source = $GLOBALS["images_folder"].$original;
				$createImage = false;				
					if (file_exists($target)){						
						$sizeExist = getimagesize($target);
						if ($width == $height){
								if (($sizeExist[0] > $sizeExist[1]) && ($width != $sizeExist[0]))
									$createImage = true;			
								if (($sizeExist[1] > $sizeExist[0]) && ($height != $sizeExist[1]))
									$createImage = true;			
						} else {
							if (($width != 0) && ($width != $sizeExist[0]))
								$createImage = true;
							if (($height != 0) && ($height != $sizeExist[1]))
								$createImage = true;
						}
						//echo "required: ".$width."x".$height.", existing: ".$sizeExist[0].'x'.$sizeExist[1]."<br />";						
					} else {
						$createImage = true;
					}
					if (!$createImage){ // && !$hardUpdate && (filemtime($target) > time()-(1 * 24 * 60 * 60)
						return true;
					} else {		
						//echo $source."<br>";
						list($iWidth,$iHeight,$iType) = getimagesize($source);
						 switch($iType) {
						   case 1:  $image_input=imagecreatefromgif($source); break;
						   case 2:  $image_input=imagecreatefromjpeg($source); break;
						   case 3:  $image_input=imagecreatefrompng($source); break;
						   case 6:
						   case 15:
							   $image_input=imagecreatefromwbmp($source); break;
						   default: echo "Unkown filetype (file $file, typ $otype)"; return;
						}
						//$image_input = imagecreatefromjpeg($source); 
						if (($width == 0) && ($height > 0)){
							$ratio = $iWidth/$iHeight;
							$fH = $height;
							$fW = $fH*$ratio;		
						}
						if (($width > 0) && ($height == 0)){
							$ratio = $iWidth/$iHeight;
							$fW = $width;
							$fH = $fW/$ratio;		
						}
						if (($width > 0) && ($height > 0)){
							if ($width == $height){
								if ($iWidth > $iHeight){
									$ratio = $iWidth/$iHeight;
									$fW = $width;
									$fH = $fW/$ratio;						
								} else {
									$ratio = $iWidth/$iHeight;
									$fH = $height;
									$fW = $fH*$ratio;	
								}
							} else {
								$fW = $width;
								$fH = $height;		
							}
						}
						
						//DODELAT i NA JINE TYPY
						$image_output = imagecreatetruecolor($fW, $fH);
						imagecopyresampled ($image_output,$image_input, 0, 0, 0,0, $fW, $fH, $iWidth, $iHeight);
						
						//OREZ OBRAZKU
						if (($cropW !== false)  || ($cropH !== false)){
							$srcX = 0; $srcY=0;
							$cropedWidth = $fW;
							if ($cropW !== false) {
								if ($cropW < $fW){
									$srcX = floor(($fW-$cropW)/2);
									$cropedWidth = $cropW;
								}
							}
							$cropedHeight = $fH;
							if ($cropH !== false) {
								if ($cropH < $fH){
									$srcY = floor(($fH-$cropH)/2);
									$cropedHeight = $cropH;
								}
							}
							$startX = 0; $startY = 0;
							if ($cropCentered){
								/*if ($srcX != 0){
									$startX = floor(($fW - $cropW)/2);
									$srcX = $startX + $srcX;
								}
								if ($srcY != 0){
									$startY = floor(($fH - $cropH)/2);
									$srcY = $startY + $srcY;
								}*/
							}
							$image_crop = imagecreatetruecolor($cropedWidth, $cropedHeight);
							imagecopyresampled ($image_crop,$image_output, 0, 0, $srcX,$srcY, $cropedWidth, $cropedHeight, $cropedWidth, $cropedHeight);//$fW, $fH
							$image_output = $image_crop;
							$fW = $cropedWidth;
							$fH = $cropedHeight;
						}

						// CONVERT TO GRAYSCALE
						if ($bw){
							// note: this will NOT affect your original image, unless
							// originalFileName and destinationFileName are the same
							for ($y = 0; $y <$fH; $y++) {
								for ($x = 0; $x <$fW; $x++) {
									$rgb = imagecolorat($image_output, $x, $y);
									$red   = ($rgb >> 16) & 0xFF;
									$green = ($rgb >> 8)  & 0xFF;
									$blue  = $rgb & 0xFF;
									
									$gray = round(.299*$red + .587*$green + .114*$blue);
									//$gray = round((255+$gray)*0.25);
									// shift gray level to the left
									$grayR = $gray << 16;   // R: red
									$grayG = $gray << 8;    // G: green
									$grayB = $gray;         // B: blue
								   
									// OR operation to compute gray value
									$grayColor = $grayR | $grayG | $grayB;
									// set the pixel color
									imagesetpixel ($image_output, $x, $y, $grayColor);
									imagecolorallocate ($image_output, $gray, $gray, $gray);
								}
							}
							// copy pixel values to new file buffer
							$image_bw = ImageCreateTrueColor($fW, $fH);
							imagecopy($image_bw, $image_output, 0, 0, 0, 0, $fW, $fH);
							$image_output = $image_bw;
							
							/*if ($image_output && imagefilter($image_output, IMG_FILTER_GRAYSCALE)) {
								echo "GR OK";
							}	else {
								echo "GR ERROR";	
							}*/
						}
									$grayR = 10 << 16;   // R: red
									$grayG = 10 << 8;    // G: green
									$grayB = 10;         // B: blue
								   
									// OR operation to compute gray value
									$grayColor = $grayR | $grayG | $grayB;
						imagejpeg($image_output,$target,$quality);
						chmod($target,0777);
						return true;
					}			
			}
		}
	}

	function getImageLinkedDescription($imgId,$modulId,$rowId,$lang){
		$q = "SELECT * FROM `wa_images_desc` WHERE `item_id`='$imgId' AND `modul_id`='$modulId' AND `uniq_id`='$rowId' AND `lang`='$lang'";
		if ($res = mysql_query($q)){
			if (mysql_num_rows($res) > 0){
				return mysql_fetch_array($res);
			}
		}
		return -1;		
	}

}