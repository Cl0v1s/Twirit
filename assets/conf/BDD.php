<?php
	/* 
	 * Par Clovis Portron
	 * Le 24/02/2014
	 * Contient les param�tres de connextion � la base de donn�es
	 */
	
	/* 
	 * Etablit et renvoie une connexion � la base de donn�each
	 */
	function GetDatabase()
	{
		try
		{
			$c=new PDO("mysql:host=sql.olympe.in;dbname=jpHpXGg0", "jpHpXGg0", "Danapoupoun1107");
			return $c;
		}
		catch(PDOException $e)
		{
			return false;
		}
	}



?>