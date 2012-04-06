<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container-fluid">
     	<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="brand" href="#">Databič<sup>v3</sup></a>
      
      <? if ($usr->logged): ?>
      <div class="nav-collapse">
        <ul class="nav pull-right">
        	<li><a href="#"><i class="icon-user icon-white"></i> <?php echo $usr->uname; ?></a></li>
        	<li class="divider-vertical"></li>
        	<li><a href="?type=logout"><?php echo $GLOBALS["menu"]["LOGOUT"]; ?></a></li>
        </ul>
      </div><!--/.nav-collapse -->      
			<? endif;?>
    </div>
  </div>
</div>


<? 
	if (strlen($GLOBALS["resultMessage"]) >1): 
?>
<div class="container">
	<div id="resMsg" class="alert alert-success"><? echo $GLOBALS["resultMessage"]; ?></div>
</div>
<? 
	endif; 
	if (strlen($GLOBALS["errorMessage"]) >1):
?>
<div class="container">
	<div id="resMsg" class="alert alert-error"><? echo $GLOBALS["errorMessage"]; ?></div>
</div>
<?
	endif; 
?>

<? 
	if (!$usr->logged):
?>
<div class="container">
	<div class="row">
		<div class="span6 offset3">
			<form action="index.php" method="post" class="form-horizontal">
			<fieldset>
				<div class="control-group">
		      <label class="control-label" for="username"><?php echo $GLOBALS["msg"]["USERNAME"]; ?>:</label>
		      <div class="controls">
		        <input type="text" class="input-xlarge" name="username" id="username" value="<?=$_POST["username"];?>" >
		      </div>
		    </div>		
				<div class="control-group">
		      <label class="control-label" for="password"><?php echo $GLOBALS["msg"]["PASSWORD"]; ?>:</label>
		      <div class="controls">
		        <input type="password" class="input-xlarge" name="password" id="password">
		      </div>
		    </div>		
				<div class="form-actions">
				  <input type="submit" name="login" class="btn btn-primary" value="<?php echo $GLOBALS["msg"]["LOGIN"]; ?>">
				</div>	    
	
			</fieldset>
			</form>
		</div>
	</div>
</div>
<? endif;?>
