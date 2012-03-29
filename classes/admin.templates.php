<?

class Templates {
	/*
		MODUL TEMPLATES
		-----------------------------------------------
	*/
	function ModulItem($ico,$name,$action,$modulName,$onSite=false){
		if ($GLOBALS["name"] == $modulName){
			$cls = "Active";
		}
		$link = '?type=modul&name='.$modulName.'&action='.$action;
		if ($onSite !==false)
			$link = '?type=webmap&nodeId='.$onSite.'&name='.$modulName.'&action='.$action;
		$output = '<div class="modulItem'.$cls.'"><a href="'.$link.'">'.$name.'</a></div><div class="stripeModul"><img src="i/pix.gif" width="1" height="41" /></div>';
		// style="background-image: url(\'i/icon/'.$ico.'\');"
		return $output;		
	}

	function ModulMenu($data){
		$output = '<div class="modulMenu">'.$data.'<div class="clear"></div></div>';//'<h1>'.$GLOBALS["menu"]["MODUL"].'</h1>'.
		return $output;
	}

	function ModulList($data){
		$output = '<div class="modulList">'.$data.'</div>';
		return $output;
	}

	function ModulListHeader($name,$id,$onSite=false){
		$link = '?type=modul&name='.$id.'&action=new';
		if ($onSite !== false)
			$link = '?type=webmap&nodeId='.$onSite.'&name='.$id.'&action=new';
		$output = '<div class="mHeadLine"><div class="left"><h2>'.$name.'</h2></div>';
		$output .= '<div class="itemAdd left">&nbsp;|&nbsp;<a href="'.$link.'" class="add">'.$GLOBALS["msg"]["NEW"].'</a></div>&nbsp;';
		$output .= '<div class="clear"><img src="i/pix.gif" width="1" height="1" /></div></div>';
		return $output;
	}

	function ModulHeadlineList($modul,$headers,$onSite=false){
		$d = "ASC";
		if ($_GET["d"] == "ASC") $d="DESC";
		foreach ($headers as $value){
			if ($onSite !== false)
				$sortLink = 'index.php?type=webmap&nodeId='.$onSite.'&name='.$modul.'&order='.$value[2].'&d='.$d;
			else
				$sortLink = 'index.php?type=modul&name='.$modul.'&order='.$value[2].'&d='.$d;
			if ($value[1] != "") $width= "width=\"".$value[1]."\"";
			else $width="";
			$output .= "
		<th ".$width."><a href=\"".$sortLink."\">".$value[0]."</a></th>";
		}		
		$output .= "
		<th colspan=\"2\" width=\"100\" style=\"text-align:center;\">".$GLOBALS["msg"]["TOOLS"]."</th>";
		return "
	<tr>".$output."
	</tr>";
	}

