<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakult�t
 * (c) 2006 Lehrstuhl f�r Wirtschaftsinformatik 3, Uni Erlangen-N�rnberg
 * R�ckfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: answer.php
 * $Revision: 1.9 $
 * Erstellt am: 15.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
/**
 * Die Klasse Answer repr�sentiert eine Antwortm�glichkeit auf eine Question,
 * falls diese vom Type "multiple choice", "single choice" oder "input_field" ist.
 * 
 * @author Michael Gottfried
 */
class Answer extends ModelHelper {
	
	private $quid = null; 
	private $answer = null;
	private $position = null;
	private $rating = null;
	
	private function __construct($anid) {
		parent::__construct($anid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enth�lt.
	 * @return Answer Das neu erzeugte Answer-Objekt.
	 */
	private function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen	
			$result = new Answer($row['anid']);
			$result->quid = $row['quid'];
			$result->answer = $row['answer'];
			$result->position = $row['position'];
			$result->rating = $row['rating'];
		}
		// Objekt zur�ckliefern
		return $result;
	}
	
	/**
	 * Liefert ein Answer-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $quid Question-ID, die das gew�nschte Objekt identifiert.
	 * @param int $anid Answer-ID, die das gew�nschte Objekt identifiert.
	 * @return Answer Das gew�nschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForId(ModelContext $context, $quid, $anid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'answer', 'getForId'), Array($anid, $quid));
		if ($resultSet == false) 
			throw new ModelException("Answer::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert alle Antwortm�glichkeiten f�r eine Frage.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $quid Die ID der Frage, deren Antwortm�glichkeiten gew�nscht werden.
	 * @return Array von Answer-Objekten
	 */
	public static function getForQuestion(ModelContext $context, $quid) {
		$result = null;
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'answer', 'getForQuestion'), Array($quid));
		if ($resultSet == false) 
			throw new ModelException("Answer::getForQuestion: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	/**
	 * Liefert alle Antworten, die ein Benutzer in einer Frage ausgew�hlt hat.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $quid Die ID der Frage, zu der die Antworten gew�nscht werden
	 * @param int $uid Die ID des Users, der die Frage beantwortet hat.
	 * @param int $attempt der Ausf�llversuch
	 * @return Array von Answer-Objekten
	 */
	public static function getForQuestionUser(ModelContext $context, $quid, $uid, $attempt) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'answer', 'getForQuestionUser'), Array($quid, $uid, $attempt));
		if ($resultSet == false) 
			throw new ModelException("Answer::getForQuestionUser: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$result = null;
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	public function getPosition() {
		return $this->position;
	}
	
	public function getRating() {
		return $this->rating;
	}
	
	public function getAnswer() {
		return $this->answer;
	}
	
	/**
	 * Speichert das Ausw�hlen dieser Antwortm�glichkeit f�r einen Benutzer und seinen Ausf�llversuch.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die Uid des Benutzers
	 * @param int %attempt Der Ausf�llversucht
	 */
	public function storeForUser(ModelContext $context, $uid, $attempt) {
		$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'answer', 'storeForUser'), Array($this->quid, $this->id, $uid, $attempt));
		if ($result == false) 
			throw new ModelException("Answer::storeForUser: Fehler beim Einf�gen in die Datenbank:<br>".$context->getDb()->getError(), 0);
	}
	
	/**
	 * Pr�ft, ob die Antwort f�r den angegebenen Benutzer ausgew�hlt worden ist.
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die Uid des Benutzers
	 * @param int %attempt Der Ausf�llversucht
	 * @return boolean true, wenn Antwort ausgew�hlt, sonst false.
	 */
	public function isSelectedForUser(ModelContext $context, $uid, $attempt) {
		$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'answer', 'isSelectedForUser'), Array($this->quid, $this->id, $uid, $attempt));
		if ($result == false) 
			throw new ModelException("Answer::isSelectedForUser: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		if (($row = $context->getDb()->fetch_array($result)) != false) 
			return $row['selected'];
		else
			throw new ModelException("Answer::isSelectedForUser: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
	}
}
?>
