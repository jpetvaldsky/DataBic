<?
class Layout {
	function buildNavigation(){
		$items = array(
			array($GLOBALS["menu"]["MAP"],"webmap","icon-home"),
			array($GLOBALS["menu"]["MODUL"],"modul","icon-folder-open"),
			array($GLOBALS["menu"]["IMAGES"],"images","icon-picture"),
			array($GLOBALS["menu"]["VIDEOS"],"videos","icon-facetime-video"),
			array($GLOBALS["menu"]["FILES"],"files","icon-file"),
			//array($GLOBALS["menu"]["URLS"],"urls","system-urls.gif"),
			array($GLOBALS["menu"]["TXT"],"text","icon-comment"),
			array($GLOBALS["menu"]["LANG"],"languages","icon-flag")
			//array($GLOBALS["menu"]["SET"],"settings","system-setting.gif"),
		);
		foreach ($items as $row) $output .= Templates::MenuItemNew($row[2],$row[0],"type",$row[1],$row[3]);
		$output = Templates::mainMenu($output);
		return $output;
	}
	
}