	function ModulDataRow($modul,$row,$count,$items,$onSite,$maxOrd=false){
		$ico_a = "item-active.gif";
		$title_a = $GLOBALS["msg"]["DEACTIVE"];
		if ($row["active"]+0 != 1) {
			$ico_a = "item-disabled.gif";
			$title_a = $GLOBALS["msg"]["ACTIVE"];
		}
		if ($onSite !== false){
			$editLink = 'index.php?type=webmap&nodeId='.$onSite.'&name='.$modul.'&action=edit&id='.$row["uniq_id"];
			$activateLink = 'index.php?type=webmap&nodeId='.$onSite.'&name='.$modul.'&action=list&do=mactive&id='.$row["uniq_id"];
			$deleteLink = 'index.php?type=webmap&nodeId='.$onSite.'&name='.$modul.'&action=list&do=mdelete&id='.$row["row_id"];
			$orderLinkUp = 'index.php?type=webmap&nodeId='.$onSite.'&name='.$modul.'&action=list&do=mpush&id='.$row["row_id"].'&reorder='.($row["order"]-1);
			$orderLinkDown = 'index.php?type=webmap&nodeId='.$onSite.'&name='.$modul.'&action=list&do=mpush&id='.$row["row_id"].'&reorder='.($row["order"]+1);
		} else {
			$editLink = 'index.php?type=modul&name='.$modul.'&action=edit&id='.$row["uniq_id"];
			$activateLink = 'index.php?type=modul&name='.$modul.'&action=list&do=active&id='.$row["uniq_id"];
			$deleteLink = 'index.php?type=modul&name='.$modul.'&action=list&do=delete&id='.$row["row_id"];
			$orderLinkUp = 'index.php?type=modul&name='.$modul.'&action=list&do=push&id='.$row["row_id"].'&reorder='.($row["order"]-1);
			$orderLinkDown = 'index.php?type=modul&name='.$modul.'&action=list&do=push&id='.$row["row_id"].'&reorder='.($row["order"]+1);
		}
		$output = '			
			<td onclick="location.href=\''.$editLink.'\'"><small>'.$row["order"].'/<em>'.$row["uniq_id"].'</em></td>
			<td onclick="location.href=\''.$editLink.'\'">'.$row["lang"].'</td>
			';
		//<td>'.$row["order_id"].'</td>
		$rcount = 0;
		foreach ($items as $r){
			$rcount++;
			if (($r[2] != "Linked")){//&& ($r[2] != "CheckBox")($r[2] != "Option") && 
				switch($r[2]){
					case "CheckBox":
						$value = "ne";
						if ($row[$r[1]] == "1") $value = "ano";						
						break;
					case "DateInp":
						$value = "";
						if (($row[$r[1]] != "") && ($row[$r[1]] != "0000-00-00")){
							list($year, $month, $day) = explode("-", $row[$r[1]]);
							$value = ($day+0).".".($month+0).".".$year;
						}
						break;
					case "DateTime":
						$value = "";
						if (($row[$r[1]] != "") && ($row[$r[1]] != 0)){
							$value = date("j.n.Y H:i",$row[$r[1]]);
						}
						break;
					case "TimeStamp":
						$value = "";
						if ($row[$r[1]] != ""  && $row[$r[1]] != "0000-00-00 00:00:00"){
							$value = date("j.n.Y H:i",strtotime($row[$r[1]]));
						}
						break;
					case "Text":
					case "ShortText":
					case "TwoLines":
					case "Wysiwyg":
						$value = shortenString(htmlspecialchars($row[$r[1]]),20);//str_replace("\"","\\\"",htmlentities(iconv("UTF-8", "ISO-8859-1",)));
						break;
					case "Option":
						$value = Modules::ModulSelectetedOption($r[6],$row[$r[1]]);
						break;
					case "Branches":
						$value = Modules::ModulSelectetedOption($r[6],$row["parent_id"]);
						break;
					case "SelectRecursive":
						$value = Modules::ModulSelectetedOption($r[6],$row[$r[1]]);
						break;
					default:
						$value = $row[$r[1]];
						break;
				}
						$output .= '
				<td onclick="location.href=\''.$editLink.'\'">'.$value.'</td>';
			}
			if ($rcount == 4) break;
		}
		if (($row["order"]-1)> 0) $orderBt = '<a href="'.$orderLinkUp.'"  title="'.$GLOBALS["msg"]["MUP"].'"><img src="i/arr-up.gif" width="13" height="13"  /></a>';
		else $orderBt = '<img src="i/arr-up-n.gif" width="13" height="13"  />';
		if ($maxOrd == false) $orderBt .= '<a href="'.$orderLinkDown.'"  title="'.$GLOBALS["msg"]["MDOWN"].'"><img src="i/arr-down.gif" width="13" height="13" /></a>';
		else $orderBt .= '<img src="i/arr-down-n.gif" width="13" height="13" />';
		$output .= '
			<td class="rc"><a href="'.$activateLink.'"  title="'.$title_a.'"><img src="i/ico/'.$ico_a.'" width="10" height="13" /></a></td>
			<td class="rc">'.$orderBt.'</td>
			<td class="rc">
				<a href="'.$editLink.'"  title="'.$GLOBALS["msg"]["EDIT"].'"><img src="i/ico/item-edit.gif" width="12" height="11" /></a>&nbsp;|&nbsp;
				<a href="'.$deleteLink.'" title="'.$GLOBALS["msg"]["DEL"].'" onclick="return confirm(\''.$GLOBALS["msg"]["DEL_C"].'\');"><img src="i/ico/item-delete.gif" width="12" height="11" /></a>
			</td>
		';
		$cls = "row".(($count%2)+1);
		return "
	<tr class=\"".$cls."\" onmouseover=\"this.className='rowOver';\" onmouseout=\"this.className='".$cls."';\">".$output."
	</tr>";
	}

	function ModulLangSpliter($count){
		$cls = "row".(($count%2)+1);
		return '
			<tr class="'.$cls.'"><td colspan="20" class="split"><img src="i/pix.gif" width="1" height="1" alt="splitter" /></td></tr>';
	}

	function ModulLinkDataRow($modul,$row,$count,$items){
		$editLink = "javascript:linkItem('".$modul."',".$row["row_id"].");";
		$output = '
			<td><a href="'.$editLink.'"><img src="i/ico/checkbox-n.gif" width="16" height="16" id="check'.$modul.'-'.$row["row_id"].'" /></a></td>			
			';
		$output .= '
			<td>'.$row["row_id"].'</td>			
			';
		$rcount = 0;
		foreach ($items as $r){
			$rcount++;
			if (($r[2] != "Option") && ($r[2] != "Linked")){//&& ($r[2] != "CheckBox")
				switch($r[2]){
					case "CheckBox":
						$value = "ne";
						if ($row[$r[1]] == "1") $value = "ano";						
						break;
					case "DateInp":
						$value = "";
						if (($row[$r[1]] != "") && ($row[$r[1]] != "0000-00-00")){
							list($year, $month, $day) = explode("-", $row[$r[1]]);
							$value = ($day+0).".".($month+0).".".$year;
						}
						break;
					case "Text":
					case "ShortText":
						$value = shortenString($row[$r[1]],30);
						break;
					default:
						$value = $row[$r[1]];
						break;
				}
						$output .= '
				<td>'.$value.'</td>';
			}
			if ($rcount == 4) break;
		}
		$cls = "row".(($count%2)+1);
		return "
	<tr class=\"".$cls."\" onmouseover=\"this.className='rowOver';\" onmouseout=\"this.className='".$cls."';\">".$output."
	</tr>";
	}

