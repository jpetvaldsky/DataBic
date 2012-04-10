<?

class Templates {
	/*
		MODUL TEMPLATES
		-----------------------------------------------
	*/
	function ModulItem($ico,$name,$action,$modulName,$onSite=false){
		if ($GLOBALS["name"] == $modulName){
			$cls = ' class="active"';
		}
		$link = '?type=modul&name='.$modulName.'&action='.$action;
		
		if ($onSite !==false)
		{
			$link = '?type=webmap&nodeId='.$onSite.'&name='.$modulName.'&action='.$action;
		}
		$icon = '';
		if ($ico != '')
		{
			$icon = '<i class="icon-'.$ico.'"></i> ';
		}
		$output = '
				<li '.$cls.'><a href="'.$link.'">'.$icon.$name.'</a></li>';
				
		return $output;		
	}

	function ModulMenu($data){
		$output = '
        <div class="well" style="padding: 8px 0;">  	      
				  <ul class="nav nav-list">
				  	<li class="nav-header"><i class="icon-folder-open"></i> '.$GLOBALS["menu"]["MODUL"].'</li>
						'.$data.'			  
				  </ul>
			  </div>';
		//$output = '<div class="modulMenu">'.$data.'<div class="clear"></div></div>';//'<h1>'.	.'</h1>'.
		return $output;
	}
	
	function ModulSiteMapMenu($pageName,$data){
/*		$output = '
        <div class="well sitemap" style="padding-top: 8px; padding-bottom: 0px;">  	      
        <h6><i class="icon-folder-open"></i> '.$GLOBALS["menu"]["MODUL"].'</h6>
				  <ul class="nav nav-pills">				  	
						'.$data.'			   						
				  </ul>
			  </div>';*/
		$output = '
        <div class="well" style="padding: 8px 0;">  	      
				  <ul class="nav nav-list">
				  	<li class="nav-header"><strong>'.$pageName.'</strong>: <small>'.$GLOBALS["menu"]["MODUL"].'</small></li>
						'.$data.'			  
				  </ul>
			  </div>';
			  
		return $output;	
	}
	
	function AddModul($data)
	{
		return '
		<li class="divider"></li>
		<li class="nav-header">'.$GLOBALS["msg"]["ADD-MODUL"].'</li>	
		<li>'.$data.'</li>';
	}
	

	function ModulList($data){
		if ($data != '')
			return $data;
		else
			return '';
	}

	function ModulListHeader($name,$id,$onSite=false){
		$link = '?type=modul&name='.$id.'&action=new';
		if ($onSite !== false)
			$link = '?type=webmap&nodeId='.$onSite.'&name='.$id.'&action=new';
		
		/*
		$output = '<div class="mHeadLine"><div class="left"><h2>'.$name.'</h2></div>';
		$output .= '<div class="itemAdd left">&nbsp;|&nbsp;<a href="'.$link.'" class="add">'.$GLOBALS["msg"]["NEW"].'</a></div>&nbsp;';
		$output .= '<div class="clear"><img src="i/pix.gif" width="1" height="1" /></div></div>';
		*/
		$output = '
				<div class="page-header">  	      		
      		<div class="row-fluid">
		  	      <div class="span6">
  	      		<h2>'.$name.'</h2>
  	      	</div>
		  	      <div class="span6">
		  	      <a class="btn btn-primary pull-right" href="'.$link.'"><i class="icon-plus icon-white"></i> '.$GLOBALS['msg']['NEW'].'</a>		
  	      	</div>
  	      </div>
      	</div><!-- /.page-header -->';
		return $output;
	}

	function ModulHeadlineList($modul,$headers,$onSite=false,$centered=''){
		$d = "ASC";
		if ($_GET["d"] == "ASC") $d="DESC";
		foreach ($headers as $value){
			if ($onSite !== false)
				$sortLink = 'index.php?type=webmap&nodeId='.$onSite.'&name='.$modul.'&order='.$value[2].'&d='.$d;
			else
				$sortLink = 'index.php?type=modul&name='.$modul.'&order='.$value[2].'&d='.$d;
			if ($value[1] != "") $width= "style=\"width: ".$value[1]."px !important;\"";
			else $width="";
			$cls = "";
			if ($value[3] != "") $cls = " class=\"centered\"";
			$output .= "
			<th ".$width.$cls."><a href=\"".$sortLink."\">".$value[0]."</a></th>";
		}		
		$output .= "
			<th style=\"width: 100px !important;\" class=\"centered\">".$GLOBALS["msg"]["TOOLS"]."</th>";
		return "
	<thead>
		<tr>
			".$output."
		</tr>
	</thead>
	<tbody>";
	}

