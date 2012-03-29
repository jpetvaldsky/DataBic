<?
class Email extends Forum {
	function sendNotify($post){
		$data = mysql_fetch_array($post);
		$users = mysql_query("SELECT * FROM `forum_user` WHERE `email_notify`='1'");
		if ($users){
			while ($u=mysql_fetch_array($users)){
				if ($u["id"] != $GLOBALS["usr"]->user){
					if (strlen($u["email"]) > 5)
						Email::composeNotify($data,$u);
				}
			}
		}
	}

	function composeNotify($d,$u){
		/* recipients */
		$recipient = $u["real_name"]." <".$u["email"].">";

		/* subject */
		$subject = "Nový příspěvek ".$d["subject"]." v diskuzi";

		/* message */
		$message .= '<html><body style="font-family:Verdana;font-size:11px;">';
		$message .= '<strong>Automatické upozornění na nový příspěvek v diskuzi:</strong><br>';
		$url = "http://www.fedorgal.cz/za/index.php?do=viewTheme&id=".$d["theme_id"];
		$message .= "Kliknutím na tento <a href=\"".$url."\">odkaz</a> si otevřete diskuzi k tématu ".Themes::getName($d["theme_id"])." kde je nový příspěvek od ".Users::getName($d["user_id"]).".<br>";
		$message .= "Název příspěvku: ".$d["subject"]."<br>";
		$message .= "Text příspěvku: ".$d["text"]."<br>";

		/* můžete přidat signaturu */
		$message .= "<br><hr><br>"; //oddělovač signatury
		$message .= "Tento mail byl odeslan automaticky ze serveru <a href=\"http://www.fedorgal.cz\">www.fedorgal.cz</a>, pokud nechcete nadale toto upozorneni dostavat zmente prosim vase osobni nastaveni.";

		/* dodatečné hlavičky pro chyby, From, cc, bcc, atd */

		$headers .= "From: Diskuze Za <za@fedorgal.cz>\n";
		$headers .= "X-Sender: <za@fedorgal.cz>\n";
		$headers .= "X-Mailer: PHP\n"; // mailový klient
		$headers .= "Return-Path: <za@fedorgal.cz>\n";  // Návratová cesta pro chyby

		/* Pokud chcete poslat HTML email, odkomentujte následující řádek */
		$headers .= "Content-Type: text/html; charset=iso-8859-2\n"; // Mime typ

		$message = iconv("UTF-8", "ISO-8859-2", $message);
		$subject = iconv("UTF-8", "ISO-8859-2", $subject);

		/* a teď to odešleme */
		if (mail($recipient, $subject, $message, $headers)) return true;


	}
}