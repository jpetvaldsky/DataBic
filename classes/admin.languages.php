<?

class Languages extends Admin {

	function init(){
		$output = '';
		$action = $_GET["do"];
		if (isSet($_POST["do"])) $action = $_POST["do"];
		switch($action){
			case "new":
				$output .= $this->editLang(-1);
				break;
			case "edit":
				$output .= $this->editLang($_GET["id"]);
				break;
			default:
				$output .= $this->langList();
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
				case "active":
					$res = $this->activeRecord();
					break;
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

	function editLang($id) {
		$action = "update";
		$check = true;
		if ($id == -1){
			$action="insert";
			$legend = $GLOBALS["msg"]["NEW"];
		} else {			
			$data =  $this->recordById($id);
			$legend = $GLOBALS["msg"]["EDIT"].': '.$data["lang_name"];
		}
		if ($data["active"] == "0") $check=false;

		$output .= Forms::getForm("TextRow","lang_id",$data["lang_id"],2,$GLOBALS["msg"]["LANG_ID"]);
		$output .= Forms::getForm("TextRow","lang_name",$data["lang_name"],100,$GLOBALS["msg"]["LANG_DESC"]);
		$output .= Forms::getForm("CheckBox","active","1",0,$GLOBALS["msg"]["ACTIVE"],$check);

		if ($id != -1) $output .= Forms::Hidden("id",$id);
		$output .= Forms::Hidden("post_action",$action);
		$output .= Forms::Hidden("lang_id_prev",$data["lang_id"]);
		$output .= Forms::Hidden("type","languages");

		$buttons = Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
		$buttons .= Forms::Button("close",$GLOBALS["msg"]["CLOSE"],"?type=languages");
		$output .= Forms::FormActions($buttons);
		return Forms::Form($output,$legend);
	}

	function recordById($id){
		$query = "SELECT * FROM `wa_languages` WHERE `id`='".$id."'";
		if($res = mysql_query($query)){
			return mysql_fetch_array($res);
		}
		return -1;
	}

	function activeRecord(){
		$data = $this->recordById($_GET["id"]);
		$active = 1;
		if($data["active"] == 1) $active=0;
		$query = "UPDATE `wa_languages` SET `active`='".$active."' WHERE `id`='".$_GET["id"]."'";
		if ($res=mysql_query($query)){
			return $GLOBALS["msg"]["UPD-OK"];	
		} else {
			return $GLOBALS["msg"]["ERR"];	
		}
	}
	
	function deleteRecord(){
		$query = "DELETE FROM `wa_languages` WHERE `id`='".$_GET["id"]."'";
		if ($res=mysql_query($query)){			
			//if (!$GLOBALS["moduls"]->deleteLangRecord($_GET["lang_id"])) $result = $GLOBALS["menu"]["MODUL"].": ".$GLOBALS["msg"]["ERR"];
			//if (!Text::deleteLangRecord($_GET["lang_id"])) $result .= $GLOBALS["menu"]["TXT"].": ".$GLOBALS["msg"]["ERR"];
			$result .= $GLOBALS["msg"]["DEL-OK"];
		} else {
			$result = $GLOBALS["msg"]["ERR"];	
		}
		return $result;
	}
	

function insert(){
		$query = "INSERT INTO `wa_languages` (`id`,`lang_id`,`lang_name`,`active`) VALUES('','".$_POST["lang_id"]."','".$_POST["lang_name"]."',";
		$active = 1;
		if ($_POST["active"]!="1")  $active = 0; 
		$query .= "'".$active."')";
		if ($res=mysql_query($query)){			
			return $GLOBALS["msg"]["INS-OK"];	
		} else {
			return $GLOBALS["msg"]["ERR"].mysql_error()."<br />".$query;	
		}
	}

	function update(){
		$query = "UPDATE `wa_languages` SET `lang_id`='".$_POST["lang_id"]."', `lang_name`='".$_POST["lang_name"]."', ";
		$active = 1;
		if ($_POST["active"]!="1")  $active = 0; 
		$query .= "`active`='".$active."' WHERE `id`='".$_POST["id"]."'";
		if ($res=mysql_query($query)){
			if ($_POST["lang_id"] != $_POST["lang_id_prev"]){
				if (!$GLOBALS["moduls"]->updateLangId($_POST["lang_id_prev"],$_POST["lang_id"])) return $GLOBALS["msg"]["ERR"];
			}
			return $GLOBALS["msg"]["UPD-OK"];
		} else {
			return $GLOBALS["msg"]["ERR"];	
		}
	}

	function langList(){
		$sql_query = "SELECT * FROM `wa_languages`";
		if ($result = mysql_query($sql_query)){
			if (mysql_num_rows($result) > 0){
				$output .= $this->listHeadline();
				$count = 0;
				while ($row=mysql_fetch_array($result)){
					$output .= Templates::LangDataRow($id,$row,$count);
					$count++;
				}
			} else {
				$output .= $GLOBALS["msg"]["EMPTY"];
			}
		} else {
			$output .= $GLOBALS["msg"]["ERR"].mysql_error();
		}
		return Templates::ModulListTable($output);
	}

	function listHeadline(){
		//array("ID",20,"uniq_id"),
		$headItems = array(			
			array($GLOBALS["msg"]["LANG_ID"],60,"lang_id"),
			array($GLOBALS["msg"]["LANG_DESC"],"","lang_name")
		);
		array_push($headItems,array($GLOBALS["msg"]["STAT"],60,"active","centered"));
		$output = Templates::ModulHeadlineList($id,$headItems);
		return $output;
	}

}