	function AdminPreviewModulItem($modul,$id,$name){
		$output = '
		<div class="linkedItemRow" id="p_'.$modul.'x'.$id.'">
			<div style="float:left;width:auto;display:inline;">'.$name.'</div>
			<div style="float:right;width:100px;display:inline;"><a href="javascript:linkOrder(\''.$id.'\',\''.$modul.'\',-1);" title="'.$GLOBALS["msg"]["FWD"].'"><img src="i/ico/up.gif" width="11" height="11" /></a>&nbsp;|&nbsp;
			<a href="javascript:linkOrder(\''.$id.'\',\''.$modul.'\',1);" title="'.$GLOBALS["msg"]["BCKWD"].'"><img src="i/ico/down.gif" width="11" height="11" /></a>&nbsp;|&nbsp;
			<a href="javascript:linkDelete(\''.$id.'\',\''.$modul.'\');" title="'.$GLOBALS["msg"]["UNLINK"].'"><img src="i/ico/link-break.gif" width="15" height="12" /></a></div>
			<div class="clear"></div>
		</div>';
		return $output;
	}

	function ModulListTable($data){
		$output = "
		<table cellpadding=\"0\" cellspacing=\"0\" class=\"modulTableList\">
			".$data."
		</table>
		";

		return $output;
	}

	/*
		-----------------------------------------------
		MODUL TEMPLATES

		////////////////////////////////////////////////////////////////////////////////////////
		
		FOLDER TREE TEMPLATES
		-----------------------------------------------
	*/

	function Folder($data,$type){
		return '
		<div class="folderTree '.$type.'">
			<div class="listLine"><img src="i/list-top.gif" width="280" height="1" /></div>
			<div class="folderPadd">'.$data.'</div>
			<div class="listLine"><img src="i/list-top.gif" width="280" height="1" /></div>
		</div>
		';
	}

	function FolderTree($type,$tree,$id,$ajax,$subId=''){
		$output = "";
		foreach ($tree as $branch){
			if ($branch[3] == $id){
				$nameid = urlize($branch[1]).$branch[2];
				$cls = "";
				$fId = 0;
				if (isSet($_GET["folder_id"])) $fId = $_GET["folder_id"];
				if (isSet($_POST["folder_id"])) $fId = $_POST["folder_id"];
				if (($fId== $branch[2]) || ($fId== $branch[2])) $cls=" class=\"activeBranch\"";
				$branchLink = "?type=".$type."&action=list&folder_id=".$branch[2];
				if ($ajax) $branchLink = "javascript:viewBranch('".$type.$subId."',".$branch[2].");";
				else $actionOver = " onmouseover=\"showEditBox('".$nameid."');\"";
				$output .= "
				<li class=\"".$branch[0]."\"><a href=\"".$branchLink."\"".$actionOver.$cls.">".$branch[1]."</a>";			
				if (!$ajax){
					$output .= "
					<div onmouseout=\"hideEditBox('".$nameid."');\" id=\"".$nameid."\" class=\"editBox hidden\">
						<a href=\"?type=".$type."&action=add_sub&folder_id=".$branch[2]."\" title=\"".$GLOBALS["msg"]["FOLD-ADD"]."\" onmouseover=\"showEditBox('".$nameid."');\"><img src=\"i/ico/folder-add.gif\" width=\"18\" height=\"15\" /></a>";
					if (($branch[2]+0) != 0){
					$output .= "
						<a href=\"?type=".$type."&action=edit&folder_id=".$branch[2]."\" title=\"".$GLOBALS["msg"]["FOLD-EDIT"]."\" onmouseover=\"showEditBox('".$nameid."');\"><img src=\"i/ico/folder-edit.gif\" width=\"18\" height=\"15\" /></a>";
					$output .= "
						<a href=\"?type=".$type."&action=del&folder_id=".$branch[2]."\" title=\"".$GLOBALS["msg"]["FOLD-DEL"]."\" onclick=\"return confirm('".$GLOBALS["msg"]["DEL_C"]."');\" onmouseover=\"showEditBox('".$nameid."');\"><img src=\"i/ico/folder-delete.gif\" width=\"18\" height=\"15\" /></a>";				
					}
					$output .= "
					</div>";
				}
				$output .= Templates::FolderTree($type,$tree,$branch[2],$ajax,$subId);
				$output .= "
				</li>";
			}
		}
		if ($output != "")
			$output = "<ul>".$output."</ul>";
		return $output;
	}

	function FolderContent($data){
		return '
		<div class="folderContent '.$type.'">
			'.$data.'
		</div>
		';
	}

	/*
		-----------------------------------------------
		FOLDER TREE TEMPLATES

		////////////////////////////////////////////////////////////////////////////////////////
		
		IMAGES TEMPLATES
		-----------------------------------------------
	*/


	function FolderListHeader($type,$id,$ajax=false){
		$output = '<div class="mHeadLine"><div class="left"><h2>'.$GLOBALS["menu"][strtoupper($type)].'</h2></div>';
		if (!$ajax)
			$output .= '<div class="itemAdd left">&nbsp;|&nbsp;<a href="?type='.$type.'&folder_id='.$id.'&action=list&do=new" class="add">'.$GLOBALS["msg"]["NEW"].'</a></div>&nbsp;';
		$output .= '<div class="clear"><img src="i/pix.gif" width="1" height="1" /></div></div>';
		return $output;
	}
		
		
	function ImageList($data,$ajax){
		return '<div id="linkedBranchData" class="dataList">'.$data.'&nbsp;<br /><div class="clear">&nbsp;</div></div><div class="clear">&nbsp;</div>';
	}

