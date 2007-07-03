<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: user.php
 * $Revision: 1.6 $
 * Erstellt am: 15.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/

/**
 * Die Klasse User repräsentiert jeden Benutzer, der sich am System anmelden kann.
 * 
 * @author Michael Gottfried
 */ 
class User extends ModelHelper {
	
	private $type = null;
	private $userName = null;
	private $eMail = null;
	private $password = null;
	private $tgId = null;
	private $confirmed = null;
	private $authCode = null;
	private $gender = null;
	private $birthday = null;
	
	private function __construct($uid) {
		parent::__construct($uid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enthält.
	 * @return User Das neu erzeugte User-Objekt.
	 */
	private function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen	
			$result = new User($row['uid']);
			$result->type = $row['type'];
			$result->eMail = $row['email'];
			$result->userName = $row['username'];
			$result->password = $row['passwd'];
			$result->tgId = $row['tgid'];
			$result->confirmed = $row['confirmed'] == 'true';
			$result->authCode = $row['auth_code'];
			$result->gender = $row['gender'];
			$result->birthday = $row['birthday'];
		}
		// Objekt zurückliefern
		return $result;
	}
	
	/**
	 * Liefert ein User-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid User-ID, die das gewünschte Objekt identifiert.
	 * @return User Das gewünschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForId(ModelContext $context, $uid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'user', 'getForId'), Array($uid));
		if ($resultSet == false) 
			throw new ModelException("User::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert ein neues, leeres User-Objekt mit id = 'new'
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return User Das Objekt
	 */
	public static function getNew(ModelContext $context) {
		return new User(User::ID_NEW);
	}
	
	/**
	 * Liefert alle User-Objekte aus der Datenbank gelesen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return Array von User-Objekten
	 */
	public static function getAll(ModelContext $context) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'user', 'getAll'), Array());
		if ($resultSet == false) 
			throw new ModelException("User::getAll: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
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
		$confirmed = $this->isConfirmed() ? 'true' : 'false';
		//Fallunterscheidung: 'neues' Objekt wird neu angelegt, 'altes' geupdated
		$result = null;
		if ($this->id==User::ID_NEW)
		{
			$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'user', 'storeInsert'), 
														Array($this->userName, $this->eMail, $this->password, $this->gender, $this->birthday, $this->tgId, $confirmed, $this->authCode));
			//zusätzlich ggf. die "richtige" ID aus der DB gleich setzen:
			if ($result) $this->setId( $context->getDb()->lastId() );
			else throw new ModelException("User::storeInDb: Fehler beim Einfügen in die Datenbank:<br>".$context->getDb()->getError(), 0);
		}
		else
		{
			//UPDATE an DB schicken:
			$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'user', 'storeUpdate'), 
														Array($this->userName, $this->eMail, $this->password, $this->gender, $this->birthday, $this->tgId, $confirmed, $this->authCode, $this->type, $this->id));
			if (!$result) 
				throw new ModelException("User::storeInDb: Fehler beim Schreiben in die Datenbank:<br>".$context->getDb()->getError(), 0);
		}

		return true;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function getEMail() {
		return $this->eMail;
	}
	
	public function getPassword() {
		return $this->password;
	}
	
	public function getTgId() {
		return $this->tgId;
	}
	
	public function isConfirmed() {
		return $this->confirmed;
	}
	
	public function getAuthCode() {
		return $this->authCode;
	}
	
	public function getGender() {
		return $this->gender;
	}
	
	public function getBirthday() {
		return $this->birthday;
	}
	
	public function getUserName() {
		return $this->userName;
	}

	public function setType($type) {
		$this->type = $type;
	}
	
	public function setEMail($email) {
		$this->eMail = $email;
	}
	
	public function setPassword($pwd) {
		$this->password = $pwd;
	}
	
	public function setTgId($tgid) {
		$this->tgId = $tgid;
	}
	
	public function setConfirmed($confirmed) {
		$this->confirmed = $confirmed;
	}
	
	public function setAuthCode($authCode) {
		$this->authCode = $authCode;
	}
	
	public function setGender($gender) {
		$this->gender = $gender;
	}
	
	public function setBirthday($birthday) {
		$this->birthday = $birthday;
	}
	
	public function setUserName($username) {
		$this->userName = $username;
	}
}
?>
