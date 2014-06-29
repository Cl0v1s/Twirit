<?php
	/* 
	 * Par Clovis Portron
	 * Le 21/02/2014
	 * Permet à l'application de se connecter et d'utiliser l'API twitter 
	 */
	 
	require_once(dirname(__FILE__).'/../assets/library/Oauth/twitteroauth.php');
	require_once(dirname(__FILE__).'/../assets/conf/BDD.php');
	
	
	/*
	 * Realise les opération demandées en fonction des paramètres envoyés à la page
	 */
	if($_POST['op']=="url")
		GetConnexionUrl();
		
			if($_GET['op']=="test")
				echo UpdateUser("413250016","ok1","ok1");
	
	
	/* 
	 * Demande le déblocage de l'application sur le compte de l'utilisateur sur twitter
	 */
	function GetConnexionUrl()
	{
		session_start();
		$c=new TwitterOAuth("LMOkQt5QC3B7hatiNE8VQ", "FobQGdLerqA6Evr7Bj0uS9qiUh65zqlkt4PdJS5vc");
		$cr=$c->getRequestToken();
		$_SESSION['oauthToken'] = $cr['oauth_token'];
		$_SESSION['oauthVerifier'] = $cr['oauth_token_secret'];
		$url=$c->getAuthorizeURL($cr);
		$xml = new DOMDocument("1.0");
		$root=$xml->createElement("response");
		$xml->appendChild($root);
		$node=$xml->createElement("token");
		$text=$xml->createTextNode($cr['oauth_token']);
		$node->appendChild($text);
		$root->appendChild($node);
		$node=$xml->createElement("tokenSecret");
		$text=$xml->createTextNode($cr['oauth_token_secret']);
		$node->appendChild($text);
		$root->appendChild($node);
		$node=$xml->createElement("url");
		$text=$xml->createTextNode($url);
		$node->appendChild($text);
		$root->appendChild($node);		
		$xml->formatOutput = true;
		echo $xml->saveXML();
		
	}
	
	/*
	 * Entamme un nouvelle connexion à twitter avce les données passées en paramètres
	 */
	function getConnexionWithToken($oAuthToken,$oAuthSecret)
	{
		return new TwitterOAuth("LMOkQt5QC3B7hatiNE8VQ", "FobQGdLerqA6Evr7Bj0uS9qiUh65zqlkt4PdJS5vc", $oAuthToken, $oAuthSecret);
	}
	
	/*
	 * Test si les tokens sont toujours valides, false si invalide sinon renvoie le pseudo de l'user
	 */
	function TokensAreValid($oAuthToken,$oAuthSecret)
	{
		$c = new TwitterOAuth("LMOkQt5QC3B7hatiNE8VQ", "FobQGdLerqA6Evr7Bj0uS9qiUh65zqlkt4PdJS5vc", $oAuthToken, $oAuthSecret);
		$data=$c->get('account/verify_credentials');
		if($data->screen_name != "" && $data->screen_name != null)
		{
			
			return (array($data->screen_name,$data->id_str));
		}
		return false;
	}
	
	/*
	 * Cherche l'IP de l'utilisateur dans la base de données et retourne ses tokens si trouvé.
	 * Sinon retourne False
	 */
	function IsUserExists($value)
	{
		$found=0;
		$connexion=GetDatabase();
		if($connexion == false)
		{
			return false;
		}
		$data=$connexion->query("SELECT * FROM users");
		$token='';
		$token_secret='';
		$id='';
		if($value==null || $value=='')
		{
			$ip=$_SERVER['REMOTE_ADDR'];
			while($entry=$data->fetch())
			{
				if($entry['IP']==$ip)
				{
					$found+=1;
					$token=$entry['token'];
					$token_secret=$entry['token_secret'];
					$id=$entry['ID'];
				}
			}
		}
		else
		{
			while($entry=$data->fetch())
			{
				if($entry['ID']==$value)
				{
					$found+=1;
					$token=$entry['token'];
					$token_secret=$entry['token_secret'];
					$id=$entry['ID'];
				}
			}		
		}
		if($found==1)
		{
			return array($token,$token_secret,$id);
		}
		else
		{
			return false;
		}
			
			
	}
	
	/*
	 * Enregistre un nouvel utilisateur dans la base de données
	 */
	function RegisterUser($id,$token, $token_secret)
	{
		if(IsUserExists($id)==false)
		{
			$connexion=GetDatabase();
			if($connexion == false)
				return false;
			$ip=$_SERVER['REMOTE_ADDR'];
			$data=$connexion->exec("INSERT INTO users(IP,ID,token,token_secret) VALUES ('$ip','$id','$token','$token_secret')");
			if($data==1)
				return true;
			else
				return false;
		}
		return false;
	}
	
	/*
	 * Met à jour les informations d'un utilisateur
	 */
	function UpdateUser($id,$token,$token_secret)
	{
		if(IsUserExists($id) != false)
		{
			$connexion=GetDatabase();
			if($connexion == false)
			{
				return false;
			}
			$ip=$_SERVER['REMOTE_ADDR'];
			$data=$connexion->exec("UPDATE users SET IP = '$ip', token = '$token', token_secret = '$token_secret' WHERE ID = '$id'");
			if($data==1)
				return true;
			else
				return true;	//on retourne true aussi si les tokens n'ont pas changé ce n'est pas dramatique		
		}
	}
	

	
	
?>