	function ModulDataRow($modul,$row,$count,$items,$onSite,$maxOrd=false,$drowRows=0){

		$ico_a = "icon-ok-sign";
		$title_a = $GLOBALS["msg"]["DEACTIVE"];
		if ($row["active"]+0 != 1) {
			$ico_a = "icon-remove-circle";
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
		$output = '';
		if ($drowRows > 0)
		{
		$output = '			
			<td rowspan="'.$drowRows.'" onclick="location.href=\''.$editLink.'\'">'.$row["order"].'</td>';
		}
		$output .= '
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
		$orderBt = '';
		if (($row["order"]-1)> 0) 
		{
			$orderBt = '<a href="'.$orderLinkUp.'" title="'.$GLOBALS["msg"]["MUP"].'" rel="tooltip"><i class="icon-arrow-up"></i></a>';
		}
		else
		{
			$orderBt .= '<i class="icon-arrow-up icon-white"></i>';
		}
		$orderBt .= ' | ';		
		if ($maxOrd == false) 
		{
			$orderBt .= '<a href="'.$orderLinkDown.'" title="'.$GLOBALS["msg"]["MDOWN"].'" rel="tooltip"><i class="icon-arrow-down"></i></a>';
		}
		else
		{
			$orderBt .= '<i class="icon-arrow-down icon-white"></i>';
		}
		$orderBt .= ' | ';
		$output .= '
			<td class="centered"><a href="'.$activateLink.'"  title="'.$title_a.'" rel="tooltip"><i class="'.$ico_a.'"></i></a></td>
			<td class="centered">
				'.$orderBt.'
				<a href="'.$editLink.'" title="'.$GLOBALS["msg"]["EDIT"].'" rel="tooltip"><i class="icon-edit"></i></a> | <a href="'.$deleteLink.'" title="'.$GLOBALS["msg"]["DEL"].'" rel="tooltip" onclick="return confirm(\''.$GLOBALS["msg"]["DEL_C"].'\');"><i class="icon-remove"></i></a>
			</td>
		';
		$cls = "row".(($count%2)+1);
		return "
	<tr class=\"".$cls."\" onmouseover=\"this.className='rowOver';\" onmouseout=\"this.className='".$cls."';\">".$output."
	</tr>";
	
	
	}
	
	function DrawFormColumns($columnData)
	{
		$spanValue = round(12/count($columnData));
		$output = '';
		foreach ($columnData as $c)
		{
			$output .= '
			<div class="span'.$spanValue.'">'.$c.'</div>
			<!-- /.span'.$spanValue.' -->
			';
		}
		if ($output != '') $output = '		
			<div class="row">'.$output.'</div>
		';
		return $output;
	}

	function ModulLangSpliter($count){
		$cls = "row".(($count%2)+1);
		/*return '
			<tr class="'.$cls.'"><td colspan="20" class="split"><img src="i/pix.gif" width="1" height="1" alt="splitter" /></td></tr>';*/
			return '';
	}

	function ModulLinkDataRow($modul,$row,$count,$items){
		$editLink = "javascript:linkItem('".$modul."',".$row["row_id"].");";
		$output = '
			<td><a href="'.$editLink.'"><img src="i/ico/checkbox-n.png" width="16" height="16" id="check'.$modul.'-'.$row["row_id"].'" /></a></td>			
			';
		$output .= '
			<td>'.$row["row_id"].'</td>			
			';
		$rcount = 0;
		foreach ($items as $r){
			$rcount++;
			if (($r[2] != "Option") && ($r[2] != "Linked") && ($r[2] != "Splitter")){//&& ($r[2] != "CheckBox")
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
		$output = '';
		if ($name != '')
		{
			$output = '
				<tr class="linkedItemRow" id="p_'.$modul.'x'.$id.'">
					<td class="centered">'.$id.'</td>
					<td>'.$name.'</td>
					<td class="centered">
						<a href="javascript:linkOrder(\''.$id.'\',\''.$modul.'\',-1);" title="'.$GLOBALS["msg"]["FWD"].'" rel="tooltip"><i class="icon-arrow-up"></i></a>&nbsp;|&nbsp;
						<a href="javascript:linkOrder(\''.$id.'\',\''.$modul.'\',1);" title="'.$GLOBALS["msg"]["BCKWD"].'" rel="tooltip"><i class="icon-arrow-down"></i></a>&nbsp;|&nbsp;
						<a href="javascript:linkDelete(\''.$id.'\',\''.$modul.'\');" title="'.$GLOBALS["msg"]["UNLINK"].'" rel="tooltip"><i class="icon-trash"></i></a>
					</td>
				</tr>
			';			
		}
		return $output;
	}
	
	function ModulPreviewTableList($data,$ajax){
			if ($data != '')
			{
			return '
			<table class="table table-condensed">
      	<thead>
      		<tr>
      			<th class="centered" style="width: 30px !important;">ID</th>
      			<th>Title</th>
      			<th class="centered" style="width: 120px !important;">&nbsp;</th>
      		</tr>
      	</thead>
      	<tbody>
				'.$data.'
      	</tbody>
      </table>';
      }
		}

	function ModulListTable($data){
		$output = "
		<table class=\"table table-striped table-condensed table-bordered\">
			".$data."
			</tbody>
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

	function FolderTree($type,$tree,$id,$ajax,$subId='',$firstLevel=false){
		$output = "";
		foreach ($tree as $branch){
			if ($branch[3] == $id){
				$nameid = urlize($branch[1]).$branch[2];
				$cls = "";
				$fId = 0;
				if (isSet($_GET["folder_id"])) $fId = $_GET["folder_id"];
				if (isSet($_POST["folder_id"])) $fId = $_POST["folder_id"];
				$icon = "icon-folder-close";
				if ($fId == $branch[2]) {
				 $cls=" class=\"active\"";
				 $icon = "icon-folder-open icon-white";
				}
				$branchLink = "?type=".$type."&action=list&folder_id=".$branch[2];
				if ($ajax) $branchLink = "javascript:viewBranch('".$type.$subId."',".$branch[2].");";
				else $actionOver = " onmouseover=\"showEditBox('".$nameid."');\"";
				$output .= "
				<li ".$cls."><a href=\"".$branchLink."\"".$actionOver."><i class=\"".$icon."\"></i> ".$branch[1]."</a>";			
				
				$output .= Templates::FolderTree($type,$tree,$branch[2],$ajax,$subId,false);
				$output .= "
				</li>";
			}
		}
		if ($output != "")
		{
			if ($firstLevel)
			{
			$output = '
			<div class="well" style="padding: 8px 0;">  	      
						  <ul class="nav nav-list">
						  	<li class="nav-header">'.$GLOBALS['msg']['LIB_FOLDERS'].'</li>
						  	'.$output.'
					  	</ul>
					  </div>';
			}
			else
			{
				$output = '
				<ul class="nav nav-list" style="padding-right: 0px;">
					'.$output.'
				</ul>';
				
			}
		}
		return $output;
	}
	
	function FolderOptions($res,$info,$type,$action)
	{
		$output = '
		<div class="well" style="padding: 8px 0;">						
							<ul class="nav nav-list">
						  	<li class="nav-header">'.$GLOBALS['msg']['FOLDER_DETAILS'].': </li>		  
						  	<li>
						  		Title: <strong>'.$info['name'].'</strong>
						  	</li>
						  	<!--<li>
						  		Image count: <strong>28</strong>
						  	</li>
						  	<li>
						  		Last modified: <strong>26.3.2012 17:58</strong>						  		
						  	</li>-->
						  	<li class="divider"></li>';
		if ($action == 'add_sub')
		{
			$output .= '<li class="active">
										<a href="?type='.$type.'&action=add_sub&folder_id='.$info["id"].'"><i class="icon-plus icon-white"></i> '.$GLOBALS["msg"]["FOLD-ADD"].'</a>
								</li>
						  	<li class="divider"></li>						  	
						  	<li>
									'.$res.'					  	
						  	</li>';
  			$output .= '<li class="divider"></li>';
		}
		else
		{
			$output .= '<li>
										<a href="?type='.$type.'&action=add_sub&folder_id='.$info["id"].'"><i class="icon-plus"></i> '.$GLOBALS["msg"]["FOLD-SUBADD"].'</a>
								</li>';
		}

		if ($action == 'edit')
		{
			$output .= '<li class="divider"></li>';
			$output .= '<li class="active">
										<a href="?type='.$type.'&action=edit&folder_id='.$info["id"].'" ><i class="icon-edit icon-white"></i> '.$GLOBALS["msg"]["FOLD-EDIT"].'</a>
								</li>
						  	<li class="divider"></li>						  	
						  	<li>
									'.$res.'					  	
						  	</li>';
		}
		else
		{
			$output .= '<li>
										<a href="?type='.$type.'&action=edit&folder_id='.$info["id"].'"><i class="icon-edit"></i> '.$GLOBALS["msg"]["FOLD-EDIT"].'</a>
								</li>';
		}		
		
		/*						  	
								<li class="active">
										<a href="#" rel="tooltip" data-original-title="Edit folder"><i class="icon-edit icon-white"></i> Edit folder</a>														
						  	</li>
						  	<li class="divider"></li>						  	
						  	<li>
									<form>
									  <label>Folder name:</label>
									  <input type="text" class="span3" placeholder="News 2">
				            <label for="select01">Parent folder:</label>
			              <select id="select01">
			                <option>Image library</option>
			                <option>2</option>
			                <option>3</option>
			                <option>4</option>
			                <option>5</option>
			              </select>

									  <button type="submit" class="btn btn-primary">Save</button>
									  <button type="submit" class="btn">Cancel</button>
									</form>						  	
						  	</li>	
								<li>
										<a href="#" rel="tooltip" data-original-title="Remove this folder"><i class="icon-remove-sign"></i> Remove this folder</a>
								</li>		*/
			$output .= '																			  	
						  </ul>
						</div>';
			
			return $output;
	}

	function FolderContent($data){
		return '
		<div class="folderContent '.$type.'">
			'.$data.'
		</div>
		';
	}

	function BreadcrumbNav($items)
	{
		$itemList = '';
		if (count($items) > 0)
		{
			foreach ($items as $i)
			{
				if ($i['is_last'])
				{
					$itemList .= '<li class="active">'.$i['name'].'</li>';
				}
				else
				{
					$itemList .= '<li><a href="'.$i['link'].'">'.$i['name'].'</a><span class="divider">/</span></li>';
				}
			}
		return '
		<ul class="breadcrumb">
			'.$itemList.'
		</ul>
';
		}
		else
		{
			return '';
		}
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
		//return '<div id="linkedBranchData" class="dataList">'.$data.'&nbsp;<br /><div class="clear">&nbsp;</div></div><div class="clear">&nbsp;</div>';
		if ($data != '')
			return '<ul class="thumbnails">'.$data.'</ul>';
		else
			return '<div class="well">'.$GLOBALS['msg']['EMPTY_FOLDER'].'</div>';
	}

	function ImageBox($img,$title,$id,$folder_id,$subId,$ajax=false){
		if (file_exists($img)){
			list($iWidth,$iHeight,$iType) = getimagesize($img);
			if ($iHeight < 100){
				$padding = floor((100-$iHeight)/2);
				if ($padding > 0) $cls= ' style="padding-top:'.$padding.'px;padding-bottom:'.$padding.'px;"';
			}
		} else {
			$bImg = $img;
			$img = "i/broken-image.gif";
			$iWidth=$iHeight= 100;
		}
		$editLink = "index.php?type=images&action=list&folder_id=".$folder_id."&do=edit&id=".$id;
		$deleteLink = "index.php?type=images&action=list&folder_id=".$folder_id."&do=delete&id=".$id;
		$checkBox = "";
		$tooltip = $GLOBALS["msg"]["EDIT"];
		$options = '';
		
		if ($ajax) 
		{
			$tooltip = "";
			$editLink = "javascript:linkItem('images".$subId."',".$id.");";
			$options = '<h5><a href="'.$editLink.'"><img src="i/ico/checkbox-n.png" width="16" height="16" id="checkimages'.$subId.'-'.$id.'" /> '.shortenString($title,20,'..').'</a></h5>';
		} else {
			$options = '<h5>'.shortenString($title,30,'..').'</h5>';
			$options .= '<a href="#" rel="tooltip" title="unlink"><i class=" icon-trash"></i> '.$GLOBALS["msg"]["DELETE"].'</a>';
			//'&nbsp;<a href="'.$deleteLink.'" title="'.$GLOBALS["msg"]["DEL"].'"><img src="i/ico/delete-bin.gif" width="12" height="14" alt="'.$GLOBALS["msg"]["DEL"].'" /></a>';
		}
		/*
		return '
			<div class="imageBox"><div class="imgThumb"'.$cls.'><a href="'.$editLink.'" title="'.$GLOBALS["msg"]["EDIT"].'"><img src="'.$img.'" width="'.$iWidth.'" height="'.$iHeight.'" alt="'.$bImg.'" /></a></div>'.$checkBox.'<a href="'.$editLink.'" title="'.$GLOBALS["msg"]["EDIT"].'">'.shortenString($title,30,'..').'</a>'.$delLink.'</div>';
			*/
		return '
		<li>
			<div class="thumbnail">
  			<a href="'.$editLink.'" title="'.$tooltip.'" rel="tooltip" class="imageThumb"><span '.$cls.'><img src="'.$img.'" width="'.$iWidth.'" height="'.$iHeight.'" alt="'.$bImg.'" /></span></a>
  			<div class="caption">  				
					'.$options.'
  			</div>
  		</div>
 		</li>';
	}
	
	// JUST IMAGE PREVIEW NO OPTIONS
	function ImageDropBox($img,$title,$id){
		if (file_exists($img)){
			list($iWidth,$iHeight,$iType) = getimagesize($img);
			if ($iHeight < 100){
				$padding = floor((100-$iHeight)/2);
				if ($padding > 0) $cls= ' style="padding-top:'.$padding.'px;padding-bottom:'.$padding.'px;"';
			}
		} else {
			$bImg = $img;
			$img = "i/broken-image.gif";
			$iWidth=$iHeight= 100;
		}
		return '
			<li>
				<div class="thumbnail">
					<div class="imageThumb">
						<span '.$cls.'><img src="'.$img.'" id="id_'.$id.'" width="'.$iWidth.'" height="'.$iHeight.'" alt="'.$bImg.'" /></span>
					</div>
					<div class="caption">
				  	<h5>'.shortenString($title,50,'..').'</h5>
				  </div>
				</div>
			</li>';
	}

	function ImageForm($data){
		$output = "
		<div class=\"imageForm\">".$data."</div>";
		return $output;
	}

	// PREVIEW FOR DISPLAY IMAGES ATTACHED TO RECORD
	function AdminPreviewImage($id,$iPath,$subId,$modul,$uniq_id){
		$row_data = Modules::ModulDataById($modul,$uniq_id);
		$iData = Images::getImageLinkedDescription($id,$modul,$uniq_id,$row_data["lang"]);
		$desc = $row_data['title'];
		if ($iData != -1){
			$desc = $iData["desc"];
		}
		if (file_exists($iPath)){
			list($iWidth,$iHeight,$iType) = getimagesize($iPath);
			if ($iHeight < 100){
				$padding = floor((100-$iHeight)/2);
				if ($padding > 0) 
					$cls= ' style="padding-top:'.$padding.'px;padding-bottom:'.$padding.'px;"';
			}
		} else {
			$iPath = "i/broken-image.gif";
			$iWidth=$iHeight= 100;
		}

		return '
		<li id="p_images'.$subId.'x'.$id.'">
			<div class="thumbnail">
  			<div class="imageThumb"><span '.$cls.'><img src="'.$iPath.'" width="'.$iWidth.'" height="'.$iHeight.'" alt="'.$bImg.'" /></span></div>
  			<div class="caption">
  				<!--<h5>'.shortenString($desc,30,'..').'</h5>-->
  				<a href="javascript:linkOrder(\''.$id.'\',\'images'.$subId.'\',-1);" rel="tooltip" title="'.$GLOBALS["msg"]["FWD"].'"><i class="icon-arrow-left"></i></a>&nbsp;|&nbsp;
  				<a href="javascript:linkOrder(\''.$id.'\',\'images'.$subId.'\',1);" rel="tooltip" title="'.$GLOBALS["msg"]["BCKWD"].'"><i class="icon-arrow-right"></i></a>&nbsp;|&nbsp;
  				<a href="javascript:linkDelete(\''.$id.'\',\'images'.$subId.'\');" rel="tooltip" title="'.$GLOBALS["msg"]["UNLINK"].'"><i class=" icon-trash"></i></a>
  			</div>
  		</div>
  		</li>';
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
			//return '<div id="linkedBranchData" class="dataList">'.$data.'&nbsp;<br /><div class="clear">&nbsp;</div></div><div class="clear">&nbsp;</div>';
			if ($data != '')
			{
			return '
			<table class="table table-condensed">
      	<thead>
      		<tr>
      			<th style="width: 30px !important;">&nbsp;</th>
      			<th>Filename</th>
      			<th>Type</th>
      			<th>Size</th>
      			<th>Title</th>
      			<th class="centered">&nbsp;</th>
      		</tr>
      	</thead>
      	<tbody>
				'.$data.'
      	</tbody>
      </table>';
      }
      else
      {
      	return '<div class="well">'.$GLOBALS['msg']['EMPTY_FOLDER'].'</div>';
      }
		}

		function FileBox($file,$original_name,$title,$id,$folder_id,$subId,$ajax=false){
			if (file_exists($file)){
				$editLink = "index.php?type=files&action=list&folder_id=".$folder_id."&do=edit&id=".$id;
				$deleteLink = "index.php?type=files&action=list&folder_id=".$folder_id."&do=delete&id=".$id;
				$ico = "&nbsp;";
				$filetype = pathinfo($file);
				$buttons = '&nbsp;';
				$tooltip = '';
				if ($ajax) 
				{
					$editLink = "javascript:linkItem('files".$subId."',".$id.");";
					$ico = '<a href="'.$editLink.'"><img src="i/ico/checkbox-n.png" width="16" height="16" id="checkfiles'.$subId.'-'.$id.'" /></a>';
					$original_name = shortenString($original_name,30,'..');
				} else {
					$editLinkBt = '&nbsp;<a href="'.$editLink.'" title="'.$GLOBALS["msg"]["EDIT"].'"><img src="i/ico/item-edit.gif" width="12" height="11" alt="'.$GLOBALS["msg"]["EDIT"].'" /></a>';
					$delLink = '&nbsp;<a href="'.$deleteLink.'" title="'.$GLOBALS["msg"]["DEL"].'"><img src="i/ico/delete-bin.gif" width="12" height="14" alt="'.$GLOBALS["msg"]["DEL"].'" /></a>';

					if (file_exists("i/ico/files/".$filetype["extension"].".gif")){
						$ico = "i/ico/files/".$filetype["extension"].".gif";
					} else {
						$ico = "i/ico/files/blank.gif";
					}		
					$ico = '<img src="'.$ico.'" width="16" height="16" alt="'.$filetype.'" />';			
					$tooltip = ' title="'.$GLOBALS["msg"]["EDIT"].'" rel="tooltip"';
					$buttons = '<a href="'.$editLink.'" rel="tooltip" title="'.$GLOBALS["msg"]["EDIT"].'"><i class="icon-edit"></i></a>';
					$buttons .= '&nbsp;|&nbsp;';
  				$buttons .= '<a href="'.$deleteLink.'" rel="tooltip" title="'.$GLOBALS["msg"]["DEL"].'"><i class=" icon-trash"></i></a>';
				}
				
				
				$filetype = $filetype["extension"];
				$filesize = Files::formatbytes(filesize ($file));
			return '
			<tr>
  			<td class="centered">'.$ico.'</td>
  			<td><a href="'.$editLink.'"'.$tooltip.'>'.$original_name.'</a></td>
  			<td>'.$filetype.'</td>
  			<td>'.$filesize.'</td>
  			<td>'.shortenString($title,50,'..').'</td>
  			<td class="centered">'.$buttons.'</td>
  		</tr>';
				return '';				
			}
			return '';
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
				$filetype = $filetype["extension"];
				$filesize = Files::FormatBytes(filesize ($file));
			return '
			<tr class="linkedFile" id="p_files'.$subId.'x'.$id.'";>
  			<td class="centered">'.$ico.'</td>
  			<td>'.$original_name.'</td>
  			<td>'.$filetype.'</td>
  			<td>'.$filesize.'</td>
  			<td>'.shortenString($title,50,'..').'</td>
  			<td class="centered"><a href="javascript:linkOrder(\''.$id.'\',\'files'.$subId.'\',-1);" rel="tooltip" title="'.$GLOBALS["msg"]["FWD"].'"><i class="icon-arrow-up"></i></a>&nbsp;|&nbsp;
  				<a href="javascript:linkOrder(\''.$id.'\',\'files'.$subId.'\',1);" rel="tooltip" title="'.$GLOBALS["msg"]["BCKWD"].'"><i class="icon-arrow-down"></i></a>&nbsp;|&nbsp;
  				<a href="javascript:linkDelete(\''.$id.'\',\'files'.$subId.'\');" rel="tooltip" title="'.$GLOBALS["msg"]["UNLINK"].'"><i class=" icon-trash"></i></a></td>
  		</tr>';
			}
			return  '';

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
			$checkBox = '<a href="'.$editLink.'"><img src="i/ico/checkbox-n.png" width="16" height="16" id="checkvideos'.$subId.'-'.$id.'" /></a>';
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
					$checkBox = '<a href="'.$editLink.'"><img src="i/ico/checkbox-n.png" width="16" height="16" id="checkurls'.$subId.'-'.$id.'" /></a>';
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
	
	function MenuItemNew($ico,$name,$action,$type,$last=false){
		if ($GLOBALS["type"] == $type)
			$cls = "active";

		$output = '<li class="'.$cls.'"><a href="?'.$action.'='.$type.'"><i class="'.$ico.'"></i> '.$name.'</a></li>';		
		return $output;
	}
	


	function MainMenu($data){
		//$output = '<div class="menu"><div class="mBox">'.$data.'</div></div>';
		$output = '
		<div class="container-fluid">
			<div class="subhead" id="overview">
				<div class="subnav">
			    <ul class="nav nav-pills">
			    	'.$data.'
			    </ul>
			  </div>
			</div>
		</div>';
		return $output;
	}

	function Message($text,$header='',$cls='alert-info'){
		if ($text != "&nbsp;"){
			if ($header != '')
			{
				$header = "<strong>".$header."</strong><br />";
			}
			return "<div class=\"container-fluid\"><div class=\"alert ".$cls."\">".$header.$text."</div></div>";
		}
		return '';
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

	function topHeader($title,$link,$subtitle='')
	{
		if ($link != '')
			$output = '<h2><a href="'.$link.'">'.$title.'</a>';
		else
			$output = '<h2>'.$title;
		if ($subtitle != '')
		{
			$output .= '&gt; <small>'.$subtitle.'</small>';
		}
		$output .= '</h2>';
		return $output;
	}

	function mainContainerTemplate($header,$link,$content='',$sidebar)
	{
		if ($sidebar != "")
		{
			$output = '
			<div class="container-fluid">
				<div class="row-fluid">
		      <div class="span3">
		      '.$sidebar.'
		      &nbsp;
					</div>
		      <div class="span9">';
			if ($header != '')
			{
				$output .= '					      
		      	<div class="page-header">  	      		
		      		<div class="row-fluid">
				  	      <div class="span6">
		  	      		'.$header.'
		  	      	</div>
				  	      <div class="span6">
				  	      <a class="btn btn-primary pull-right" href="'.$link.'"><i class="icon-plus icon-white"></i> '.$GLOBALS['msg']['NEW'].'</a>		
		  	      	</div>
		  	      </div>
		      	</div><!-- /.page-header -->';
			}
			$output .= $content;
			$output .= '
					</div>
				</div><!-- /.row-fluid -->
			</div><!-- /.container-fluid -->';
		}
		else
		{
			$output = '
			<div class="container">
      	<div class="page-header">  	      		
      		<div class="row-fluid">
		  	      <div class="span6">
  	      		'.$header.'
  	      	</div>
		  	      <div class="span6">
		  	      <a class="btn btn-primary pull-right" href="'.$link.'"><i class="icon-plus icon-white"></i> '.$GLOBALS['msg']['NEW'].'</a>		
  	      	</div>
  	      </div>
      	</div><!-- /.page-header -->';
			$output .= $content;
			$output .= '
			</div><!-- /.container -->';		
		}
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

	function LangDataRow($id,$row,$count){

		$ico_a = "icon-ok-sign";
		$title_a = $GLOBALS["msg"]["DEACTIVE"];
		if ($row["active"]+0 != 1) {
			$ico_a = "icon-remove-circle";
			$title_a = $GLOBALS["msg"]["ACTIVE"];
		}


		$editLink = 'index.php?type=languages&do=edit&id='.$row["id"];
		//<td>'.$row["id"].'</td>			
		$output = '			
			<td onclick="location.href=\''.$editLink.'\'" class="centered">'.$row["lang_id"].'</td>			
			<td onclick="location.href=\''.$editLink.'\'">'.$row["lang_name"].'</td>			
			';
		$output .= '
			<td class="centered"><a href="index.php?type=languages&do=active&id='.$row["id"].'"  title="'.$title_a.'" rel="tooltip"><i class="'.$ico_a.'"></i></a></td>
			<td class="centered">
				<a href="'.$editLink.'"  title="'.$GLOBALS["msg"]["EDIT"].'" rel="tooltip"><i class="icon-edit"></i></a>&nbsp;|&nbsp;<a href="index.php?type=languages&do=delete&id='.$row["id"].'&lang_id='.$row["lang_id"].'" title="'.$GLOBALS["msg"]["DEL"].'" onclick="return confirm(\''.$GLOBALS["msg"]["DEL_C"].'\');" rel="tooltip"><i class="icon-remove"></i></a>
			</td>
		';
		$cls = "row".(($count%2)+1);
		return "
	<tr>
		".$output."
	</tr>";
	}

	/*
		----------------------------------------------
		TEXTS
	*/

	function TextDataRow($id,$row,$count,$dropRows=0){
		$editLink = 'index.php?type=text&do=edit&id='.$row["id"].'&lang_id='.$row["lang_id"];
		//<td>'.$row["id"].'</td>			
		if ($dropRows != 0)
		{
		$output = '			
			<td class="centered" rowspan="'.$dropRows.'" onclick="location.href=\''.$editLink.'\'">'.$row["id"].'</td>';
		}
		if ($row["value"] != '')
		{
		$output .= '		
			<td onclick="location.href=\''.$editLink.'\'">'.shortenString($row["value"],100).'</td>';
		}
		else
		{
			$output .= '		
			<td onclick="location.href=\''.$editLink.'\'" class="emptyValue">'.$GLOBALS["msg"]["EMPTY_EDIT"].'</td>';
		}
		$output .= '
			<td class="centered" onclick="location.href=\''.$editLink.'\'">'.$row["lang_id"].'</td>			
			';
		$output .= '
			<td class="centered">
				<a href="'.$editLink.'"  title="'.$GLOBALS["msg"]["EDIT"].'" rel="tooltip"><i class="icon-edit"></i></a>&nbsp;|&nbsp;<a href="index.php?type=text&do=delete&id='.$row["id"].'&lang_id='.$row["lang_id"].'" title="'.$GLOBALS["msg"]["DEL"].'" onclick="return confirm(\''.$GLOBALS["msg"]["DEL_C"].'\');" rel="tooltip"><i class="icon-remove"></i></a>
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


	function treeNode($parentId,$id,$name){
		$parentId = $parentId-1; 
		$nid = $id-1;
		$output = '		sitemap.add('.$nid.','.$parentId.',\''.$name.'\',\'?type=webmap&amp;nodeId='.$id.'\');
		';
		return $output;
	}

	function webMap($nodes,$modulForm,$editForm,$nodeId,$fClass='hidden'){
		$output = '
			<div class="well" style="padding: 8px 0; ">
				<ul class="nav nav-list">
			  	<li class="nav-header">'.$GLOBALS['msg']['SITEMAP_OPTIONS'].'</li>
							<li><a href="#" onclick="sitemap.openAll();" title="'.$GLOBALS['msg']['SITEMAP_OPEN'].'"><i class="icon-zoom-in"></i> '.$GLOBALS['msg']['SITEMAP_OPEN'].'</a></li>
							<li><a href="#" onclick="sitemap.closeAll();" title="'.$GLOBALS['msg']['SITEMAP_CLOSE'].'"><i class="icon-zoom-out"></i> '.$GLOBALS['msg']['SITEMAP_CLOSE'].'</a></li>
		  				<li><a href="?type=webmap&amp;do=new" title="'.$GLOBALS['msg']['PAGE_ADD'].'"><i class="icon-plus-sign"></i> '.$GLOBALS['msg']['PAGE_ADD'].'</a></li>
					  </ul>

			';
		/*
		if ($nodeId != -1) {
		$output .= '<div class="modulItem"><a href="javascript: addModul();">Přidat modul:</a></div><div class="stripeModul"><img src="i/pix.gif" width="1" height="41" /></div>
						<div id="modulForm" class="hidden"><div class="modulItem formTop">'.$modulForm.'</div><div class="stripeModul"><img src="i/pix.gif" width="1" height="41" /></div>';
		}
		*/
		$output .= '
			<hr />
			'.Templates::sitemapNodeHeader($GLOBALS['menu']['MAP']).'
			<div class="sitemap" style=" padding: 0px 15px;">
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
			</div><!-- /.sitemap -->
			';
			if ($editForm != '')
			{
				$output .='
				<hr />
				'.$editForm.'
						';
			}
		$output .= '
			</div><!-- /.well -->
		';
		return $output;
	}
	
	function sitemapNodeHeader($title)
	{
		return '			
			<ul class="nav nav-list">
			  	<li class="nav-header">'.$title.'</li>
		  </ul>			
		';
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
		/*
		if ($class != "fill"){
			return '<div class="splitter '.$class.'">'.$h.'</div>';
		} else {
			return '<div class="splitter '.$class.'" style="background-color:'.$color.';">'.$h.'</div>';
		}*/
		return '<h4>'.$h.'</h4><hr />';
	}
	
	function TwoColumnsFluid($c1,$c2,$s1,$s2,$pullRight=false)
	{
		if ($pullRight)
		{
			$c2 = "<div class=\"pull-right\">".$c2."</div>";
		}
		return
		'<br>
		<div class="row-fluid">
      <div class="span'.$s1.'">'.$c1.'</div>
      <div class="span'.$s2.'">'.$c2.'</div>
    </div>
		';
	}
}