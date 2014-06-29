<?php
	/* 
	* Par Clovis Portron
	* Le 01/03/2014 à 19:22
	* Gère le controle des comptes enregistrés, lits les tweets et applique les changements d'images
	*/
	require_once(dirname(__FILE__)."/auth.php");
	require_once(dirname(__FILE__)."/../assets/conf/BDD.php");
	
	if($GET['op']='applytoall')
		CheckandApplyForAll();
	
	function CheckandApplyForAll()
	{

		$connexion=GetDatabase();
		if($connexion == false)
			return;
		$data=$connexion->query("SELECT * FROM users");
							
		while($entry = $data->fetch())
		{
			$id=$entry['ID'];
			$token=$entry['token'];
			$token_secret=$entry['token_secret'];
			$tw=TokensAreValid($token, $token_secret);
			if($tw == false)
				return;
			$tw=getConnexionWithToken($token,$token_secret);
			$tweets = $tw -> get('statuses/user_timeline', array('user_id' => $id));
			$d=$connexion->query("SELECT tag,url FROM pictures WHERE user='$id'");
			$date=null;
			$url=null;
			while($en=$d->fetch())
			{
				$tag=$en['tag'];
				for($u=0;$u<count($tweets);$u++)
				{
					if(strpos($tweets[$u]->text,$tag) != false)
					{
						$p=$tweets[$u]->created_at;
						$p=str_replace("+0000","",$p);
						$p=strtotime($p);
						if($date==null || $p>$date)
						{
							$date=$p;
							$url=$en['url'];
						}
					}
				}
			}
			if($date != null && $url != null)
			{
				$img=file_get_contents($url);
				$img=base64_encode($img);
				$tw->post('account/update_profile_image', array('image' => $img));
			}
		}
		echo "success";
	}





?>