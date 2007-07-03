<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: answer.php
 * $Revision: 1.9 $
 * Erstellt am: 15.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
/**
 * Die Klasse Answer repräsentiert eine Antwortmöglichkeit auf eine Question,
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
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enthält.
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
		// Objekt zurückliefern
		return $result;
	}
	
	/**
	 * Liefert ein Answer-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $quid Question-ID, die das gewünschte Objekt identifiert.
	 * @param int $anid Answer-ID, die das gewünschte Objekt identifiert.
	 * @return Answer Das gewünschte Objekt oder null, falls kein Objekt mit diser ID existiert.
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
	 * Liefert alle Antwortmöglichkeiten für eine Frage.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $quid Die ID der Frage, deren Antwortmöglichkeiten gewünscht werden.
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
	 * Liefert alle Antworten, die ein Benutzer in einer Frage ausgewählt hat.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $quid Die ID der Frage, zu der die Antworten gewünscht werden
	 * @param int $uid Die ID des Users, der die Frage beantwortet hat.
	 * @param int $attempt der Ausfüllversuch
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
	 * Speichert das Auswählen dieser Antwortmöglichkeit für einen Benutzer und seinen Ausfüllversuch.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die Uid des Benutzers
	 * @param int %attempt Der Ausfüllversucht
	 */
	public function storeForUser(ModelContext $context, $uid, $attempt) {
		$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'answer', 'storeForUser'), Array($this->quid, $this->id, $uid, $attempt));
		if ($result == false) 
			throw new ModelException("Answer::storeForUser: Fehler beim Einfügen in die Datenbank:<br>".$context->getDb()->getError(), 0);
	}
	
	/**
	 * Prüft, ob die Antwort für den angegebenen Benutzer ausgewählt worden ist.
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die Uid des Benutzers
	 * @param int %attempt Der Ausfüllversucht
	 * @return boolean true, wenn Antwort ausgewählt, sonst false.
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
