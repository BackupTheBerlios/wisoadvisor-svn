<?php
/***********************************************************************************
 * WiSo@visor v2 - Performance Optimizer
 * (c) 2007 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 *
 * Datei: userparameter.php
 * $Revision: 1.6 $
 * Erstellt am: 09.10.2007
 * Erstellt von: Florian Mattes
 ***********************************************************************************/

/** 
 * Die Klasse UserParameter repräsentiert jeden Benutzerparameter
 * 
 * @author Florian Mattes
 */ 
class UserParameter extends ModelHelper {
	
	private $uid = null;
  private $key1 = null;
	private $key2 = null;
	private $key3 = null;
	private $value = null;
	
	private function __construct($uid) {
		parent::__construct($uid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enthaelt.
	 * @return UserParameter Das neu erzeugte UserParameter-Objekt.
	 */
	private function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen	
			$result = new UserParameter($row['upid']);
			$result->uid = $row['uid'];
			$result->key1 = $row['key1'];
			$result->key2 = $row['key2'];
			$result->key3 = $row['key3'];
			$result->value = $row['value'];
		} 
		// Objekt zurueckliefern
		return $result;
	}
	
	/**
	 * Liefert ein UserParameter-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $upid UserParameter-ID, die das gewuenschte Objekt identifiert.
	 * @return UserParameter Das gewuenschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForId(ModelContext $context, $upid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'userparameter', 'getForId'), Array($upid));
		if ($resultSet == false) 
			throw new ModelException("UserParameter::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert ein UserParameter-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid User-ID, die das gewuenschte Objekt identifiert.
	 * @param String $key1 Key
	 * @param String $key2 Key
	 * @param String $key3 Key
	 * @return UserParameter Das gewuenschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getOneForUser(ModelContext $context, $uid, $key1, $key2, $key3) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'userparameter', 'getOneForUser'), Array($uid, $key1, $key2, $key3));
		if ($resultSet == false) 
			throw new ModelException("UserParameter::getOneForUser: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert alle UserParameter für einen User, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid User-ID, die das gewuenschte Objekt identifiert.
	 * @return UserParameter Das gewuenschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForUser(ModelContext $context, $uid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'userparameter', 'getForId'), Array($uid));
		if ($resultSet == false) 
			throw new ModelException("UserParameter::getForUser: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result[] = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert ein neues, leeres User-Objekt mit id = 'new'
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return User Das Objekt
	 */
	public static function getNew(ModelContext $context) {
		return new UserParameter(UserParameter::ID_NEW);
	}
	
	/**
	 * speichert das Userobjekt wieder neu in der DB ab
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return true, wenn alles geklappt hat
	 * @throws ModelException
	 */
	public function storeInDb(ModelContext $context)
	{
		//Fallunterscheidung: 'neues' Objekt wird neu angelegt, 'altes' geupdated
		$result = null;
		if ($this->id==User::ID_NEW) {
			$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'userparameter', 'storeInsert'), 
														Array($this->uid, 
														      $this->key1, 
														      $this->key2, 
														      $this->key3, 
														      $this->value));
			//zusaetzlich ggf. die "richtige" ID aus der DB gleich setzen:
			if ($result) $this->setId( $context->getDb()->lastId() );
			else throw new ModelException("UserParameter::storeInDb: Fehler beim Einfügen in die Datenbank:<br>".$context->getDb()->getError(), 0);
		} else {
			//UPDATE an DB schicken:
			$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'userparameter', 'storeUpdate'), 
														Array($this->uid, 
														      $this->key1, 
														      $this->key2, 
														      $this->key3, 
														      $this->value,
														      $this->id));
			if (!$result) 
				throw new ModelException("UserParameter::storeInDb: Fehler beim Schreiben in die Datenbank:<br>".$context->getDb()->getError(), 0);
		}

		return true;
	}
	
	public function setKeys($key1, $key2, $key3) {
	  $this->setKey1($key1);
	  $this->setKey2($key2);
		$this->setKey3($key3);
	}
	
	public function getKey1() {
    return $this->key1;
	}
	
	public function getKey2() {
    return $this->key2;
	}
	
	public function getKey3() {
    return $this->key3;
	}

	public function setKey1($key1) {
	  $this->key1 = $key1;
	}

	public function setKey2($key2) {
	  $this->key2 = $key2;
	}

	public function setKey3($key3) {
	  $this->key3 = $key3;
	}
  
	public function getValue() {
	  return $this->value;
	}
	
	public function setValue($value) {
	  $this->value = $value;
	}
	
	public function getUserId() {
	  return $this->uid;
	}
	
	public function setUserId($uid) {
	  $this->uid = $uid;
	}
	
}
?>
