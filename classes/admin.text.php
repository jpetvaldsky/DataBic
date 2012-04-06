<?

class Text extends Admin {

	function init(){
		$output = '';
		$action = $_GET["do"];
		if (isSet($_POST["do"])) $action = $_POST["do"];
		switch($action){
			case "new":
				$output .= $this->editText(-1);
				break;
			case "edit":
				$output .= $this->editText($_GET["id"]);
				break;
			default:
				$output .= $this->textList();
				break;			
		}
		return $output;
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
				default:
					//$res = $_GET["do"];
					break;
			}
		}
		return $res;
	}

	function editText($id) {
		$action = "update";
		$check = true;
		if ($id == -1){
			$action="insert";
			$legend = $GLOBALS["msg"]["NEW"];			
		} else {			
			$data =  $this->recordById($id);
			$legend = $GLOBALS["msg"]["EDIT"].': '.$data["id"];			
		}
		if ($data == null){
			$langData = array();
			$lQuery = "SELECT * FROM `wa_languages` WHERE `active`=1";
			$res = mysql_query($lQuery);
			while ($lRow=mysql_fetch_array($res)){
					array_push($langData,$lRow["lang_id"]);
			}
			$lang = $langData[0];
		} else {
			$lang = $data["lang_id"];
		}
		if ($data["active"] == "0") $check=false;
		$output .= Forms::getForm("Number","id",$data["id"],2,"ID");
		$output .= Forms::getForm("ShortText","value",$data["value"],2,"Text");

		$output .= Forms::Hidden("post_action",$action);
		$output .= Forms::Hidden("lang_id",$lang);
		$output .= Forms::Hidden("type","text");

		$buttons = Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
		$buttons .= Forms::Button("close",$GLOBALS["msg"]["CLOSE"],"?type=text");
		$output .= Forms::FormActions($buttons);


		return Forms::Form($output,$legend);
	}

	function recordById($id){
		$query = "SELECT * FROM `web_texts` WHERE `id`='".$id."' AND `lang_id`='".$_GET["lang_id"]."'";
		if($res = mysql_query($query)){
			return mysql_fetch_array($res);
		}
		return -1;
	}

	function deleteRecord(){
		$query = "DELETE FROM `web_texts` WHERE `id`='".$_GET["id"]."'";
		if ($res=mysql_query($query)){			
			$result .= $GLOBALS["msg"]["DEL-OK"];
		} else {
			$result = $GLOBALS["msg"]["ERR"];	
		}
		return $result;
	}

	function deleteLangRecord($lang_id){
		$resUpd = true;
		$query = "DELETE FROM `web_texts` WHERE `lang_id`='".$lang_id."'";
		if (!$res=mysql_query($query)){		
			$resUpd = false;
			echo mysql_error();
		}
		return $resUpd;
	}
	

	function insert(){
		$query = "INSERT INTO `web_texts` (`id`,`value`,`lang_id`) VALUES('".$_POST["id"]."','".htmlspecialchars($_POST["value"])."','".$_POST["lang_id"]."')";
		if ($res=mysql_query($query)){			
			return $GLOBALS["msg"]["INS-OK"];	
		} else {
			return $GLOBALS["msg"]["ERR"].mysql_error()."<br />".$query;	
		}
	}

	function insertBlank($id,$lang){
		$query = "INSERT INTO `web_texts` (`id`,`value`,`lang_id`) VALUES('".$id."','','".$lang."')";
		if ($res=mysql_query($query)){			
			return $GLOBALS["msg"]["INS-OK"];	
		} else {
			return $GLOBALS["msg"]["ERR"].mysql_error()."<br />".$query;	
		}
	}

	function update(){
		$query = "UPDATE `web_texts` SET `value`='".htmlspecialchars($_POST["value"])."' ";
		$query .= " WHERE `id`='".$_POST["id"]."' AND `lang_id`='".$_POST["lang_id"]."'";
		if ($res=mysql_query($query)){
			return $GLOBALS["msg"]["UPD-OK"];
		} else {
			return $GLOBALS["msg"]["ERR"];	
		}
	}

	function textList(){
		// DEFINICE JAZYKU DAT
		$langData = array();
		$lQuery = "SELECT * FROM `wa_languages` WHERE `active`=1";
		$res = mysql_query($lQuery);
		while ($lRow=mysql_fetch_array($res)){
				array_push($langData,$lRow["lang_id"]);
		}

		//VYPIS POLOZEK
		$sql_query = "SELECT * FROM `web_texts` WHERE `lang_id`='".$langData[0]."' ORDER BY `id`";
		if ($result = mysql_query($sql_query)){
			if (mysql_num_rows($result) > 0){
				$output .= $this->listHeadline();
				$count = 0;
				$prevID = '';
				while ($row=mysql_fetch_array($result)){
					$data = $row;
					foreach  ($langData as $langId){
						$langRow = true;
						if ($langId != $langData[0]) {
							$lData = "SELECT * FROM `web_texts` WHERE `lang_id`='".$langId."' AND `id`='".$row["id"]."'";
							if ($lRes = mysql_query($lData)){
								if (mysql_num_rows($lRes) > 0)	{
									$data = mysql_fetch_array($lRes);
								} else {									
									if ($this->insertBlank($row["id"],$langId)){
										$lData = "SELECT * FROM `web_texts` WHERE `lang_id`='".$langId."' AND `id`='".$row["id"]."'";
										if ($lRes = mysql_query($lData))
											$data = mysql_fetch_array($lRes);
									} else {
										echo mysql_error()."<br />".$lData;
										$langRow = false;
									}
								}
							} else {
								$langRow = false;
							}
							if ($langRow){
								//$output .= Templates::ModulLangSpliter($count);
							}
						}
						if ($langRow){
							$dropRows = 0;
							if ($prevID != $row["id"])
							{
								$dropRows = count($langData);
								$prevID = $row["id"];
							} 
							$output .= Templates::TextDataRow($id,$data,$count,$dropRows);			
						}
					}
					$count++;
				}
				$output = Templates::ModulListTable($output);
			} else {
				$output = Templates::Message($GLOBALS["msg"]["EMPTY"]);
			}
		} else {
			$output = Templates::Message(mysql_error(),$GLOBALS["msg"]["ERR"],"alert-error");
		}
		return $output;
	}

	function listHeadline(){
		//array("ID",20,"uniq_id"),
		$headItems = array(			
			array("ID",60,"id","center"),
			array("Text","","value"),
			array("Jazyk","","lang_id","center")
		);
		$output = Templates::ModulHeadlineList($id,$headItems);
		return $output;
	}

}