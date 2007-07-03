<?php
//SpecialisedDatabase ist ein spezielles Objekt, das das Database-Objekt kapselt,
//und es mit den n�tigen Verbindungsdaten bef�llt
require_once('classes/framework/database.php');

class SpecialisedDatabase extends Database
{
	//Konstruktor: liefert entweder ein (unspezialisiertes) Verbindungsobjekt oder false, falls Verbindung misslingt
	public function __construct()
	{
		//setze Verbindungsdaten
		$type = 'mysql';
		$server = 'localhost';
		$database = 'wisoadvisor2';
		$user = 'root';
		$password = '';
		
		//rufe den Konstruktor des Parent auf:
		parent::__construct($type, $server, $database, $user, $password);
		
	}

}

?>