	function ImageBox($img,$title,$id,$folder_id,$subId,$ajax=false){
		if (file_exists($img)){
			list($iWidth,$iHeight,$iType) = getimagesize($img);
			if ($iHeight < 80){
				$padding = floor((80-$iHeight)/2);
				if ($padding > 0) $cls= ' style="padding-top:'.$padding.'px;padding-bottom:'.$padding.'px;"';
			}
		} else {
			$bImg = $img;
			$img = "i/broken-image.gif";
			$iWidth=$iHeight= 80;
		}
		$editLink = "index.php?type=images&action=list&folder_id=".$folder_id."&do=edit&id=".$id;
		$deleteLink = "index.php?type=images&action=list&folder_id=".$folder_id."&do=delete&id=".$id;
		$checkBox = "";
		if ($ajax) 
		{
			$editLink = "javascript:linkItem('images".$subId."',".$id.");";
			$checkBox = '<a href="'.$editLink.'"><img src="i/ico/checkbox-n.gif" width="16" height="16" id="checkimages'.$subId.'-'.$id.'" /></a>';
		} else {
			$delLink = '&nbsp;<a href="'.$deleteLink.'" title="'.$GLOBALS["msg"]["DEL"].'"><img src="i/ico/delete-bin.gif" width="12" height="14" alt="'.$GLOBALS["msg"]["DEL"].'" /></a>';
		}
		return '
			<div class="imageBox"><div class="imgThumb"'.$cls.'><a href="'.$editLink.'" title="'.$GLOBALS["msg"]["EDIT"].'"><img src="'.$img.'" width="'.$iWidth.'" height="'.$iHeight.'" alt="'.$bImg.'" /></a></div>'.$checkBox.'<a href="'.$editLink.'" title="'.$GLOBALS["msg"]["EDIT"].'">'.shortenString($title,30,'..').'</a>'.$delLink.'</div>';
	}
	
	function ImageDropBox($img,$title,$id){
		if (file_exists($img)){
			list($iWidth,$iHeight,$iType) = getimagesize($img);
			if ($iHeight < 80){
				$padding = floor((80-$iHeight)/2);
				if ($padding > 0) $cls= ' style="padding-top:'.$padding.'px;padding-bottom:'.$padding.'px;"';
			}
		} else {
			$bImg = $img;
			$img = "i/broken-image.gif";
			$iWidth=$iHeight= 80;
		}
		return '
			<div class="imageBox"><div class="imgThumb"'.$cls.'><img src="'.$img.'" id="id_'.$id.'" width="'.$iWidth.'" height="'.$iHeight.'" alt="'.$bImg.'" /></div>'.shortenString($title,50,'..').'</div>';
	}

	function ImageForm($data){
		$output = "
		<div class=\"imageForm\">".$data."</div>";
		return $output;
	}

	function AdminPreviewImage($id,$iPath,$subId,$modul,$uniq_id){
		$row_data = Modules::ModulDataById($modul,$uniq_id);
		$iData = Images::getImageLinkedDescription($id,$modul,$uniq_id,$row_data["lang"]);
		$desc = " - - -";
		if ($iData != -1){
			$desc = $iData["desc"];
		}
		if (file_exists($iPath)){
			list($iWidth,$iHeight,$iType) = getimagesize($iPath);
			if ($iHeight < 80){
				$padding = floor((80-$iHeight)/2);
				if ($padding > 0) $cls= ' style="padding-top:'.$padding.'px;padding-bottom:'.$padding.'px;"';
			}
		} else {
			$iPath = "i/broken-image.gif";
			$iWidth=$iHeight= 80;
		}
		$output = '
		<div class="imageBox" id="p_images'.$subId.'x'.$id.'">
			<div class="imgThumb"'.$cls.'><img src="'.$iPath.'" width="'.$iWidth.'" height="'.$iHeight.'" /></div>
			<a href="javascript:linkOrder(\''.$id.'\',\'images'.$subId.'\',-1);" title="'.$GLOBALS["msg"]["FWD"].'"><img src="i/ico/left.gif" width="12" height="11" /></a>&nbsp;|&nbsp;
			<a href="javascript:linkOrder(\''.$id.'\',\'images'.$subId.'\',1);" title="'.$GLOBALS["msg"]["BCKWD"].'"><img src="i/ico/right.gif" width="12" height="11" /></a>&nbsp;|&nbsp;
			<a href="javascript:linkDelete(\''.$id.'\',\'images'.$subId.'\');" title="'.$GLOBALS["msg"]["UNLINK"].'"><img src="i/ico/link-break.gif" width="15" height="13" /></a>			
		</div>';
		return $output;
		/*
		<div id="form_img'.$subId.'x'.$id.'" class="descInput hidden"><input type="text" value="'.(($desc!=" - - -")?$desc:"").'" id="input_img'.$subId.'x'.$id.'" />&nbsp;<a href="javascript:saveLinkDesc(\''.$id.'\',\'images\',\'img'.$subId.'\','.$modul.','.$uniq_id.');" title="'.$GLOBALS["msg"]["SAVE"].'"><img src="i/ico/action_save.gif" width="14" height="14" /></a></div>
			<div id="desc_img'.$subId.'x'.$id.'" class="descText"><a href="javascript:editLinkDesc(\''.$id.'\',\'img'.$subId.'\');" title="'.$GLOBALS["msg"]["EDIT_DESC"].'"><img src="i/ico/page_edit.gif" width="14" height="14" /></a>&nbsp;<span id="desct_img'.$subId.'x'.$id.'">'.$desc.'</span></div>
			*/
	}
	/*
		-----------------------------------------------
		IMAGES TREE TEMPLATES

		////////////////////////////////////////////////////////////////////////////////////////
		
		FILES TEMPLATES
		-----------------------------------------------
	*/
		function FileForm($data){
			$output = "
			<div class=\"fileForm\">".$data."</div>";
			return $output;
		}
	
