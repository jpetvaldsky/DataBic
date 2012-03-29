<?
class Layout {
	function buildNavigation(){
		$items = array(
			//array($GLOBALS["menu"]["MAP"],"webmap","system-sitemap.gif"),
			array($GLOBALS["menu"]["MODUL"],"modul","system-data.gif"),
			array($GLOBALS["menu"]["IMAGES"],"images","system-images.gif"),
			array($GLOBALS["menu"]["VIDEOS"],"videos","system-videos.gif"),
			array($GLOBALS["menu"]["FILES"],"files","system-files.gif"),
			//array($GLOBALS["menu"]["URLS"],"urls","system-urls.gif"),
			array($GLOBALS["menu"]["TXT"],"text","system-text.gif"),
			//array($GLOBALS["menu"]["LANG"],"languages","system-lang.gif"),
			//array($GLOBALS["menu"]["SET"],"settings","system-setting.gif"),
			array($GLOBALS["menu"]["LOGOUT"],"logout","system-logout.gif",true),
		);
		foreach ($items as $row) $output .= Templates::MenuItem($row[2],$row[0],"type",$row[1],$row[3]);
		$output = Templates::mainMenu($output);
		return $output;
	}
	
}