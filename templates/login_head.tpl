<? 
	if (strlen($GLOBALS["resultMessage"]) >1): 
?>
<div id="resMsg" class="success"><? echo $GLOBALS["resultMessage"]; ?></div>
<? 
	endif; 
	if (strlen($GLOBALS["errorMessage"]) >1):
?>
<div id="resMsg" class="error"><? echo $GLOBALS["errorMessage"]; ?></div>
<?
	endif; 
?>

<? 
	if (!$usr->logged):
?>
<div class="headline">
	<div class="logo"><h1>Databič<sup>1</sup></h1></div>
	<div class="stripe"><img src="i/pix.gif" width="1" height="41" /></div>
</div>
<div class="clear">&nbsp;</div>

<div id="login">
	<form action="index.php" method="post"><fieldset>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td><?php echo $GLOBALS["msg"]["USERNAME"]; ?>:</td>
			<td><input type="text" name="username" class="loginText" value="<?=$_POST["username"];?>" /></td>
		</tr>
		<tr>
			<td><?php echo $GLOBALS["msg"]["PASSWORD"]; ?>:</td>
			<td><input type="password" name="password" class="loginText" /></td>
		</tr>
		<tr>
			<td colspan="2" class="np"><input type="submit" name="login" value="<?php echo $GLOBALS["msg"]["LOGIN"]; ?>" class="submit" /></td>
		</tr>
	</table>
	</fieldset>
	</form>
</div>
<? endif;?>
