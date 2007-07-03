<?php
//Der SessionHandler verwaltet Nutzerdaten, die in der Session gespeichert werden

class SessionHandler
{
	private $db = null; //enth�lt nach der Initialisierung den DatenbankHandler
	private $conf = null; //enth�lt das Konfigurations-Objekt
	private $userData = null; //enth�lt sp�ter ein Array mit allen Userdaten
	private $uid = null; //enth�lt die ID des authentifizierten Users; eigentlich eine Doppeltspeicherung, da
						//die ID auch im, $userData-Array vorkommt, allerdings wird die ID auch zur Authentifizierung usw. benutzt
	private $admin = false; //dient zur �berpr�fung, ob der User auch Administrator ist
		
	//Konstruktor: initialisiert den SessionHandler
	public function __construct( $database, $configuration )
	{
		//setze Member-Variablen
		$this->db = $database;
		$this->conf = $configuration;
	}

	/**
	 * isAuthenticated() �berpr�ft, ob der User angemeldet ist
	 * @return true wenn angemeldet, false wenn nicht
	 */
	public function isAuthenticated()
	{
		//einfach checken, ob uid (=die UserID) gesetzt ist
		if ($this->uid) return true;
		else return false;	
	}

	/**
	 * isAdmin() �berpr�ft, ob der User auch Administrator ist
	 * @return true wenn Administrator, false wenn nicht
	 */
	public function isAdmin()
	{
		//einfach checken, wie $admin gesetzt ist
		return $this->admin;	
	}

	/**
	 * getUid() liefert die Uid zum angemeldeten User
	 * @return die Uid oder false falls diese nicht existiert
	 */
	public function getUid()
	{
		if ( $this->uid ) return $this->uid;
		else return false;
	}

	/**
	 * getUserData() liefert Daten zum angemeldeten User
	 * @param $type qualifiziert Daten - als String; entspricht einer Spalte der UserTabelle
	 * @return das angeforderte Datum oder false falls dieses nicht existiert
	 */
	public function getUserData($type)
	{
		if ( ($this->userData[$type]<>'') || ($this->userData[$type]) )	return $this->userData[$type];
		else return false;
	}

	/**
	 * setUserData() setzt Daten zum angemeldeten User
	 * @param $key der Schl�ssel, unter dem die Daten abgelegt werden
	 * @param $value der zugeh�rige Wert
	 * @return true
	 */
	private function setUserData($key, $value)
	{
		$this->userData[$key] = $value;
	}

	/**
	 * authenticate() registriert einen User in der Session
	 * Dient gleichzeitig zum �berpr�fen von Nutzername & Passwort
	 * abschlie�end werden die Nutzerdaten in $userData abgelegt
	 * @param $username der Nutzername
	 * @param $password das Passwort
	 * @return true wenns geklappt hat, sonst false
	 */
	public function authenticate($username, $password)
	{
		$record = $this->db->fetchPreparedRecord($this->conf->getConfString('sql', 'session', 'authentication'), Array($username, $password));		
		//wenn der Record jetzt "leer" ist, hat die Authentifizierung NICHT geklappt - User und/oder Passwort passen nicht...
		
		if (($record['uid']=='') || (!$record['uid']))
		{
			return false;
		}
		else
		{
			//ansonsten: Die Nutzerdaten ins Session-Objekt �bernehmen
			foreach ($record AS $key => $value) $this->setUserData($key, $value);
			//die UID zus�tzlich getrennt ablegen:
			$this->uid = $record['uid'];
			//auch, ob der User Administrator ist:
			if ($record['type']=='admin') $this->admin = true;
			else $this->admin = false;

			return true;		
		}
	}

	/**
	 * destroy() zerst�rt die Session und meldet den User damit ab
	 * @return true wenn alles geklappt hat, sonst false
	 */	
	public function destroy()
	{
		//es kann einfach die entsprechende PHP-.Funktion benutzt werden
		return session_destroy();
	}
}
?>