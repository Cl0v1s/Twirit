<?php
	/* 
	 * Par Clovis Portron
	 * Le 24/02/2014
	 * Contient les paramètres de connextion à la base de données
	 */
	
	/* 
	 * Etablit et renvoie une connexion à la base de donnéeach
	 */
	function GetDatabase()
	{
		try
		{
			$c=new PDO("mysql:host=sql.olympe.in;dbname=jpHpXGg0", "jpHpXGg0", "Password");
			return $c;
		}
		catch(PDOException $e)
		{
			return false;
		}
	}



?>
