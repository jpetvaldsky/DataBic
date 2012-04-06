<?

class Forms {

	function Form($data,$legend=''){
		$output = '
			<form enctype="multipart/form-data" action="index.php" method="post" class="form-horizontal">
				<fieldset>';
		if ($legend != '')
		{
			$output .= '
					<legend>'.$legend.'</legend>';
					
		}
		$output .= '		
				'.$data.'
				</fieldset>
			</form>';
		return $output;
	}
	
	function FormSimple($data,$legend=''){
		$output = '
			<form enctype="multipart/form-data" action="index.php" method="post" class="form-vertical">
				<fieldset>';
		if ($legend != '')
		{
			$output .= '
					<legend>'.$legend.'</legend>';
					
		}
		$output .= '		
				'.$data.'
				</fieldset>
			</form>';
		return $output;
	}


	function getForm($formName,$name,$value,$length,$desc,$check=false,$disabled=false){		
		switch ($formName){
			case "TextRow":
				return Forms::TextRow($name,$value,$length,$desc,"iText",$disabled);
				break;
			case "Number":
			case "FloatNum":
				return Forms::Number($name,$value,$length,$desc,$disabled);
				break;
			case "Year":
				return Forms::Year($name,$value,$length,$desc,$disabled);
				break;
			case "DateInp":
			case "DateTime":
				return Forms::DateInp($name,$value,$length,$desc,$disabled);
				break;
			case "TimeStamp":
				return Forms::TimeStamp($name,$value,$length,$desc,$disabled);
				break;
			case "CheckBox":
				return Forms::CheckBox($name,$value,$length,$desc,$check);
				break;			
			case "TwoLines":
				return Forms::TwoLines($name,$value,$length,$desc);
				break;
			case "ShortText":
				return Forms::ShortText($name,$value,$length,$desc);
				break;
			case "Text":
				return Forms::Text($name,$value,$length,$desc);
				break;
			default:
				break;
		}
	}
	
	function formActions($buttons)
	{
		return
		'
			<div class="form-actions">		
				'.$buttons.'
			</div>
		';
	}

	function TextRow($name,$value,$length,$desc,$class="iText",$disabled){
		$dis = "";
		if ($disabled) 	$dis = ' disabled';
		return '
					<div class="control-group">
						<label class="control-label" for="i'.$name.'">'.$desc.': </label>
            <div class="controls">
              <input type="text" class="input-xlarge'.$dis.'" id="i'.$name.'" name="'.$name.'" value="'.htmlspecialchars($value).'" length="'.$length.'" '.$dis.'>			              
            </div>
          </div>';
		
	}

	function Number($name,$value,$length,$desc,$disabled){
		$dis = "";
		if ($disabled) 	$dis = ' disabled';
		return '
					<div class="control-group">
						<label class="control-label" for="i'.$name.'">'.$desc.': </label>
            <div class="controls">
              <input type="text" class="input-medium'.$dis.'" id="i'.$name.'" name="'.$name.'" value="'.htmlspecialchars($value).'" length="'.$length.'" '.$dis.'>			              
            </div>
          </div>';
	}

	function Year($name,$value,$length,$desc,$disabled){
		$dis = "";
		if ($disabled) 	$dis = ' disabled';
		return '
					<div class="control-group">
						<label class="control-label" for="i'.$name.'">'.$desc.': </label>
            <div class="controls">
              <input type="text" class="input-small'.$dis.'" id="i'.$name.'" name="'.$name.'" value="'.htmlspecialchars($value).'" length="'.$length.'" '.$dis.'>			              
            </div>
          </div>';
	}

	function DateInp($name,$value,$length,$desc,$disabled){
		$dis = "";
		if ($disabled) 	$dis = ' disabled';
		return '
					<div class="control-group">
						<label class="control-label" for="i'.$name.'">'.$desc.': </label>
            <div class="controls">
              <input type="text" class="input-medium'.$dis.'" id="i'.$name.'" name="'.$name.'" value="'.htmlspecialchars($value).'" length="'.$length.'" '.$dis.'>			              
            </div>
          </div>';
	}