		function FileList($data,$ajax){
			return '<div id="linkedBranchData" class="dataList">'.$data.'&nbsp;<br /><div class="clear">&nbsp;</div></div><div class="clear">&nbsp;</div>';
		}

		function FileBox($file,$original_name,$title,$id,$folder_id,$subId,$ajax=false){
			if (file_exists($file)){
				$editLink = "index.php?type=files&action=list&folder_id=".$folder_id."&do=edit&id=".$id;
				$deleteLink = "index.php?type=files&action=list&folder_id=".$folder_id."&do=delete&id=".$id;
				$checkBox = "&nbsp;";
				if ($ajax) 
				{
					$editLink = "javascript:linkItem('files".$subId."',".$id.");";
					$checkBox = '<a href="'.$editLink.'"><img src="i/ico/checkbox-n.gif" width="16" height="16" id="checkfiles'.$subId.'-'.$id.'" /></a>';
				} else {
					$editLinkBt = '&nbsp;<a href="'.$editLink.'" title="'.$GLOBALS["msg"]["EDIT"].'"><img src="i/ico/item-edit.gif" width="12" height="11" alt="'.$GLOBALS["msg"]["EDIT"].'" /></a>';
					$delLink = '&nbsp;<a href="'.$deleteLink.'" title="'.$GLOBALS["msg"]["DEL"].'"><img src="i/ico/delete-bin.gif" width="12" height="14" alt="'.$GLOBALS["msg"]["DEL"].'" /></a>';
				}
				$filetype = pathinfo($file);
				if (file_exists("i/ico/files/".$filetype["extension"].".gif")){
					$ico = "i/ico/files/".$filetype["extension"].".gif";
				} else {
					$ico = "i/ico/files/blank.gif";
				}
				$ico = '<img src="'.$ico.'" width="16" height="16" alt="'.$filetype.'" />';
				$filetype = ' / '.$filetype["extension"];
				$filesize = ' / '.Files::formatbytes(filesize ($file));
				return '
					<table class="fileBox" cellspacing="0">
						<tr><td width="30" style="text-align:center;">'.$ico.'</td><td><a href="'.$editLink.'">'.$original_name.'</a>'.$filetype.$filesize.'</td><td width="50">&nbsp;</td></tr><tr><td class="tbb">'.$checkBox.'</td><td class="tbb">'.shortenString($title,30,'..').'</td><td class="tbb">'.$editLinkBt.$delLink.'</td></tr>
					</table>';
			}

		}
		function FileBoxPreview($file,$original_name,$title,$id,$folder_id,$subId){
			if (file_exists($file)){
				$filetype = pathinfo($file);
				if (file_exists("i/ico/files/".$filetype["extension"].".gif")){
					$ico = "i/ico/files/".$filetype["extension"].".gif";
				} else {
					$ico = "i/ico/files/blank.gif";
				}
				$ico = '<img src="'.$ico.'" width="16" height="16" alt="'.$filetype.'" />';
				$filetype = ' / '.$filetype["extension"];
				$filesize = ' / '.Files::FormatBytes(filesize ($file));
				return '
					<div class="linkedFile" id="p_files'.$subId.'x'.$id.'">
					<table class="fileBox" cellspacing="0">
						<tr><td width="30" class="tbb" style="text-align:center;">'.$ico.'</td><td class="tbb">'.$original_name.$filetype.$filesize.'</td><td class="tbb" width="200"><em>'.shortenString($title,50,'..').'</em></td><td width="100" class="tbb"><a href="javascript:linkOrder(\''.$id.'\',\'files'.$subId.'\',-1);" title="'.$GLOBALS["msg"]["FWD"].'"><img src="i/ico/up.gif" width="12" height="11" /></a>&nbsp;|&nbsp;
			<a href="javascript:linkOrder(\''.$id.'\',\'files'.$subId.'\',1);" title="'.$GLOBALS["msg"]["BCKWD"].'"><img src="i/ico/down.gif" width="12" height="11" /></a>&nbsp;|&nbsp;
			<a href="javascript:linkDelete(\''.$id.'\',\'files'.$subId.'\');" title="'.$GLOBALS["msg"]["UNLINK"].'"><img src="i/ico/link-break.gif" width="15" height="13" /></a></td></tr>
					</table>
				</div>';
			}

		}
		/*
		-----------------------------------------------
		FILES TEMPLATES

		////////////////////////////////////////////////////////////////////////////////////////
		
		VIDEOS TEMPLATES
		-----------------------------------------------
		*/


	function VideoList($data,$ajax){
		return '<div id="linkedBranchData" class="dataList">'.$data.'&nbsp;<br /><div class="clear">&nbsp;</div></div><div class="clear">&nbsp;</div>';
	}

