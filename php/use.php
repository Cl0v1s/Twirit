<?php
	/* 
	 * Par Clovis Portron
	 * Le 26/06/2014 à 11:53
	 * Permet à l'utilisateur de gérer ses photos et ses #Hashtags
	 */
	require_once(dirname(__FILE__)."/../assets/conf/BDD.php");
	require_once(dirname(__FILE__)."/auth.php");
	session_start();
	if($_POST['op']=="pictures")
		GetUserPictures($_POST['id']);
	else if($_POST['op']=="apply")
		ApplyPictureToUser($_POST['id']);
	else if($_POST['op']=="delete")
		DeletePicture($_POST['id'], $_POST['user']);
	else if($_POST['op']=="add")
		AddPicture($_POST['user'],$_POST['tag'],$_POST['url']);
	else if($_POST['op']=="edit")
		ChangePictureTag($_POST['id'],$_POST['user'],$_POST['tag']);
		
	/* 
	 * Change le tag associé a la photo possédant l'id associé
	 * ER.0   ER.1    ER.4
	 */
	function ChangePictureTag($id,$user,$tag)
	{
		$xml=new DOMdocument("1.0");
		$root=$xml->createElement("response");
        $connexion=GetDatabase();
		if($connexion==false)
		{
			$d=$xml->createElement("result");
			$d->appendChild($xml->createTextNode("error"));
			$u=$xml->createElement("code");
			$u->appendChild($xml->createTextNode("0"));
			$root->appendChild($u);
			$root->appendChild($d);
			$xml->appendChild($root);
			echo $xml->saveXML();	
			return;		
		}
		$data=$connexion->query("SELECT * FROM pictures WHERE tag='$tag' AND user='$user'");
		$_p=0;
		while($entry=$data->fetch())
		{
			if($entry['ID'] != $id)
				$_p+=1;
		}
		if($_p>0)
		{
			$d=$xml->createElement("result");
			$d->appendChild($xml->createTextNode("error"));
			$u=$xml->createElement("code");
			$u->appendChild($xml->createTextNode("4"));
			$root->appendChild($u);
			$root->appendChild($d);
			$xml->appendChild($root);
			echo $xml->saveXML();	
			return;			
		}
		
		$data=$connexion->exec("UPDATE pictures SET tag='$tag' WHERE ID='$id'");
		if($data==1)
		{
			$d=$xml->createElement("result");
			$d->appendChild($xml->createTextNode("ok"));
			$u=$xml->createElement("user");
			$u->appendChild($xml->createTextNode($user));
			$root->appendChild($u);
			$root->appendChild($d);
			$xml->appendChild($root);
			echo $xml->saveXML();			
		}
		else
		{
			$d=$xml->createElement("result");
			$d->appendChild($xml->createTextNode("error"));
			$u=$xml->createElement("code");
			$u->appendChild($xml->createTextNode("1"));
			$root->appendChild($u);
			$root->appendChild($d);
			$xml->appendChild($root);
			echo $xml->saveXML();	
			return;					
		}
	}
	
	
	
	/*
	 * Applique la photo passée en paramète au compte connecté
	 */
	function ApplyPictureToUser($id)
	{
		$xml=new DOMdocument("1.0");
		$root=$xml->createElement("response");
		$connexion=GetConnexionWithToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$db=GetDatabase();
		if($db==false)
		{
			$d=$xml->createElement("result");
			$d->appendChild($xml->createTextNode("error"));
			$u=$xml->createElement("code");
			$u->appendChild($xml->createTextNode("0"));
			$root->appendChild($u);
			$root->appendChild($d);
			$xml->appendChild($root);
			echo $xml->saveXML();	
			return;		
		}
		$data=$db->query("SELECT * FROM pictures WHERE ID='$id'");
		$url=$data->fetch();
		$url=$url['url'];
		$img=file_get_contents($url);
		$img=base64_encode($img);
		$da=$connexion->post('account/update_profile_image', array('image' => $img));
		$d=$xml->createElement("result");
		$d->appendChild($xml->createTextNode("ok"));
		$root->appendChild($d);
		$xml->appendChild($root);
		echo $xml->saveXML();
	}
	

	
	/*
	 * Ajoute une photo dans la base de donnée 
	 * ERR.0 ERR.3
	 */
	function AddPicture($user,$tag,$picture)
	{
		$xml=new DOMdocument("1.0");
		$root=$xml->createElement("response");	
		$connexion=GetDatabase();
		if(!$connexion)
		{	
			$d=$xml->createElement("result");
			$d->appendChild($xml->createTextNode("error"));
			$u=$xml->createElement("code");
			$u->appendChild($xml->createTextNode("0"));
			$root->appendChild($u);
			$root->appendChild($d);
			$xml->appendChild($root);
			echo $xml->saveXML();	
			return;		
		}
		$data=$connexion->exec("INSERT INTO pictures(user,tag,url) VALUES ('$user','$tag','$picture')");
		if($data==1)
		{
			$d=$xml->createElement("result");
			$d->appendChild($xml->createTextNode("ok"));
			$u=$xml->createElement("user");
			$u->appendChild($xml->createTextNode($user));
			$root->appendChild($u);
			$root->appendChild($d);
			$xml->appendChild($root);
			echo $xml->saveXML();	
		}
		else
		{
			$d=$xml->createElement("result");
			$d->appendChild($xml->createTextNode("error"));
			$u=$xml->createElement("code");
			$u->appendChild($xml->createTextNode("3"));
			$root->appendChild($u);
			$root->appendChild($d);
			$xml->appendChild($root);
			echo $xml->saveXML();	
			return;		
		}
	}
	
	/*
	 * Supprime une photo de la base de données
	 * ERR.0 ERR.2
	 */
	function DeletePicture($id, $user)
	{
		$xml=new DOMdocument("1.0");
		$root=$xml->createElement("response");	
		$connexion=GetDatabase();
		if($connexion==false)
		{
			$d=$xml->createElement("result");
			$d->appendChild($xml->createTextNode("error"));
			$u=$xml->createElement("code");
			$u->appendChild($xml->createTextNode("0"));
			$root->appendChild($u);
			$root->appendChild($d);
			$xml->appendChild($root);
			echo $xml->saveXML();	
			return;				
		}
		$data=$connexion->exec("DELETE FROM pictures WHERE ID='$id'");
		if($data==1)
		{
			$d=$xml->createElement("result");
			$d->appendChild($xml->createTextNode("ok"));
			$u=$xml->createElement("user");
			$u->appendChild($xml->createTextNode($user));
			$root->appendChild($u);
			$root->appendChild($d);
			$xml->appendChild($root);
			echo $xml->saveXML();		
		}
		else
		{
			$d=$xml->createElement("result");
			$d->appendChild($xml->createTextNode("error"));
			$u=$xml->createElement("code");
			$u->appendChild($xml->createTextNode("2"));
			$root->appendChild($u);
			$root->appendChild($d);
			$xml->appendChild($root);
			echo $xml->saveXML();	
			return;			
		}
	}
		
		
	/*
	 * Récupère toutes les images associciées à des Hashtags dans la base de donnée dont l'utilisateur passé en paramètres et le propriétaire
	 */
	function GetUserPictures($user)
	{
		$xml=new DOMdocument("1.0");
		$root=$xml->createElement("response");
		$connexion=GetDatabase();
		if($connexion != false)
		{
			$data=$connexion->query("SELECT * FROM pictures WHERE user='$user'");
			while($entry=$data->fetch())
			{
				$pic=$xml->createElement("picture");
				$id=$xml->createElement("id");
				$id->appendChild($xml->createTextNode($entry['ID']));
				$pic->appendChild($id);
				$use=$xml->createElement("user");
				$use->appendChild($xml->createTextNode($entry['user']));
				$pic->appendChild($use);
				$tag=$xml->createElement("tag");
				$tag->appendChild($xml->createTextNode($entry['tag']));
				$pic->appendChild($tag);
				$link=$xml->createElement("url");
				$link->appendChild($xml->createTextNode($entry['url']));
				$pic->appendChild($link);
				$root->appendChild($pic);
			}
		}
		else
		{
			$d=$xml->createElement("result");
			$d->appendChild($xml->createTextNode("error"));
			$root->appendChild($d);
			$xml->appendChild($root);
			echo $xml->saveXML();
			return;
		}
		$xml->appendChild($root);
		echo $xml->saveXML();
	}

?>