	function TimeStamp($name,$value,$length,$desc,$disabled){
		$dis = "";
		if ($disabled) 	$dis = ' disabled';
		return '
					<div class="control-group">
						<label class="control-label" for="i'.$name.'">'.$desc.': </label>
            <div class="controls">
              <input type="text" class="input-medium'.$dis.'" id="i'.$name.'" name="'.$name.'" value="'.htmlspecialchars($value).'" length="'.$length.'" '.$dis.'>			              
            </div>
          </div>';
	}

	function CheckBox($name,$value,$length,$desc,$check){
		if ($check) $checked = ' checked';
		/*
		return '
		<div class="label" for="i'.$name.'">'.$desc.': </div><input type="checkbox" id="i'.$name.'" name="'.$name.'" value="'.$value.'"'.$checked.'/><br />';*/
		return '
			          <div class="control-group">
			            <label class="control-label" for="i'.$name.'">'.$desc.': </label>
			            <div class="controls">
			              <label class="checkbox">
			                <input type="checkbox" id="i'.$name.'" name="'.$name.'" value="'.$value.'"'.$checked.'>
			              </label>
			            </div>
			          </div>';
	}
	
	function TwoLines($name,$value,$length,$desc){
		return '
								<div class="control-group">
			            <label class="control-label" for="i'.$name.'">'.$desc.': </label>
			            <div class="controls">
			              <textarea class="span6 twoLines" id="i'.$name.'" name="'.$name.'" length="'.$length.'" rows="2">'.$value.'</textarea>
			            </div>
			          </div>';
	}

	function ShortText($name,$value,$length,$desc){
		return '
								<div class="control-group">
			            <label class="control-label" for="i'.$name.'">'.$desc.': </label>
			            <div class="controls">
			              <textarea class="span6 shortText" id="i'.$name.'" name="'.$name.'" length="'.$length.'" rows="4">'.$value.'</textarea>
			            </div>
			          </div>';		
	}

	function Text($name,$value,$length,$desc){
		return '
								<div class="control-group">
			            <label class="control-label" for="i'.$name.'">'.$desc.': </label>
			            <div class="controls">
			              <textarea class="span6 longText" id="i'.$name.'" name="'.$name.'" length="'.$length.'" rows="8">'.$value.'</textarea>
			            </div>
			          </div>';
	}
	
	function WysiwygEditor($name,$value,$length,$desc,$imgValue=false){
		if ($imgValue != false) $imgValue = "\"".$imgValue."\"";
		else  $imgValue = "false";
		return '
		<div class="label" for="i'.$name.'">'.$desc.': </div><br />
	    <textarea id="simpleEditor" class="htmlText" name="'.$name.'" cols="50" rows="15">'.$value.'</textarea>
	    <script type="text/javascript">    
    	    $(function() {
        	    $("#simpleEditor").simpleditor({
					imageButton: '.$imgValue.',
	                css: "css/simpleEditor.View.css"
                }); // INIT
                $("#btnSubmit").live("click",function(){
                	$("#simpleEditor").simpleditor("updateTextArea");	
                	//alert($("#simpleEditor").simpleditor("toHtmlString"));	
                });
       		 });
	    </script>';
	    		
	    
		//Templates::writeJS("$(function() { $('#i".$name."').wysiwyg(); });");
		//<textarea class="tinyMCE" id="i'.$name.'" name="'.$name.'" length="'.$length.'">'.$value.'</textarea><br />
	}

	function FileUpload($name,$desc,$class="iFile"){
		
		return '<div class="control-group">
	            <label class="control-label" for="i'.$name.'">'.$desc.': </label>
	            <div class="controls">
	              <input id="i'.$name.'" name="'.$name.'" type="file" class="input-file" />
	            </div>
	          </div>';
	}

