<?php
//SpecialisedDatabase ist ein spezielles Objekt, das das Database-Objekt kapselt,
//und es mit den nötigen Verbindungsdaten befüllt
require_once('../classes/framework/database.php');

class SpecialisedDatabase extends Database
{
	//Konstruktor: liefert entweder ein (unspezialisiertes) Verbindungsobjekt oder false, falls Verbindung misslingt
	public function __construct()
	{
		//setze Verbindungsdaten
		$type = 'mysql';
		$server = 'localhost';
		$database = 'wisoadvisor';
		$user = 'advisoradm';
		$password = 'hcKJ5v.fvJTBm.47';
		
		//rufe den Konstruktor des Parent auf:
		parent::__construct($type, $server, $database, $user, $password);
	}

}

?>