	function VideoBox($img,$title,$id,$folder_id,$subId,$ajax=false){
		if (file_exists($img)){
			list($iWidth,$iHeight,$iType) = getimagesize($img);
			if ($iHeight < 80){
				$padding = floor((80-$iHeight)/2);
				if ($padding > 0) $cls= ' style="padding-top:'.$padding.'px;padding-bottom:'.$padding.'px;"';
			}
		} else {
			$bImg = $img;
			$img = "i/broken-image.gif";
			$iWidth=$iHeight= 80;
		}
		$editLink = "index.php?type=videos&action=list&folder_id=".$folder_id."&do=edit&id=".$id;
		$deleteLink = "index.php?type=videos&action=list&folder_id=".$folder_id."&do=delete&id=".$id;
		$checkBox = "";
		if ($ajax) 
		{
			$editLink = "javascript:linkItem('videos".$subId."',".$id.");";
			$checkBox = '<a href="'.$editLink.'"><img src="i/ico/checkbox-n.gif" width="16" height="16" id="checkvideos'.$subId.'-'.$id.'" /></a>';
		} else {
			$delLink = '&nbsp;<a href="'.$deleteLink.'" title="'.$GLOBALS["msg"]["DEL"].'"><img src="i/ico/delete-bin.gif" width="12" height="14" alt="'.$GLOBALS["msg"]["DEL"].'" /></a>';
		}
		return '
			<div class="imageBox"><div class="imgThumb"'.$cls.'><a href="'.$editLink.'" title="'.$GLOBALS["msg"]["EDIT"].'"><img src="'.$img.'" width="'.$iWidth.'" height="'.$iHeight.'" alt="'.$bImg.'" /></a></div>'.$checkBox.'<a href="'.$editLink.'" title="'.$GLOBALS["msg"]["EDIT"].'">'.shortenString($title,30,'..').'</a>'.$delLink.'</div>';
	}
	
	function VideoDropBox($img,$title,$id){
		if (file_exists($img)){
			list($iWidth,$iHeight,$iType) = getimagesize($img);
			if ($iHeight < 80){
				$padding = floor((80-$iHeight)/2);
				if ($padding > 0) $cls= ' style="padding-top:'.$padding.'px;padding-bottom:'.$padding.'px;"';
			}
		} else {
			$bImg = $img;
			$img = "i/broken-image.gif";
			$iWidth=$iHeight= 80;
		}
		return '
			<div class="imageBox"><div class="imgThumb"'.$cls.'><img src="'.$img.'" id="id_'.$id.'" width="'.$iWidth.'" height="'.$iHeight.'" alt="'.$bImg.'" /></div>'.shortenString($title,50,'..').'</div>';
	}

	function VideoForm($data){
		$output = "
		<div class=\"videoForm\">".$data."</div>";
		return $output;
	}

	function AdminPreviewVideo($id,$iPath,$subId,$modul,$uniq_id){
		$row_data = Modules::ModulDataById($modul,$uniq_id);

		$desc = " - - -";
		$desc = $iData["desc"];

		if (file_exists($iPath)){
			list($iWidth,$iHeight,$iType) = getimagesize($iPath);
			if ($iHeight < 80){
				$padding = floor((80-$iHeight)/2);
				if ($padding > 0) $cls= ' style="padding-top:'.$padding.'px;padding-bottom:'.$padding.'px;"';
			}
		} else {
			$iPath = "i/broken-image.gif";
			$iWidth=$iHeight= 80;
		}
		$output = '
		<div class="imageBox" id="p_videos'.$subId.'x'.$id.'">
			<div class="imgThumb"'.$cls.'><img src="'.$iPath.'" width="'.$iWidth.'" height="'.$iHeight.'" /></div>
			<a href="javascript:linkOrder(\''.$id.'\',\'videos'.$subId.'\',-1);" title="'.$GLOBALS["msg"]["FWD"].'"><img src="i/ico/left.gif" width="12" height="11" /></a>&nbsp;|&nbsp;
			<a href="javascript:linkOrder(\''.$id.'\',\'videos'.$subId.'\',1);" title="'.$GLOBALS["msg"]["BCKWD"].'"><img src="i/ico/right.gif" width="12" height="11" /></a>&nbsp;|&nbsp;
			<a href="javascript:linkDelete(\''.$id.'\',\'videos'.$subId.'\');" title="'.$GLOBALS["msg"]["UNLINK"].'"><img src="i/ico/link-break.gif" width="15" height="13" /></a>			
		</div>';
		return $output;
	}

		/*
		-----------------------------------------------
		VIDEOS TEMPLATES

		////////////////////////////////////////////////////////////////////////////////////////
		
		URLS TEMPLATES
		-----------------------------------------------
	*/
		function UrlForm($data){
			$output = "
			<div class=\"urlForm\">".$data."</div>";
			return $output;
		}
	
		function UrlList($data,$ajax){
			return '<div id="linkedBranchData" class="dataList">'.$data.'&nbsp;<br /><div class="clear">&nbsp;</div></div><div class="clear">&nbsp;</div>';
		}