	function Linked($name,$desc,$value,$linkId,$modul,$id){
		/*
		$output .= '<a href="javascript:linkWindow(\''.$linkId.'\');" class="linkItem">'.$desc.'</a>';
		//$output .= '<iframe id="'.$linkId.'Mask" src="about:blank" scrolling="no" frameborder="0" class="dataLoaderIeMask hidden"></iframe>';
		//$output .= '<div class="dataLoader hidden" id="'.$linkId.'ListContainer"></div>';
		$output .= '<div class="dataPreview" id="'.$linkId.'PreviewContainer"></div><br />';
		$output .= Forms::Hidden($name,$value,"linkId".$linkId);
		$output .= Templates::writeJS("buildLinkPreview('".$linkId."',".$modul.",".$id.");");
		*/
		$icon = 'pencil';
		//picture
		
		$output = '
		<div class="well">
	      <h4><i class="icon-'.$icon.'"></i> <a href="javascript:linkWindow(\''.$linkId.'\');" rel="tooltip" title="'.$GLOBALS['msg']['REL_EDIT'].'">'.mb_strtoupper($desc,"utf-8").'</a></h4>
	      <hr />';

		$output .= '<div class="dataPreview" id="'.$linkId.'PreviewContainer"></div><br />';
		$output .= Forms::Hidden($name,$value,"linkId".$linkId);
		$output .= Templates::writeJS("buildLinkPreview('".$linkId."',".$modul.",".$id.");");
	      
	  $output .= '</div>';
		
		return $output;
	}

	function Hidden($name,$value,$id=""){
		if ($id == ""){
		return '
		<input type="hidden" name="'.$name.'" value="'.$value.'" >';
		} else {
		return '
		<input type="hidden" name="'.$name.'" id="'.$id.'" value="'.$value.'" >';
		}
	}

	function Submit($name,$value){
		return '<input class="btn btn-primary" id="btnSubmit" type="submit" name="'.$name.'" value="'.$value.'" />';
	}

	function Button($name,$value,$link){
		return '
		<input class="btn" type="button" name="'.$name.'" value="'.$value.'" onClick="location.href=\''.$link.'\';" />';
	}

	function Select($name,$value,$length,$desc,$option,$cls="iSelect"){
		if (is_array($option)){
			$output = '<select class="'.$cls.'" id="i'.$name.'" name="'.$name.'">';
			$output .= '<option value="0">----------------------</option>';
			if (count($option)){
				foreach ($option as $o){
					$sel ="";
					if ($value == $o[0]) $sel = " selected=\"selected\"";
					$output .= '<option value="'.$o[0].'"'.$sel.'>'.$o[1].'</option>';
				}
			} else {
				//$output .= '<option value="0">----------------------</option>';
			}
			$output .= '</select>';
		}
		$output = '
			<div class="control-group">
          <label class="control-label" for="i'.$name.'">'.$desc.': </label>
          <div class="controls">
          '.$output.'
          </div>
			</div>';
		return $output;
	}
	
	function Branches($name,$value,$length,$desc,$option,$cls="iSelect"){
		if (is_array($option)){
			$output = '
			<select class="'.$cls.'" id="i'.$name.'" name="'.$name.'">';
			$output .= '<option value="0">----------------------</option>';
			if (count($option) > 0)			
				$output .= Forms::OptionRecursive(0,$option,$value,0);			
			
			$output .= '</select>';
		}
		$output = '
			<div class="control-group">
          <label class="control-label" for="i'.$name.'">'.$desc.': </label>
          <div class="controls">
          '.$output.'
          </div>
			</div>';
		return $output;
	}
	
	function OptionRecursive($parentId,&$option,$value,$level){
		$output = '';			
		foreach ($option as $key=>$o){
			$sel ="";
			if ($o[2] == $parentId){
				if (is_array($value)){
					foreach ($value as $val) {
						if ($val == $o[0]) {	
							$sel = " selected=\"selected\"";
							break;
						}
					}
				} else {
					if ($value == $o[0]) $sel = " selected=\"selected\"";
				}
				$levelTabs = "";
				if ($level != 0){
					$padStr = "-";
					$levelTabs = str_pad($padStr,($level*3)," ",STR_PAD_LEFT);
					if ($levelTabs != "") $levelTabs .= " ";
					$levelTabs = str_replace(" ","&nbsp;",$levelTabs);
				}
				$output .= '<option value="'.$o[0].'"'.$sel.'>'.$levelTabs.$o[1].'</option>';
				if (count($option) > 0)
					$output .= Forms::OptionRecursive($o[0],$option,$value,$level+1);	
			}
		}
		return $output;
	}
	
