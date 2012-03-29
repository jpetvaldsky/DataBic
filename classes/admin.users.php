<?

class Users extends Admin {

	/*
		VERIFY USER DATA AND LOGIN 
	*/

	function checkLogin(){
		$this->logged = false;
		if ($_POST["login"]){
			$uname = $_POST["username"];
			$pass= md5($_POST["password"]);
			$query = "SELECT * FROM `wa_users` WHERE `username`='$uname' AND `password`='$pass'";
			$this->checkUserData($query);
		} elseif ($_COOKIE["WA_USER_".$this->appName]) {
			$cData = explode("|",$_COOKIE["WA_USER_".$this->appName]);
			$id = $cData[0];
			$pass = $cData[1];
			$query = "SELECT * FROM `wa_users` WHERE `id`='$id' AND `password`='$pass'";
			$this->checkUserData($query,false);
		}
	}

	function checkUserData($query,$res=true){
		$result = mysql_query($query);
		if ($result){
			if (mysql_num_rows($result) > 0){
				$this->logged = true;								
				$this->superadmin = false;								
				$GLOBALS["resultMessage"] = "Přihlášení proběhlo úspěšně!<br />";
				while ($row=mysql_fetch_array($result)){
					setcookie ("WA_USER_".$this->appName,$row["id"]."|".$row["password"],time()+60*60*24*90);
					$this->user = $row["id"];
					if ($row["sa"] == 1){
						$this->superadmin = true;
					}
				}
			} else {
				$GLOBALS["errorMessage"]= "Chyba při přihlášení!";
			}
		} else {
			$GLOBALS["errorMessage"]= "Chyba při přihlášení!";
		}
		if (!$res){
			$GLOBALS["resultMessage"]= "";
			$GLOBALS["errorMessage"]= "";
		}
	}

	function logout(){
		setcookie ("WA_USER_".$this->appName, "", time() - 3600);
		$this->logged = false;
		$this->superadmin = false;
	}

}