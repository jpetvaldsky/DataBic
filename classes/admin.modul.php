<?

class Modules extends Admin {
	
	var $modulList = array();
	
	function Modules($sa){
		if (is_dir($GLOBALS["modul_folder"])){
			$d = dir($GLOBALS["modul_folder"]);
			while($entry=$d->read()) {
				if (strpos($entry,".modul")){
					require_once ($GLOBALS["modul_folder"].$entry);
					$mNum = explode(".",$entry);
					$modulName = "Modul_".$mNum[0];					
					if (class_exists($modulName)){
						$modul = new $modulName;	
						if ($modul->superadmin == true) {
							if ($sa) array_push($this->modulList,$mNum[0]);
						} else {
							array_push($this->modulList,$mNum[0]);
						}		
						$this->modulTableCreate($mNum[0],$modul->items);
					}
				}
			}
			$d->close();		
		}
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
					$res = $_POST["post_action"];
					break;
			}
			//$this->autoOrder($_POST["name"]);
		}
		if (isSet($_GET["do"])){
			switch ($_GET["do"]){
				case "active":
					$res = $this->activeRecord();
					break;
				case "delete":
					$res = $this->deleteRecord();
					break;
				case "push":
					$res = $this->reorderRecords();
					break;
				default:
					$res = $_GET["do"];
					break;
			}
			//$this->autoOrder($_GET["name"]);
		}		
		return $res;
	}

	function activeRecord(){
		$data = $this->ModulDataById($_GET["name"],$_GET["id"]);
		$active = 1;
		if($data["active"] == 1) $active=0;
		$query = "UPDATE `modul_".$_GET["name"]."_data` SET `active`='".$active."' WHERE `uniq_id`='".$_GET["id"]."'";
		if ($res=mysql_query($query)){
			return $GLOBALS["msg"]["UPD-OK"];	
		} else {
			return $GLOBALS["msg"]["ERR"];	
		}
	}
	
	function deleteRecord(){
		$query = "DELETE FROM `modul_".$_GET["name"]."_data` WHERE `row_id`='".$_GET["id"]."'";
		if ($res=mysql_query($query)){
			$result = $GLOBALS["msg"]["DEL-OK"];	
		} else {
			$result = $GLOBALS["msg"]["ERR"];	
		}
		$query = "SELECT * FROM `modul_".$_GET["name"]."_data`";
		if ($res=mysql_query($query)){
			if (mysql_num_rows($res) == 0){
				$query = "TRUNCATE `modul_".$_GET["name"]."_data`";
				$res=mysql_query($query);
			}
		}
		return $result;
	}

	function autoOrder($modulId){
		$sql_query = "SELECT * FROM `modul_".$modulId."_data` WHERE `lang`='".$GLOBALS["lang"]."' ORDER BY `order`,`uniq_id` ASC";
		$count = 0;
		$prevOrd = 0;
		$res = mysql_query($sql_query);
		while($row = mysql_fetch_array($res)){
			$count ++;
			$uquery = "UPDATE `modul_".$modulId."_data` SET `order`='".$count."' WHERE `uniq_id`='".$row["uniq_id"]."'";
			$ures=mysql_query($uquery);
			$ulangquery = "UPDATE `modul_".$modulId."_data` SET `order`='".$count."' WHERE `row_id`='".$row["row_id"]."'";
			$ures=mysql_query($ulangquery);
		}
	}

	function reorderRecords(){
		$sql_query = "SELECT * FROM `modul_".$_GET["name"]."_data` WHERE `order`=".$_GET["reorder"]." AND  `lang`='".$GLOBALS["lang"]."'";
		$res = mysql_query($sql_query);
		$rowPrev = mysql_fetch_array($res);
		$sql_query = "SELECT * FROM `modul_".$_GET["name"]."_data` WHERE `row_id`=".$_GET["id"]." AND  `lang`='".$GLOBALS["lang"]."'";
		$res = mysql_query($sql_query);
		$rowNew = mysql_fetch_array($res);
		$query1 = "UPDATE `modul_".$_GET["name"]."_data` SET `order`='".$_GET["reorder"]."' WHERE `row_id`='".$_GET["id"]."'";
		$query2 = "UPDATE `modul_".$_GET["name"]."_data` SET `order`='".$rowNew["order"]."' WHERE `row_id`='".$rowPrev["row_id"]."'";
		if ($res = mysql_query($query1)){
			if ($res = mysql_query($query2)){
				$result = $GLOBALS["msg"]["UPD-OK"];
			} else {
				$result = $GLOBALS["msg"]["ERR"].mysql_error();	
			}
		} else {
			$result = $GLOBALS["msg"]["ERR"].mysql_error();	
		}
		return $result;
	}
	
	function insertBlank($modul,$rowId,$lang,$onSite,$order){
		$query = "INSERT INTO `modul_".$modul."_data` (`uniq_id`,`row_id`,`onSite`,`lang`,`order`) VALUES(NULL,".$rowId.",".$onSite.",'".$lang."',".$order.")";
		if ($res=mysql_query($query))
			return true;
		else
			echo mysql_error()."<br />".$query;
		return false;
	}

	function insert(){
		$modulName = "Modul_".$_POST["name"];
		$modul = new $modulName;
		$values = array();
		foreach ($modul->items as $row){
			if ($row[2] != "Splitter")
				array_push($values,array($row[1],$row[2]));
		}
		$query = "INSERT INTO `modul_".$_POST["name"]."_data` (`uniq_id`,`row_id`,`onSite`,`order`,";
		foreach ($values as $val) $query .= "`".$val[0]."`,";
		$query .= "`active`,`lang`) VALUES(NULL,".$_POST["row_id"].",".$_POST["node_id"].",999,";
		foreach ($values as $val) {
			switch ($val[1]){
				case "CheckBox":
					if (!isSet($_POST[$val[0]]) || ($_POST[$val[0]]==""))
						$query .= "0,";
					else
						$query .= "'".$_POST[$val[0]]."',";
					break;
				case "DateInp":
					if ((strlen($_POST[$val[0]]) == 4) && ($_POST[$val[0]]+0 > 1900)){
						$year = $_POST[$val[0]];
						$month = $day = 1;
					} else {
						list($day, $month, $year) = explode(".", $_POST[$val[0]]);
					}
					$date = $year."-".$month."-".$day;
					$query .= "'".$date."',";
					break;
				case "Linked":
					$link = $_POST[$val[0]];
					$link = str_replace("xx",",",$link);
					$link = str_replace("x","",$link);
					$query .= "'".$link."',";
					break;
				case "Select":
					$multiple = $_POST[$val[0]];
					$mValue = "";
					if (is_array($multiple)){
						foreach ($multiple as $sel){
							if ($mValue != "") $mValue .= ",";
								$mValue .= $sel;
						}
					}
					$query .= "'".$mValue."',";
					break;
				case "Splitter":
					break;
				default:
					$query .= "'".$_POST[$val[0]]."',";
					break;
			}
		}

		$active = 1;
		if ($_POST["active"]!="1")  $active = 0; 
		$query .= "'".$active."','".$GLOBALS["lang"]."')";
		if ($res=mysql_query($query)){
			return $GLOBALS["msg"]["INS-OK"];	
		} else {
			return $GLOBALS["msg"]["ERR"]."<br />".$query."<br />";//.mysql_error()."<br>".$query;	
		}
	}

	function update(){
		$modulName = "Modul_".$_POST["name"];
		$modul = new $modulName;
		$values = array();
		foreach ($modul->items as $row){
			if ($row[2] != "Splitter")
				array_push($values,array($row[1],$row[2]));
		}
		$query = "UPDATE `modul_".$_POST["name"]."_data` SET ";
		foreach ($values as $val) {
			switch ($val[1]){
				case "CheckBox":
					if (!isSet($_POST[$val[0]]) || ($_POST[$val[0]]==""))
						$query .= "`".$val[0]."`='0', ";
					else
						$query .= "`".$val[0]."`='".$_POST[$val[0]]."', ";
					break;
				case "DateInp":
					list($day, $month, $year) = explode(".", $_POST[$val[0]]);
					$date = $year."-".$month."-".$day;
					$query .= "`".$val[0]."`='".$date."', ";
					break;
				case "Linked":
					$link = $_POST[$val[0]];
					$link = str_replace("xx",",",$link);
					$link = str_replace("x","",$link);
					$query .= "`".$val[0]."`='".$link."',";
					break;
				case "Splitter":
					break;
				case "Select":
					$multiple = $_POST[$val[0]];
					$mValue = "";
					if (is_array($multiple)){
						foreach ($multiple as $sel){
							if ($mValue != "") $mValue .= ",";
							$mValue .= $sel;
						}
					}
					$query .= "`".$val[0]."`='".$mValue."', ";
					break;
				default:
					$query .= "`".$val[0]."`='".str_replace("'","´",$_POST[$val[0]])."', ";
					break;
			}
		}
		$active = 1;
		if ($_POST["active"]!="1")  $active = 0; 
		$query .= "`active`='".$active."' WHERE `uniq_id`='".$_POST["uniq_id"]."'";
		if ($res=mysql_query($query)){
			return $GLOBALS["msg"]["UPD-OK"];	
		} else {
			return $GLOBALS["msg"]["ERR"]."<br />".mysql_error()."<br>".$query;	
		}
	}

	function listModulNavigation(&$name){
		if (is_array($this->modulList)){				
			foreach($this->modulList as $modulNum){
				if ($name == '')
				{
					$name = $modulNum;
				}
				
				$modulName = "Modul_".$modulNum;
				$modul = new $modulName;
				$ico = "Modul/".$modulNum.".gif";
				if (!file_exists("i/icon/".$ico)) $ico = "system-modul-default.gif";
				$output .= Templates::ModulItem($ico,$modul->name,"list",$modulNum);
			}
		}
		$output = Templates::ModulMenu($output);
		return $output;
	}

	function listModul($name,&$header,&$headerLink){
		if (($name != "") && isSet($name)){
			$modulName = "Modul_".$name;
			$modul = new $modulName;

			$headerLink = '?type=modul&name='.$name;	
			$header = Templates::topHeader($modul->name,$headerLink);
			$headerLink = $headerLink."&amp;action=new";
			
			$action = $_GET["action"];
			if (isSet($_POST["action"])) $action = $_POST["action"];
			switch ($action){
				case "new":
					$output .= $this->ModulDataForm($name,-1);
					break;
				case "edit":
					$output .= $this->ModulDataForm($name,$_GET["id"]);
					break;
				case "list":
				default:
					$output .= Templates::ModulList($this->modulContent($name,$modul->orderBy));
					break;
			}
		}
		return $output;
	}

	function getModulForm($nodeId){
		$option = array();
		$node = $GLOBALS["sitemap"]->getById($nodeId);
		$nodeArray = explode(",",$node["moduls"]);
		if (is_array($this->modulList)){				
			foreach($this->modulList as $modulNum){
				$modulName = "Modul_".$modulNum;
				$modul = new $modulName;
				$add = true;
				foreach($nodeArray as $modulId){
					if ($modulNum == $modulId) {
						$add = false;
						break;
					}				
				}
				if ($add)
					array_push($option,array($modulNum,$modul->name));
			}
		}
		$selMenu = Forms::SelectMenu("modulId",$option);
		$selMenu .= Forms::Hidden("post_action","add_modul");
		$selMenu .= Forms::Hidden("node_id",$nodeId);
		$selMenu .= Forms::Hidden("type","webmap");
		$selMenu .= Forms::Submit("submit",$GLOBALS["msg"]["ADD"]);

		$output = Forms::Form($selMenu);
		return $output;
	}

	function modulContent($name,$ord,$ajax=false){		
		$output .= $this->listModulData($name,$ord,$ajax);
		return $output;
	}

	function listModulData($modulId,$ord,$ajax,$onSite=false){
		// DEFINICE JAZYKU DAT
		$langData = array();
		$lQuery = "SELECT * FROM `wa_languages` WHERE `active`=1";		
		$res = mysql_query($lQuery);
		if ($ajax) {
			$lRow = mysql_fetch_array($res);
			array_push($langData,$lRow["lang_id"]);
		} else {
			while ($lRow=mysql_fetch_array($res)){
				array_push($langData,$lRow["lang_id"]);
			}
		}
		//VYPIS POLOZEK
		if (strlen($ord) > 1) $orderBy = " ORDER BY `".$ord."` ASC";
		if (isSet($_GET["order"])) $orderBy = " ORDER BY `".$_GET["order"]."` ".$_GET["d"];
		else $orderBy = " ORDER BY `order`, `uniq_id` ASC";
		$sql_query = "SELECT * FROM `modul_".$modulId."_data` WHERE `lang`='".$langData[0]."'";
		if ($onSite !== false){
			$sql_query .= " AND `onSite`='".$onSite."'";
		}
		$sql_query .= $orderBy;
		if ($result = mysql_query($sql_query)){
			if (mysql_num_rows($result) > 0){
				if (!$ajax) $output .= $this->listHeadline($modulId,$onSite);
				$count = 0;
				while ($row=mysql_fetch_array($result)){
					$data = $row;					
					foreach  ($langData as $langId){
						$langRow = true;
						if ($langId != $langData[0]) {
							$lData = "SELECT * FROM `modul_".$modulId."_data` WHERE `lang`='".$langId."' AND `row_id`='".$row["row_id"]."'";
							if ($lRes = mysql_query($lData)){
								if (mysql_num_rows($lRes) > 0)	{
									$data = mysql_fetch_array($lRes);
								} else {									
									if ($this->insertBlank($modulId,$row["row_id"],$langId,$row["onSite"],$row["order"])){
										$lData = "SELECT * FROM `modul_".$modulId."_data` WHERE `lang`='".$langId."' AND `row_id`='".$row["row_id"]."'";
										if ($lRes = mysql_query($lData))
											$data = mysql_fetch_array($lRes);
									} else {
										//echo mysql_error()."<br />".$lData."<br />".$row["onSite"];
										$langRow = false;
									}
								}
							} else {
								$langRow = false;
							}
						}
						if ($langRow){
							$dropRows = 0;
							if ($prevID != $row["row_id"])
							{
								$dropRows = count($langData);
								$prevID = $row["row_id"];
							} 
						
							$output .= $this->ModulDataRow($data,$modulId,$count,$ajax,$onSite,$dropRows);							
						}
					}
					$count++;
				}
				$output = Templates::ModulListTable($output);
			} else {
				$output = '<div class="alert alert-info"><strong>'.$GLOBALS["msg"]["EMPTY"].'</strong></div>';//$sql_query
			}
		} else {
			$output = '<div class="alert alert-error"><h4>'.$GLOBALS["msg"]["ERR"].'</h4> '.mysql_error().'</div>';
		}
		return $output;
	}

	function listHeadline($id,$onSite=false){
		$modulName = "Modul_".$id;
		$modul = new $modulName;
		$headItems = array(
			array("Pořadí",25,"order"),
			//array("ID",20,"uniq_id"),
			array("Jazyk","","lang"),
			
		);
		//array($GLOBALS["msg"]["ORD"],60)
		$count = 0;
		foreach ($modul->items as $row){
			$count++;
			if ($row[2] != "Linked" && $row[2] != "Splitter")//&& ($row[2] != "CheckBox")($row[2] != "Option") &&
				array_push($headItems,array($row[0],"",$row[1]));			
			if ($count == 4) break;
		}
		array_push($headItems,array($GLOBALS["msg"]["STAT"],40,"active"));
		$output = Templates::ModulHeadlineList($id,$headItems,$onSite);
		return $output;
	}

	function ModulDataRow($row,$id,$count,$ajax,$onSite=false,$dropRows=0){
		$modulName = "Modul_".$id;
		$modul = new $modulName;
		$maxOrder = "SELECT MAX(`order`) FROM `modul_".$id."_data`";
		$ores = mysql_query($maxOrder);
		$mo = mysql_fetch_array($ores);
		$maxOrd = false;
		if ($row["order"] == $mo[0]) $maxOrd = true;
		if (!$ajax)
			return Templates::ModulDataRow($id,$row,$count,$modul->items,$onSite,$maxOrd,$dropRows);		
		else
			return Templates::ModulLinkDataRow($id,$row,$count,$modul->items);		
	}

	function ModulDataById($modulId,$id){
		$query = "SELECT * FROM `modul_".$modulId."_data` WHERE `uniq_id`='".$id."'";
		if ($res=mysql_query($query)){
			return mysql_fetch_array($res);
		}
		return array();
	}

	function ModulDataByRowId($modulId,$id){
		$query = "SELECT * FROM `modul_".$modulId."_data` WHERE `row_id`='".$id."' AND `lang`='".$GLOBALS["lang"]."'";
		if ($res=mysql_query($query)){
			return mysql_fetch_array($res);
		}
		return array();
	}


	function ModulSelectData($modulId){
		$output = array();
		$modulName = "Modul_".$modulId;
		$modul = new $modulName;
		if (strlen($modul->orderBy) > 1) $orderBy = " ORDER BY `".$modul->orderBy."` ASC";
		$query = "SELECT * FROM `modul_".$modulId."_data` WHERE `active`='1' AND `lang`='".$GLOBALS["lang"]."'".$orderBy;
		if ($res=mysql_query($query)){
			while($row=mysql_fetch_array($res)){
				$oName = $row[4];
				if (is_array($modul->selectId)){
					$oName = "";
					foreach ($modul->selectId as $value){
						$oName .= $row[$value]." ";
					}
				}
				array_push($output,array($row["row_id"],$oName));
			}
		}
		return $output;
	}

	function ModulSelectetedOption($modulId,$rowId){
		$output = "";
		$modulName = "Modul_".$modulId;
		$modul = new $modulName;
		$query = "SELECT * FROM `modul_".$modulId."_data` WHERE `row_id`='".$rowId."' AND `active`='1'";
		if ($res=mysql_query($query)){
			if (mysql_num_rows($res) > 0){
				$row=mysql_fetch_array($res);
				return $row[4]." ";	
			}
		}
		return "=====";
	}

	function getRowId($name,$id){
			if ($id==-1) {
				$res = mysql_query("SELECT COUNT(DISTINCT(uniq_id)) AS `row_num` FROM `modul_".$name."_data`");
				$count = mysql_fetch_array($res);
				$rowid = $count[0]+1;
				$res = mysql_query("SELECT * FROM `modul_".$name."_data` WHERE `row_id`='$rowid'");
				if (mysql_num_rows($res) == 0)
					return ($rowid);
				else
					return $this->getRowId($name,$rowid+1);
			} else {
				$res = mysql_query("SELECT * FROM `modul_".$name."_data` WHERE `row_id`='$id'");
				if (mysql_num_rows($res) == 0)
					return ($id);
				else
					return $this->getRowId($name,$id+1);
			}
	}

	function ModulDataForm($name,$id,$onSite=false){
		$modulName = "Modul_".$name;
		$modul = new $modulName;
		$data = $this->ModulDataById($name,$id);
		$action = "update";
		$legend = "";
		
		$check = true;
		
		if ($id == -1){
			$action="insert";
			$rowid = $this->getRowId($name,-1);
			$legend = $GLOBALS["msg"]["NEW"];			
		}
		else
		{
			$legend = $GLOBALS["msg"]["EDIT"].': '.$id;				
		}
		
		if (is_array($modul->items)){
			foreach ($modul->items as $row){	
				$thisCheck = false;
				switch($row[2]){
					case "Option":
						$selectData = $this->ModulSelectData($row[6]);
						$output .= Forms::Select($row[1],$data[$row[1]],$row[3],$row[0],$selectData);
						break;
					case "Select":
							$selectData = $this->ModulSelectData($row[6]);
							$output .= Forms::SelectMultiple($row[1],$data[$row[1]],$row[3],$row[0],$selectData);
							break;
					case "CheckBox":
						if ($data[$row[1]] == "1") $thisCheck=true;
						$output .= Forms::getForm($row[2],$row[1],"1",$row[3],$row[0],$thisCheck);
						break;
					case "Year":
						if ($data[$row[1]] == "0000") $data[$row[1]] = "";
						$output .= Forms::getForm($row[2],$row[1],$data[$row[1]],$row[3],$row[0]);
						break;
					case "DateInp":
						$value = "";
						if (($data[$row[1]] != "") && ($data[$row[1]] != "0000-00-00")){
							list($year, $month, $day) = explode("-", $data[$row[1]]);
							$value = ($day+0).".".($month+0).".".$year;
						}
						$output .= Forms::getForm($row[2],$row[1],$value,$row[3],$row[0]);
						break;
					case "Linked":
						$value = "";
						if ($data[$row[1]] != ""){
							$value = str_replace(",","xx",$data[$row[1]]);
							$value = "x".$value."x";
						}
						$linked_id = $row[6];
						if (isSet($row[7])){
							$linked_id = $row[6]."_ID_".$row[7];
						}
						$output .= Forms::Linked($row[1],$row[0],$value,$linked_id,$name,$id);
						break;
					case "Splitter":
						$output .= Templates::Splitter($row[0],$row[1],$row[3]);
						break;
					default:
						$output .= Forms::getForm($row[2],$row[1],$data[$row[1]],$row[3],$row[0]);
						break;
				}
			}
			if ($data["active"] == "0") $check=false;
			$output .= Forms::getForm("CheckBox","active","1",0,$GLOBALS["msg"]["ACTIVE"],$check);
			$output .= Forms::Hidden("uniq_id",$id);
			$output .= Forms::Hidden("row_id",$rowid);
			$output .= Forms::Hidden("action","list");
			if ($onSite !== false){
				$output .= Forms::Hidden("node_id",$onSite);
				$output .= Forms::Hidden("post_action","m".$action);
				$output .= Forms::Hidden("type","webmap");
				$closeLink = "?type=webmap&nodeId=".$onSite."&name=".$name."&action=list";
			} else {
				$output .= Forms::Hidden("node_id",0);
				$output .= Forms::Hidden("post_action",$action);
				$output .= Forms::Hidden("type","modul");
				$closeLink = "?type=modul&name=".$name."&action=list";
			}
			$output .= Forms::Hidden("name",$name);
			
			$buttons = Forms::Submit("submit",$GLOBALS["msg"]["SAVE"]);
			$buttons .= Forms::Button("close",$GLOBALS["msg"]["CLOSE"],$closeLink);
			$output .= Forms::FormActions($buttons);

		}
		
		return Forms::Form($output,$legend);
	}
	/*
		MODUL LANGUAGES
	*/
	function updateLangId($lang_id_prev,$lang_id){
		$resUpd = true;
		if (is_array($this->modulList)){				
			foreach($this->modulList as $modulNum){
				$query = "UPDATE `modul_".$modulNum."_data` SET `lang`='".$lang_id."' WHERE `lang`='".$lang_id_prev."'";
				if (!$res=mysql_query($query)){					
					$resUpd = false;
					echo mysql_error();
				}
			}
		}
		return $resUpd;
	}

	function deleteLangRecord($lang_id){
		$resUpd = true;
		if (is_array($this->modulList)){				
			foreach($this->modulList as $modulNum){
				$query = "DELETE FROM `modul_".$modulNum."_data` WHERE `lang`='".$lang_id."'";
				if ($res=mysql_query($query)){		
				} else {
					$resUpd = false;
					echo mysql_error();
				}
			}
		}
		return $resUpd;
	}

	/*
		CREATE MODUL DB TABLE IF NOT EXIST
	*/
	function modulTableCreate($modulId,$items){
		$sql_create = "
CREATE TABLE IF NOT EXISTS `modul_".$modulId."_data` (
  `uniq_id` int(11) NOT NULL auto_increment PRIMARY KEY,
  `row_id` int(11) NOT NULL default '0',
  `onSite` int(11) NOT NULL default '0',
  `order` int(11) NOT NULL default '1',";
	$fulltext = array();
	foreach ($items as $row){		
		switch ($row[2]){
			case "Number":
			case "DateTime":
				$sql_create .= "`".$row[1]."` int(".$row[3].") NOT NULL default '0',\n";
				break;
			case "DateInp":
				$sql_create .= "`".$row[1]."` date NOT NULL,\n";
				break;
			case "Year":
				$sql_create .= "`".$row[1]."` year(4) NOT NULL default '2008',\n";
				break;
			case "CheckBox":
				$sql_create .= "`".$row[1]."` tinyint(".$row[3].") NOT NULL default '0',\n";
				break;
			case "TextRow":
				$sql_create .= "`".$row[1]."` varchar(".$row[3].") NOT NULL default '',\n";
				break;
			case "Option":
			case "Select":
			case "Linked":
				$sql_create .= "`".$row[1]."` varchar(255) NOT NULL default '',\n";
				break;
			case "ShortText":
			case "TwoLines":
				$sql_create .= "`".$row[1]."` text NOT NULL default '',\n";
				array_push($fulltext,$row[1]);
				break;
			case "Text":
				$sql_create .= "`".$row[1]."` mediumtext NOT NULL default '',\n";				
				array_push($fulltext,$row[1]);
				break;
			default:
				break;
		}
	}
	$sql_create .= "
  `active` int(1) NOT NULL default '0',
  `lang` varchar(2) NOT NULL default '".$GLOBALS["lang"]."'";
		if (count($fulltext) > 0){
		     $strFT = ",  
		FULLTEXT (\n";
			for($i=0;$i<count($fulltext);$i++){
				$strFT .= "`".$fulltext[$i]."`";
				if (($i+1)<count($fulltext)) $strFT .= ",\n"; else $strFT .= "\n";
			}
			$strFT .= "  )\n";
		}
		$sql_create .= $strFT;
		$sql_create .= "
	) TYPE=MyISAM AUTO_INCREMENT=1 ;";
	
		$result = mysql_query($sql_create);
		if (!$result){
			echo mysql_error();
		}
	}

}