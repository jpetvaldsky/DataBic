<?
	function urlize($string) {
		$toAsciiTable = array(
			0x20,0x21,0x27,0x23,0x24,0x25,0x26,0x27,0x28,0x29,0x2a,0x2b,0x2c,0x2d,0x2e,0x2f,0x30,0x31,0x32,0x33,0x34,0x35,0x36,0x37,
			0x38,0x39,0x3a,0x3b,0x3c,0x3d,0x3e,0x3f,0x40,0x41,0x42,0x43,0x44,0x45,0x46,0x47,0x48,0x49,0x4a,0x4b,0x4c,0x4d,0x4e,0x4f,
			0x50,0x51,0x52,0x53,0x54,0x55,0x56,0x57,0x58,0x59,0x5a,0x5b,0x5c,0x5d,0x5e,0x5f,0x60,0x61,0x62,0x63,0x64,0x65,0x66,0x67,
			0x68,0x69,0x6a,0x6b,0x6c,0x6d,0x6e,0x6f,0x70,0x71,0x72,0x73,0x74,0x75,0x76,0x77,0x78,0x79,0x7a,0x7b,0x7c,0x7d,0x7e,0x7f,
			0x80,0x81,0x82,0x83,0x27,0x85,0x86,0x87,0x88,0x89,0x73,0x8b,0x8c,0x74,0x7a,0x8f,0x90,0x27,0x27,0x27,0x27,0x95,0x2d,0x97,
			0x98,0x99,0x73,0x9b,0x9c,0x74,0x7a,0x9f,0xa0,0xa1,0xa2,0xa3,0xa4,0xa5,0xa6,0xa7,0xa8,0xa9,0xaa,0xab,0xac,0xad,0xae,0xaf,
			0xb0,0xb1,0xb2,0xb3,0xb4,0xb5,0xb6,0xb7,0xb8,0xb9,0xba,0xbb,0x6c,0xbd,0x6c,0x7a,0x72,0x61,0x41,0x41,0x41,0x6c,0x43,0x43,
			0x63,0x65,0x45,0x45,0x65,0x69,0x49,0x64,0x44,0x4e,0x6e,0x6f,0x4f,0x4f,0x4f,0xd7,0x72,0x75,0x75,0x55,0x55,0x79,0x54,0x53,
			0x72,0x61,0x61,0x61,0x61,0x6c,0x63,0x63,0x63,0x65,0x65,0x65,0x65,0x69,0x69,0x64,0x64,0x6e,0x6e,0x6f,0x6f,0x6f,0x6f,0xf7,
			0x72,0x75,0x75,0x75,0x75,0x79,0xfe,0xff);
		
		$nstring = '';
		$string = iconv('UTF-8','WINDOWS-1250',$string);
		for ($pos=0;$pos<strlen($string);$pos++) {
			$new = $toAsciiTable[ord($string[$pos])-32];
			if (	($new>=ord('0') && $new<=ord('9')) ||
					($new>=ord('a') && $new<=ord('z')) ||
					($new>=ord('A') && $new<=ord('Z')) 
				) {
				$nstring .= chr($new);
			}
			/*if ($new==ord('/')) {
				$nstring .= '/';
			}*/
			if ($new==ord(' ')) {
				$nstring .= '-';
			}		
		}
		$string = strtolower($nstring);
		
		return str_replace("--","-",$string);	
	}

	function parseDate2Time($input){
		list($date,$time) = explode(' ',$input);
		list($d,$m,$y) = explode('.',$date);
		list($hour,$minute) = explode(':',$time);
		$ts = mktime($hour,$minute,0,$m,$d,$y);
		if ($ts == -1)
			$ts = time();
		return $ts;
	}

	function shortenString($input,$lenght,$add=true,$byWord=false){
		$input = iconv("utf-8","windows-1250",$input);
		if (mb_strlen($input) > $lenght){
			$pattern = "@<a[^>]*.*?</a>@si"; //>?
			if (preg_match($pattern,$input)){
				$x = correctStr($input,$fullLink,$linkName);
				$linkArr = explode("|",$x["blank_txt"]);
				$countLength = 0;
				for($a=0;$a<count($linkArr);$a++){
					if ($linkArr[$a] == "link"){
						$countLength+=mb_strlen($x["linkName"]);
						if ($countLength<$lenght){
							$output.=$x["fullLink"].$x["linkName"]."</a>";
						} else {						
							$output.= mb_substr($x["fullLink"],0,mb_strlen($x["fullLink"])-1)." title=``".$x["linkName"]."``>".mb_substr($x["linkName"],0,($lenght-$prevLength))."...</a>";
							break;
						}
					} else {
						$countLength+=mb_strlen($linkArr[$a]);
						if ($countLength<$lenght) {
							$output.=$linkArr[$a];
						} else {						
							$output.=mb_substr($linkArr[$a],0,($lenght-$prevLength));
							break;
						}
					}
					$prevLength = $countLength;
				}
				$input = $output;
			} else {
				if (mb_strlen($input) > $lenght){
					$input = mb_substr($input,0,$lenght);
				}		
			}
			if ($byWord)
				$input = correctToWord($input);
			if ($add !== true) $input = $input.$add;
			else $input = $input."...";
		}		
		return iconv("windows-1250","utf-8",$input);
	}

	function correctToWord($input){
		$output = $input;
		for ($a=1;$a<strlen($input);$a++){
			if ($input[strlen($input)-$a] != " ") {
				$output = substr($input,0,0-$a);
			} else {
				$output = substr($input,0,0-$a);
				break;
			}
		}
		return $output;
	}

	function correctStr($text){
		$searchHref = "@(<a[^>]*>)(.+)(<\/a>)@"; // Strip out anchors
		$replaceHtml = "@<[\/\!]*?[^<>]*@si"; //>?
		$output = $text;
		if (preg_match($searchHref,$output,$matches)){
			$fullLink = $matches[1];
			$linkName = $matches[2];
			$output = preg_replace($searchHref,"|link|",$output);
		}		
		return array("blank_txt"=>preg_replace($replaceHtml,"",$output),"linkName"=>$linkName,"fullLink"=>$fullLink);


	}

	function str_RegexPrePos($strValue)
	{
		//replace predlozek
		$oRegexp = "(([ ]{1})|(&nbsp;))([-–—a-zA-Z]{1,2})(([ ]{1})|(&nbsp;))";
		$strValue = ereg_replace($oRegexp," \\4&nbsp;",$strValue); // &nbsp;\\4\\5
		//replace cislo + jednotka
		$oRegexp = "([0123456789]{1})([ ]{1})(([%]{1})|(m)|(km)|(cm)|(mm)|(kg)|(pcs))";
		$strValue = ereg_replace($oRegexp,"\\1&nbsp;\\3",$strValue); 
		return $strValue;
	}

	function getTableMaxIncrement($tName){
		$qShowStatus = "SHOW TABLE STATUS LIKE '$tName'";
		if ($qShowStatusResult 	= mysql_query($qShowStatus)){
			$row = mysql_fetch_assoc($qShowStatusResult);
			return $next_increment = $row['Auto_increment'];						
		}
		return 0;
	}

	function getLastOrderId($tName,$item_id){
		$q = sprintf("SELECT * FROM `%s` WHERE `item_id`='%d'",$tName,$item_id);
		$count = 0;
		if ($total = mysql_query($q)){
			while($row = mysql_fetch_array($total)) $count++;
			return $count;						
		}
		return 0;
	}


	function formatbytes($val, $digits = 3, $mode = "SI", $bB = "B"){ //$mode == "SI"|"IEC", $bB == "b"|"B"
		$si = array("", "k", "M", "G", "T", "P", "E", "Z", "Y");
		$iec = array("", "Ki", "Mi", "Gi", "Ti", "Pi", "Ei", "Zi", "Yi");
		switch(strtoupper($mode)) {
			case "SI" : $factor = 1000; $symbols = $si; break;
			case "IEC" : $factor = 1024; $symbols = $iec; break;
			default : $factor = 1000; $symbols = $si; break;
		}
		switch($bB) {
			case "b" : $val *= 8; break;
			default : $bB = "B"; break;
		}
		for($i=0;$i<count($symbols)-1 && $val>=$factor;$i++)
			$val /= $factor;
		$p = strpos($val, ".");
		if($p !== false && $p > $digits) $val = round($val);
		elseif($p !== false) $val = round($val, $digits-$p);
		return round($val, $digits) . " " . $symbols[$i] . $bB;
	}

	function IsSpamText($input){
		$badWords = Array("porn "," porn","xxx","vicodin", "xanax", "ultram", "analgesic", "steroidal", "drug", "valium", "tramadol", "phentermine", "xenical", "meridia", "viagra", "diet pills", "cialis", "cheap soma", "soma online", "hydrocodone", "narcotic", "codeine", "propecia", "finasteride", "ringtones", "ringtone", "hey cool site man,thank you ;)", "very informative and well designed website", "i like your website and enjoy your life","ativan", "lorazepam", "hardcore","incest","lesbian","anal sex","black sex","hot sex","free sex","tamiflu","alprazolam","carisoprodol","prozac","free mp3","anime porn","hard anal","double anal","anal cum","wanna download","adult sex","pornstar","tit fuck","sexy girls","loan mortgage","erect penis","increase penis","replica watch","docs.google.com","large penis","penis","Penis","-sex","sexy-","adult-","wedding dress","[url=","blogspot.com","[url]http://","sexy teen","reductil"," sex ","free poker","vaginal","levitra","online casino","play roulette","online poker","http://forex","hoodia","online gambling","free casino","play blackjack","claritin","Realy great site about ","Your site is the best.");
		foreach($badWords as $word){
			if (strpos(strtolower($input),$word) === false){
				//echo ".";
			} else {
				return true;
			}
		}
		return false;
	}

	function check_email_address($email) {  
		// First, we check that there's one @ symbol, and that the lengths are right  
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {    
			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.    
			return false;  
		}  
		// Split it into sections to make life easier  
		$email_array = explode("@", $email);  
		$local_array = explode(".", $email_array[0]);  
		for ($i = 0; $i < sizeof($local_array); $i++) {     
			if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {      
				return false;    
			}  
		}    
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { 
			// Check if domain is IP. If not, it should be valid domain name    
			$domain_array = explode(".", $email_array[1]);    
			if (sizeof($domain_array) < 2) {        
				return false; 
				// Not enough parts to domain    
			}    
			for ($i = 0; $i < sizeof($domain_array); $i++) {      
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {        
					return false;      
				}    
			}  
		}  
		return true;
	} 

	function replaceLinkTag($input){
		$text = $input;
		while (preg_match("/\[(.*[^]])\]/",$text,$matches)){
			$result = $matches[1];
			$replaceLink = '<a href="'.$result.'" target="_blank"><span>'.$result.'</span></a>';
			if (strpos($result,"::") !== false){
				$lData = explode("::",$result);
				$replaceLink = '<a href="'.$lData[0].'" target="_blank"><span>'.$lData[1].'</span></a>';
			}
			$text = preg_replace("/\[(.*[^]])\]/",$replaceLink,$text,1);
		}
		return $text;
	}
	
	function replaceImageTag($input){
  $text = $input;
  while (preg_match("/~(.*[^~])\~/",$text,$matches)){
  $result = $matches[1];
  $imgId = $result;
  $imgTitle = "";
  if (strpos($result,"::") !== false){
  $lData = explode("::",$result);
  $imgId = $lData[0];
  $imgTitle = $lData[1];
  }
  array_push($GLOBALS["inTextImages"],$imgId);
  $replaceImg = '<div class="inTextPic">'.getImageById($imgId,$imgTitle).'</div>';
  $text = preg_replace("/\~(.*[^~])\~/",$replaceImg,$text,1);
  }
  return $text;
  }

	

	function getImageById($id,$title='',$addStr=''){
  $image = Images::ImageById($id);
  if ($image["filename"] != ""){
  if ($title == "") $title = $image["title"];
  list($fName,$extension) = explode(".",$image["filename"]);
  $bFile = $GLOBALS["images_folder"].$image["filename"];
  if (file_exists($bFile)) {
  $iSize = getimagesize($bFile);
  $output .= '<img src="/'.$bFile.'" alt="'.$title.'">';
  }
  }
  return $output;
  }

  function getResizedImageById($id,$w,$h,$qual,$cropW,$cropH,$folder,$title='',$addStr=''){
        $image = Images::ImageById($id);
        if ($image["filename"] != ""){            
            if ($title == "") $title = $image["title"];
            list($fName,$extension) = explode(".",$image["filename"]);
            Images::createThumbnail($image["filename"],$fName.".jpg",$folder,$w,$h,$qual,$cropW,$cropH);                        
            $bFile = $GLOBALS["images_folder"].$folder.$fName.".jpg";
            if (file_exists($bFile)) {                    
                $iSize = getimagesize($bFile);
                $output .= '<img src="/'.$bFile.'" width="'.$iSize[0].'" height="'.$iSize[1].'" title="'.$title.'" alt="'.$title.'" class="tImg" />';
            }
        }
        return $output;
    }
    
    function getResizedImagePathById($id,$w,$h,$qual,$cropW,$cropH,$folder,$bFile=''){
        $image = Images::ImageById($id);
        if ($image["filename"] != ""){            
            list($fName,$extension) = explode(".",$image["filename"]);
            Images::createThumbnail($image["filename"],$fName.".jpg",$folder,$w,$h,$qual,$cropW,$cropH);                        
            $bFile = $GLOBALS["images_folder"].$folder.$fName.".jpg";
            }
        return $bFile;
    }
    
    function getVideoThumb($file,$w,$h,$qual,$cropW,$cropH,$folder,$bFile=''){
        if ($file != ""){            
            list($fName,$extension) = explode(".",$file);
            Images::createThumbnail($file,$fName.".jpg",$folder,$w,$h,$qual,$cropW,$cropH);                        
            $bFile = $GLOBALS["images_folder"].$folder.$fName.".jpg";
            }
        return $bFile;
    }
    
	
	function getImageTitleById($id,$title=''){
		$image = Images::ImageById($id);
		if ($image["filename"] != ""){		
			if ($title == "") $title = $image["title"];
			}
		return $title;
	}
	
	function getImagePathById($id,$bFile=''){
		$image = Images::ImageById($id);
		if ($image["filename"] != ""){			
			if ($bFile == "") $bFile = $GLOBALS["images_folder"].$image["filename"];
			}
		return $bFile;
	}

	function fixTextOutput($input,$replaceLink=true){
		$nbChars = array("&amp;nbsp;","&nbsp;");
		$input = str_replace($nbChars,"__",$input);
		if ($replaceLink) $input = replaceLinkTag($input);
		$input = html_entity_decode(nl2br($input));
		$input = str_replace("\n","",$input);
		$input = str_replace("\r","",$input);
		return str_replace("__","&nbsp;",$input);
	}
	
	function convertToDecimal($input){
    	$output = '';
		for ($i=0; $i<strlen($input); $i++) {
			$output .= "&#" . ord($input[$i]) . ";";
		}
		return $output;
	}
	
	function verifyAvaiableDirPath($dirPath,$mode=0755)
	{
		$dirParams = explode('/',$dirPath);
		$baseDir = '';
		for ($a = 0;$a < count($dirParams); $a++)
		{
			if ($dirParams[$a] != '')
			{
				$baseDir .= $dirParams[$a];			
				if (!is_dir($baseDir))
				{
					if (mkdir($baseDir))
					{
						chmod($baseDir,$mode);
					}
					else
					{
						break;
					}
				}
				else
				{
					@chmod($baseDir,$mode);
				}
				if (!is_dir($baseDir)) {
					break;
				}
				$baseDir .= '/';
			}
		}
	}