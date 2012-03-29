<?php
class Search {
	var $modulList = array();

	function Search(){
		$sTable = "
			CREATE TABLE IF NOT EXISTS `wa_search_index` (
			`id` int(11) NOT NULL auto_increment,
			`site_id` INT( 11 ) NOT NULL DEFAULT  '0',
			`modul_id` INT( 11 ) NOT NULL DEFAULT  '0',
			`uniq_id` VARCHAR( 11 ) NOT NULL DEFAULT  '0',
			`content` TEXT NOT NULL ,
			`lang` VARCHAR( 2 ) NOT NULL ,
			`update_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (  `id` ) ,
			FULLTEXT (`content`)
			);";
		if (!$res = mysql_query($sTable)){
			echo(mysql_error($res));
		}
		$this->fillModulList();
	}
	
	function fillModulList(){
		if (is_dir($GLOBALS["modul_folder"])){
			$d = dir($GLOBALS["modul_folder"]);
			while($entry=$d->read()) {
				if (strpos($entry,".modul")){
					require_once ($GLOBALS["modul_folder"].$entry);
					$mNum = explode(".",$entry);
					array_push($this->modulList,array($mNum[0],''));
				}
			}
			$d->close();		
		}	
		if (is_array($this->modulList)){				
			foreach($this->modulList as $key=>$modulData){
				$modulName = "Modul_".$modulData[0];
				$modul = new $modulName;
				$fields = array();
				foreach ($modul->items as $row){
					switch ($row[2]){
						case "TextRow":
						case "ShortText":
						case "TwoLines":
						case "Text":		
						case "Wysiwyg":
							array_push($fields,$row[1]);
							break;
						default:
							break;
					}
				}
				$this->modulList[$key][1] = $fields;
			}
		}
	}
	
	function updateSearchContent(){
		$output = '';
		$emptyDB = 'TRUNCATE TABLE `wa_search_index`';
		if ($res = mysql_query($emptyDB)){
			$output .= 'Indexovaci vyhledavaci tabulka byla vymazana.<br>';
		} else {
			$output .= 'Indexovaci vyhledavaci tabulka nebyla vymazana.<br>'.mysql_error().'<br>';
			return $output;
		}
		if (is_array($this->modulList)){				
			foreach($this->modulList as $key=>$modulData){
				$fields = '';
				foreach ($modulData[1] as $f){
					if ($fields != '') $fields .= ',';
					$fields .= $f;
				}
				$getModulData = 'SELECT uniq_id,onSite,lang,active,'.$fields.' FROM modul_'.$modulData[0].'_data';				
				if ($mRes = mysql_query($getModulData)){
					if (mysql_num_rows($mRes) > 0)
					{
						$count = 0;
						while ($row = mysql_fetch_assoc($mRes)){
							$output .= $this->insertNewSearchIndex($modulData[0],$row,$count);
						}
						if ($count > 0){
							$output .= 'Byla provedena indexace '.$count.' radku z modulu '.$modulData[0].'.<br>';	
						}
					}
					else
					{
						$output .= 'Modul '.$modulData[0].' nevratil zadne data<br>';
					}
				} else {
					$output .= 'Modul '.$modulData[0].' nevratil zadne data<br>';
				}
				
			}
		}
		return $output;
	}
	
	function updateRowContent($modulId,$uniqId){
		$fields = $this->getModulFieldsKey($modulId);
		$getModulData = 'SELECT uniq_id,onSite,lang,active,'.$fields.' FROM modul_'.$modulId.'_data WHERE uniq_id='.$uniqId;
		if ($this->deleteRowContent($modulId,$uniqId)){
			if ($mRes = mysql_query($getModulData)){
				if (mysql_num_rows($mRes) > 0)
				{
					$row = mysql_fetch_assoc($mRes);
					$count = 0;
					$this->insertNewSearchIndex($modulId,$row,$count);
				}
			}
		}
	}
	
	function deleteRowContent($modul,$uniqId){
		$delRow = 'DELETE FROM `wa_search_index` WHERE `uniq_id`='.$uniqId.' LIMIT 1;';
		if ($delRes = mysql_query($delRow)){
			return true;
		}
		return false;
	}
	
	function insertNewSearchIndex($modul,$row,&$count){
		$output = '';
		$stringContent = '';
		foreach ($row as $key=>$value){
			switch ($key){
				case "uniq_id":
				case "onSite":
				case "lang":
				case "active":
					break;
				default:
					if ($row['active'] == 1)
						$stringContent .= str_replace('-',' ',urlize(strip_tags($value))).' ';//.'/'.$key;					
					break;
			}
		}
		if ($stringContent != ''){								
			$insertIndex = 
				'INSERT INTO `wa_search_index` (`id`,`site_id`,`modul_id`,`uniq_id`,`content`,`lang`,`update_date`) '.
				'VALUES(NULL,'.$row['onSite'].','.$modul.','.$row['uniq_id'].',\''.$stringContent.'\',\''.$row['lang'].'\',CURRENT_TIMESTAMP);';
			if ($ins=mysql_query($insertIndex)){
				$count++;
			} else {
				$output .= $row['uniq_id'].' nebyl pridan.<br>'.mysql_error().'<br>';
			}
		}
		
		return $output;
	}
	
	function getModulFieldsKey($modul){
		$fields = '';
		if (is_array($this->modulList)){				
			foreach($this->modulList as $key=>$modulData){
				if ($modulData[0] == $modul){
					foreach ($modulData[1] as $f){
						if ($fields != '') $fields .= ',';
						$fields .= $f;
					}
					break;
				}
			}
		}
		return $fields;
	}

}


?>