	function SelectMultiple($name,$value,$length,$desc,$option,$cls="iSelect"){
		if (is_array($option)){
			$results = explode(",",$value);
			$output = '
			<select multiple="multiple" size="10" class="'.$cls.'" id="i'.$name.'" name="'.$name.'[]">';
			if (count($option)){
				foreach ($option as $o){
					$sel ="";
					foreach ($results as $val) {
						if ($val == $o[0]) {	
							$sel = " selected=\"selected\"";
							break;
						}
					}
					$output .= '<option value="'.$o[0].'"'.$sel.'>'.$o[1].'</option>';
				}
			} else {
				$output .= '<option value="0">----------------------</option>';
			}
			$output .= '</select>>';
		}
		$output = '
			<div class="control-group">
          <label class="control-label" for="i'.$name.'">'.$desc.': </label>
          <div class="controls">
          '.$output.'
          </div>
			</div>';
		return $output;
	}
	
	//SELECT RECURSIVE
	function SelectRecursive($name,$value,$length,$desc,$option,$cls="iSelect"){
		if (is_array($option)){
			$results = explode(",",$value);
			$output = '
			<select multiple="multiple" size="10" class="'.$cls.'" id="i'.$name.'" name="'.$name.'[]">';
			if (count($option) > 0){			
				$output .= Forms::OptionRecursive(0,$option,$results,0);
			} else {
				$output .= '<option value="0">----------------------</option>';
			}
			$output .= '</select>';
		}
		$output = '
			<div class="control-group">
          <label class="control-label" for="i'.$name.'">'.$desc.': </label>
          <div class="controls">
          '.$output.'
          </div>
			</div>';
		return $output;
	}

function SelectMenu($name,$option){
		if (is_array($option)){
			$output = '<select class="selMenu" id="sMenu" name="'.$name.'">';
			if (count($option)){
				foreach ($option as $o){
					$sel ="";
					if ($value == $o[0]) $sel = " selected=\"selected\"";
					$output .= '<option value="'.$o[0].'"'.$sel.'>'.$o[1].'</option>';
				}
			} else {
				$output .= '<option value="0">----------------------</option>';
			}
			$output .= '</select>';
		}
		
		return $output;
	}

	function ContentDisable(){
		return '<div id="backgroundMask" class="hidden">
			<div class="close"><a href="javascript:maskClose();" title="'.$GLOBALS["msg"]["CLOSE"].'"><img src="i/ico/close.gif" width="13" height="13" alt="'.$GLOBALS["msg"]["CLOSE"].'" /></a></div>
		</div>';
	}
	
	function VideoUploader()
	{
		return '<div style="background-color: white; border: 1px dotted grey; padding: 3px;">Video:<br />
	<div id="flashUploader">
	</div></div>
	<input type="hidden" name="server_video_id" value="-1" id="video_id_input" />
	<script type="text/javascript">
	   var so = new SWFObject("images/upload-video.swf", "videoupload", "300", "100", "9", "#FFFFFF");
	   so.addParam("allowScriptAccess","always");
	   so.write("flashUploader");
	   
	   function setvideofileid(id)
	   {
	   		$("#video_id_input").val(id);
	   		$("#videoSubmit").removeAttr("disabled");
	   }
	</script>';
	}
	
	function VideoPreview($vData)
	{
		return '<div id="videoPreview" class="hidden" style="background-color: white; border: 1px dotted grey; padding: 3px;"><br />
	<div id="flashVideoPreview">
	</div>
	<a href="javascript:$(\'#videoPreview\').hide();">skryt video</a>
	</div>
	<script type="text/javascript">
	   var soFP = new SWFObject("images/simpleplayer.swf", "videoupload", "392", "218", "9", "#FFFFFF");
	   //var soFP = new SWFObject("images/player.swf", "videoupload", "100%", "400", "9", "#FFFFFF");
	   soFP.addParam("allowScriptAccess","always");
	   soFP.addVariable("videoSource","'.$vData['converted_file'].'");
	   //soFP.addVariable("file","http://magic.glow.cz:8080/clients/'.$vData['converted_file'].'");
	   soFP.write("flashVideoPreview");	   
	</script>';
	}

}