		function UrlBox($url_link,$title,$id,$folder_id,$subId,$ajax=false){
				$editLink = "index.php?type=urls&action=list&folder_id=".$folder_id."&do=edit&id=".$id;
				$deleteLink = "index.php?type=urls&action=list&folder_id=".$folder_id."&do=delete&id=".$id;
				$checkBox = "&nbsp;";
				if ($ajax) 
				{
					$editLink = "javascript:linkItem('urls".$subId."',".$id.");";
					$checkBox = '<a href="'.$editLink.'"><img src="i/ico/checkbox-n.gif" width="16" height="16" id="checkurls'.$subId.'-'.$id.'" /></a>';
				} else {
					$editLinkBt = '&nbsp;<a href="'.$editLink.'" title="'.$GLOBALS["msg"]["EDIT"].'"><img src="i/ico/item-edit.gif" width="12" height="11" alt="'.$GLOBALS["msg"]["EDIT"].'" /></a>';
					$delLink = '&nbsp;<a href="'.$deleteLink.'" title="'.$GLOBALS["msg"]["DEL"].'"><img src="i/ico/delete-bin.gif" width="12" height="14" alt="'.$GLOBALS["msg"]["DEL"].'" /></a>';
				}
				

				if ((strpos($url_link, "http") === false) && (strpos($url_link, "mailto") === false)) $url_link = "http://".$url_link;

				return '
					<table class="fileBox" cellspacing="0">
						<tr><td class="tbb" width="20">'.$checkBox.'</td><td class="tbb"><a href="'.$url_link.'" target="_blank">'.$url_link.'</a></td><td class="tbb" width="200">'.$title.'</td><td class="tbb" width="50">'.$editLinkBt.$delLink.'</td></tr>
					</table>';
			

		}
		function UrlBoxPreview($linkUrl,$title,$id,$folder_id,$subId){
				return '
					<div class="linkedUrl" id="p_urls'.$subId.'x'.$id.'">
					<table class="fileBox" cellspacing="0">
						<tr><td class="tbb"><a href="http://'.$linkUrl.'">'.$linkUrl.'</a></td><td class="tbb" width="200"><em>'.$title.'</em></td><td width="100" class="tbb"><a href="javascript:linkOrder(\''.$id.'\',\'urls'.$subId.'\',-1);" title="'.$GLOBALS["msg"]["FWD"].'"><img src="i/ico/up.gif" width="12" height="11" /></a>&nbsp;|&nbsp;
			<a href="javascript:linkOrder(\''.$id.'\',\'urls'.$subId.'\',1);" title="'.$GLOBALS["msg"]["BCKWD"].'"><img src="i/ico/down.gif" width="12" height="11" /></a>&nbsp;|&nbsp;
			<a href="javascript:linkDelete(\''.$id.'\',\'urls'.$subId.'\');" title="'.$GLOBALS["msg"]["UNLINK"].'"><img src="i/ico/link-break.gif" width="15" height="13" /></a></td></tr>
					</table>
				</div>';
		}


	/*
		-----------------------------------------------
		URLS TEMPLATES
		
		////////////////////////////////////////////////////////////////////////////////////////
		
		OTHER TEMPLATES
		-----------------------------------------------
	*/

	function DataContainer($data){
		//$output = "
		//<div class=\"container\">".$data."</div>";
		return $data;

	}

	function MenuItem($ico,$name,$action,$type,$last=false){
		if ($GLOBALS["type"] == $type)
			$cls = "Active";
		if ($last) {
			$output = '<div class="stripe"><img src="i/pix.gif" width="1" height="41" /></div><div class="menuBox right"><a href="?'.$action.'='.$type.'" class="menuItem" style="background-image: url(\'i/ico/'.$ico.'\');">'.$name.'</a></div><div class="stripe right">&nbsp;</div><div class="stripeBefore"><img src="i/pix.gif" width="1" height="41" /></div>';
		} else {
			$output = '<div class="stripe'.$cls.'"><img src="i/pix.gif" width="1" height="41" /></div><div class="menuBox'.$cls.'"><a href="?'.$action.'='.$type.'" class="menuItem" style="background-image: url(\'i/ico/'.$ico.'\');">'.$name.'</a></div>';
		}
		
		return $output;
	}

	function MainMenu($data){
		//$output = '<div class="menu"><div class="mBox">'.$data.'</div></div>';
		$output = '
	<div class="headline">
		<div class="logo"><h1>Databič<sup>1</sup></h1></div>		
		'.$data.'
	</div>
	<div class="clear"><img src="i/pix.gif" width="1" height="1" /></div>';
		return $output;
	}

	function Message($text){
		if ($text != "&nbsp;")
			return "<div class=\"msg\"><strong>".$text."</strong></div>";
	}

	function writeJS($data){
		$output = '
		<script type="text/javascript">
		<!--			
			'.$data.'
		//-->
		</script>';
		return $output;
	}

	/*
		-----------------------------------------------
		OTHER TEMPLATES
	*/

	/*
		----------------------------------------------
		LANGUAGES
	*/
	
	function LangListHeader(){
		$output = '<div class="mHeadLine"><div class="left"><h2>'.$GLOBALS["menu"]["LANG"].'</h2></div>';
		$output .= '<div class="itemAdd left">&nbsp;|&nbsp;<a href="?type=languages&do=new" class="add">'.$GLOBALS["msg"]["NEW"].'</a></div>&nbsp;';
		$output .= '<div class="clear"><img src="i/pix.gif" width="1" height="1" /></div></div>';
		return $output;
	}

	function LangDataRow($id,$row,$count){
		$ico_a = "item-active.gif";
		$title_a = $GLOBALS["msg"]["ACTIVE"];
		if ($row["active"]+0 != 1) {
			$ico_a = "item-disabled.gif";
			$title_a = $GLOBALS["msg"]["DEACTIVE"];
		}
		$editLink = 'index.php?type=languages&do=edit&id='.$row["id"];
		//<td>'.$row["id"].'</td>			
		$output = '			
			<td onclick="location.href=\''.$editLink.'\'">'.$row["lang_id"].'</td>			
			<td onclick="location.href=\''.$editLink.'\'">'.$row["lang_name"].'</td>			
			';
		$output .= '
			<td class="rc"><a href="index.php?type=languages&do=active&id='.$row["id"].'"  title="'.$title_a.'"><img src="i/ico/'.$ico_a.'" width="10" height="13" /></a></td>
			<td class="rc">
				<a href="'.$editLink.'"  title="'.$GLOBALS["msg"]["EDIT"].'"><img src="i/ico/item-edit.gif" width="12" height="11" /></a>&nbsp;|&nbsp;<a href="index.php?type=languages&do=delete&id='.$row["id"].'&lang_id='.$row["lang_id"].'" title="'.$GLOBALS["msg"]["DEL"].'" onclick="return confirm(\''.$GLOBALS["msg"]["DEL_C"].'\');"><img src="i/ico/item-delete.gif" width="12" height="11" /></a>
			</td>
		';
		$cls = "row".(($count%2)+1);
		return "
	<tr class=\"".$cls."\" onmouseover=\"this.className='rowOver';\" onmouseout=\"this.className='".$cls."';\">".$output."
	</tr>";
	}

