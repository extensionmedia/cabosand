<?php 

$formToken='';
$envirenment = "CABOSANDE-MANAGER";
	if ( !isset( $_SESSION[$envirenment]['formToken'] ) ){
		$formToken = md5( uniqid('auth', true) ); 
		$_SESSION[$envirenment]['formToken']	= $formToken;
		//$_SESSION['nFois'] = 1;
	}else{
		$formToken = $_SESSION[$envirenment]['formToken'];
		//$_SESSION['nFois']=$_SESSION['nFois']+1;
	}
	//echo md5('password');

/*******	GET COOKIES		*/	
/*
$gestore_usr = "";
if(isset($_COOKIE["gestore_usr"])) {
    $gestore_usr = $_COOKIE["gestore_usr"];
} 	
*/
//var_dump($_COOKIE);

?>


<div style="position: fixed; width: 100%; top: 20px; z-index: 9999 ">
	<div class="login_response" style="width: 350px; margin-left: auto; margin-right:auto"></div>
</div>
<div style="padding: 45px"></div>
<div class="login_container">
	<h2><?= $CONTENT["welcome"] ?></h2>
	
	<div class="row">
		<div class="col_12" style="padding: 0;">
			<input type="text" placeholder="<?= $CONTENT["login"] ?>" id="login" value="<?= (isset($_COOKIE["CABOSANDE-REMEMBER_LOGIN"]))? $_COOKIE["CABOSANDE-REMEMBER_LOGIN"] : "" ?>">
		</div>
	</div>
	
	<div class="row" style="margin-top: 13px;">
		<div class="col_12" style="padding: 0;">
			<input type="password" placeholder="<?= $CONTENT["password"] ?>" id="password" value="<?= (isset($_COOKIE["CABOSANDE-REMEMBER_PASSWORD"]))? $_COOKIE["CABOSANDE-REMEMBER_PASSWORD"] : "" ?>">
			<input type="hidden" id="formToken" value="<?= $formToken ?>">
		</div>
	</div>
	
	<div class="row">
		<div class="col_12" style="padding: 0; font-size: 12px; padding: 15px 0">
			<label><input type="checkbox" id="remember"> <?= $CONTENT["q1"] ?> </label>
		</div>
		<div class="col_12" style="padding: 0; font-size: 12px; padding: 15px 0; text-align: right; color: #560203">
			<a href="" style="color: #560203; text-decoration: none"><?= $CONTENT["q2"] ?></a>
		</div>
	</div>
	<div class="row" style="margin-bottom: 15px;">
		<div class="col_12" style="padding: 0; margin-top: 12px">
			<button style="width: 100%; padding: 12px 0" class="btn btn-blue btn_login" value="login">
				<span class="is_doing hide"><i class="fas fa-sync fa-spin"></i> Envoi en cours</span> 
				<span class="do"><?= $CONTENT["q3"] ?> </span>
			</button>
		</div>
	</div>
	<div class="debug">
	<?php
		//var_dump($_SESSION[$envirenment]);
	?>
	</div>
</div>