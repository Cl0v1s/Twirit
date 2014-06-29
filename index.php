<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Tune your Twitter profile's pic according with your mood !">
	<meta name="keywords" content="Twitter,picture,tool,profile,mood,twirit">
	<meta name="robots" content="index">
	<meta name="REVISIT-AFTER" content="7 days">
	<meta http-equiv="Content-Language" content="fr">
	<meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" type="image/png" href="./assets/image/Logo.png">  
    <title>Tw|irit</title>
	<link href="./css/default.css" rel="stylesheet">
	<link href="./assets/library/animate.css" rel="stylesheet">
	<script type="text/javascript" src="./assets/library/jquery-1.11.js"></script>
	<script type="text/javascript" src="./assets/library/jquery.cookie.js"></script>
	<script type="text/javascript" src="./js/default.js"></script>
  </head>
  <body>
	<br><br><br><br>
	<!--code php chargé de faire fonctionner la connectivité de l'application avec twitter-->
	<script type='text/javascript'>
	<?php
			require_once('./assets/library/Oauth/twitteroauth.php');
			require_once('./php/auth.php');
			session_start();
			$tokens=IsUserExists();
			if($tokens != false && !(isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])))
			{
				$token=$tokens[0];
				$token_secret=$tokens[1];
				$r=TokensAreValid($token,$token_secret);
				if($r==false)
				{
					echo "$(document).ready(function(){present();});";
				}
				else 
				{
					$_SESSION['oauth_token']=$token;
					$_SESSION['oauth_token_secret']=$token_secret;
					echo "$(document).ready(function(){use('".$r[1]."');});";
				}
			}
			else if(isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']))
			{
				$connexion = new TwitterOAuth("LMOkQt5QC3B7hatiNE8VQ", "FobQGdLerqA6Evr7Bj0uS9qiUh65zqlkt4PdJS5vc", $_SESSION['oauthToken'], $_SESSION['oauthVerifier']);
				$token=$connexion->getAccessToken($_GET['oauth_verifier']);
				$r=TokensAreValid($token['oauth_token'],$token['oauth_token_secret']);
				if($r != false)
				{
					$_SESSION['oauth_token'] = $token['oauth_token'];
					$_SESSION['oauth_token_secret'] = $token['oauth_token_secret'];
					if(RegisterUser($r[1],$token['oauth_token'], $token['oauth_token_secret']))
					{
						echo "$(document).ready(function(){linked('".$r[0]."');});";
					}
					else if(UpdateUser($r[1],$token['oauth_token'], $token['oauth_token_secret']))
					{
						echo "$(document).ready(function(){linked('".$r[0]."');});";
					}
					else 
						echo "$(document).ready(function(){noLinked();});";
				}
				else
					echo "$(document).ready(function(){noLinked();});";

			}
			else
			{
				echo "$(document).ready(function(){present();});";	
			}
			
	?>
	</script>
  
	<!--Tête de page contenant le logo du service-->
	<header>
		<center><img class="logo" src="./assets/image/Logo.png"></center>
	</header>
	
	
	<div id="loading">
	</div>
	
	<!--Cadre contenant le contenu du site du service-->
	<div class="content">
		<div id="content">
			<!--contenu de test-->

		</div>
	</div>
	
	<!--Pieds de page contenant les mentions légales-->
	<footer>
		<center>All the elements of the present page are the property of Clovis Portron (@Chaip0koi) portron.cl[AT]gmail.com.</center>
	</footer>
	<!-- Start of StatCounter Code for Default Guide -->
	<script type="text/javascript">
	var sc_project=9657293; 
	var sc_invisible=1; 
	var sc_security="4337906b"; 
	var scJsHost = (("https:" == document.location.protocol) ?
	"https://secure." : "http://www.");
	document.write("<sc"+"ript type='text/javascript' src='" +
	scJsHost+
	"statcounter.com/counter/counter.js'></"+"script>");
	</script>
	<noscript><div class="statcounter"><a title="web counter"
	href="http://statcounter.com/" target="_blank"><img
	class="statcounter"
	src="http://c.statcounter.com/9657293/0/4337906b/1/"
	alt="web counter"></a></div></noscript>
	<!-- End of StatCounter Code for Default Guide -->
  </body>
</html>