	/*
		----------------------------------------------
		TEXTS
	*/
	function TextListHeader(){
		$output = '<div class="mHeadLine"><div class="left"><h2>'.$GLOBALS["menu"]["TXT"].'</h2></div>';
		$output .= '<div class="itemAdd left">&nbsp;|&nbsp;<a href="?type=text&do=new" class="add">'.$GLOBALS["msg"]["NEW"].'</a></div>&nbsp;';
		$output .= '<div class="clear"><img src="i/pix.gif" width="1" height="1" /></div></div>';
		return $output;
	}

	function TextDataRow($id,$row,$count){
		$editLink = 'index.php?type=text&do=edit&id='.$row["id"].'&lang_id='.$row["lang_id"];
		//<td>'.$row["id"].'</td>			
		$output = '			
			<td onclick="location.href=\''.$editLink.'\'">'.$row["id"].'</td>			
			<td onclick="location.href=\''.$editLink.'\'">'.shortenString($row["value"],100).'</td>
			<td onclick="location.href=\''.$editLink.'\'">'.$row["lang_id"].'</td>			
			';
		$output .= '
			<td class="rc">
				<a href="'.$editLink.'"  title="'.$GLOBALS["msg"]["EDIT"].'"><img src="i/ico/item-edit.gif" width="12" height="11" /></a>&nbsp;|&nbsp;<a href="index.php?type=text&do=delete&id='.$row["id"].'&lang_id='.$row["lang_id"].'" title="'.$GLOBALS["msg"]["DEL"].'" onclick="return confirm(\''.$GLOBALS["msg"]["DEL_C"].'\');"><img src="i/ico/item-delete.gif" width="12" height="11" /></a>
			</td>
		';
		$cls = "row".(($count%2)+1);
		return "
	<tr class=\"".$cls."\" onmouseover=\"this.className='rowOver';\" onmouseout=\"this.className='".$cls."';\">".$output."
	</tr>";
	}

	/*
		----------------------------------------------
		SITEMAP
	*/

	function SitemapHeader(){
		$output = '<div class="mHeadLine"><div class="left"><h2>'.$GLOBALS["menu"]["MAP"].'</h2></div>';
		$output .= '<div class="itemAdd left">&nbsp;|&nbsp;<a href="?type=webmap&do=new" class="add">'.$GLOBALS["msg"]["NEW"].'</a></div>&nbsp;<div class="clear"></div>';
		$output .= '</div><div class="hr"><img src="i/pix.gif" width="1" height="1" /></div>';
		return $output;
	}

	function treeNode($parentId,$id,$name){
		$parentId = $parentId-1; 
		$nid = $id-1;
		$output = '		sitemap.add('.$nid.','.$parentId.',\''.$name.'\',\'?type=webmap&amp;nodeId='.$id.'\');
		';
		return $output;
	}

	function webMap($nodes,$modulForm,$editForm,$nodeId,$fClass='hidden'){
		$output = '
			<div class="modulMenu">
				<div class="modulItem"><a href="javascript: sitemap.openAll();">otevřít mapu</a></div><div class="stripeModul"><img src="i/pix.gif" width="1" height="41" /></div>
				<div class="modulItem"><a href="javascript: sitemap.closeAll();">zavřít mapu</a></div><div class="stripeModul"><img src="i/pix.gif" width="1" height="41" /></div>
			';
		if ($nodeId != -1) {
		$output .= '<div class="modulItem"><a href="javascript: addModul();">Přidat modul:</a></div><div class="stripeModul"><img src="i/pix.gif" width="1" height="41" /></div>
						<div id="modulForm" class="hidden"><div class="modulItem formTop">'.$modulForm.'</div><div class="stripeModul"><img src="i/pix.gif" width="1" height="41" /></div>';
		}
		$output .= '
			</div>
			<div class="clear"></div></div>
			<div class="sitemap">
			<script type="text/javascript">
				<!--
				sitemap = new dTree(\'sitemap\');
				sitemap.config.submenu = true;
				';
		if ($nodeId == -1)
			$output .= 'sitemap.deleteHighlightCookie();';
		$output .= '
				'.$nodes.'
				document.write(sitemap);
				//-->
			</script>
			<div class="editForm '.$fClass.'">'.$editForm.'</div>
			</div>
		';
		return $output;
	}

	function nodeContent($data){
		return '
			<div class="nodeContent">
				'.$data.'
			</div>
		';
	}
	
	function addClearer(){
		return '<div class="clear"></div>';
	}

	function Splitter($h,$class,$color){
		if ($class != "fill"){
			return '<div class="splitter '.$class.'">'.$h.'</div>';
		} else {
			return '<div class="splitter '.$class.'" style="background-color:'.$color.';">'.$h.'</div>';
		}
	}
}