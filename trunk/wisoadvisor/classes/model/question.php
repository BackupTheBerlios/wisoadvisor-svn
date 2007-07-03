<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: question.php
 * $Revision: 1.12 $
 * Erstellt am: 15.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
/**
 * Die Klasse Question stellt eine Frage da, die zu einer Survey gehört.
 * Fragen haben unterschiedliche Typen und bewertete Fragen haben feste Antworten
 */
class Question extends ModelHelper {
	
	private $qtid = null;
	private $title = null;
	private $chid = null;
	private $required = null;
	private $qbid = null;
	private $position = null;
	
	const TYPE_MULTIPLE_CHOICE = 'multiplechoice';
	const TYPE_SINGLE_CHOICE = 'singlechoice';
	const TYPE_SINGLE_CHOICE_OTHER = 'singlechoiceother';
	const TYPE_TESTED_INPUT = 'testedinput';
	const TYPE_RESTRICTED_INPUT = 'restrictedinput';
	const TYPE_OPEN_INPUT = 'openinput';
	const TYPE_OPEN_TEXT = 'opentext';
	
	private function __construct($qid) {
		parent::__construct($qid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enthält.
	 * @return Question Das neu erzeugte Question-Objekt.
	 */
	private static function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen
			$result = new Question($row['quid']);
			$result->title = $row['question'];
			$result->qtid = $row['qtid'];
			$result->required = $row['required']=='true';
			$result->chid = $row['chid'];
			$result->qbid = $row['qbid'];
			$result->position = $row['position'];
		}
		// Objekt zurückliefern
		return $result;
	}
	
	/**
	 * Liefert ein Question-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $quid Question-ID, die das gewünschte Objekt identifiert.
	 * @return Question Das gewünschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForId(ModelContext $context, $quid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question', 'getForId'), Array($quid));
		if ($resultSet == false) 
			throw new ModelException("Question::getForId: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert alle Question-Objekte aus der Datenbank gelesen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return Array von Question-Objekten
	 */
	public static function getAll(ModelContext $context) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question', 'getAll'), Array());
		if ($resultSet == false) 
			throw new ModelException("Question::getAll: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	/**
	 * Liefert alle Fragen, die zu einem Fragenblock gehören.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $qbid Die ID des QuestionBlock, dessen Fragen gewünscht werden.
	 * @return Array von Question
	 */
	public static function getForQuestionBlock(ModelContext $context, $qbid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question', 'getForQuestionBlock'), Array($qbid));
		if ($resultSet == false) 
			throw new ModelException("Question::getForQuestionBlock: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	
	/**
	 * Liefert alle Fragen, die zu einer Umfrage gehören.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $sid Die ID der Umfrage, deren Fragen gewünscht werden.
	 * @return Array von Question
	 */
	public static function getForSurvey(ModelContext $context, $sid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question', 'getForSurvey'), Array($sid));
		if ($resultSet == false) 
			throw new ModelException("Question::getForSurvey: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getQtId() {
		return $this->qtId;
	}
	
	public function getQuestionType(ModelContext $context) {
		return QuestionType::getForId($context, $this->qtid);
	}
	
	public function getQbId() {
		return $this->qtId;
	}
	
	public function getQuestionBlock(ModelContext $context) {
		return QuestionBlock::getForId($context, $this->qbid);
	}
	
	public function getPosition() {
		return $this->position;
	}
	
	public function getChId() {
		return $this->chid;
	}
	
	public function getCharacteristic(ModelContext $context) {
		return Characteristic::getForId($context, $this->getId());
	}
	
	public function isRequired() {
		return $this->required;
	}
	
	/**
	 * Prüft, ob eine Frage in die Bewertung eingeht oder nicht.
	 * 
	 * @return boolean True, falls Frage bewertungsrelevant ist, sonst false.
	 */
	public function isRated(ModelContext $context) {
		return $this->getQuestionType($context)->isRated();
	}
	
	/**
	 * Liefert das Ergebnis eines Benutzers auf diese Frage.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die ID des Benutzers, dessen Ergebnis gewünscht wird.
	 * @param int $attempt der Ausfüllversuch - ist attempt=null, dann der aktuellste Versuch
	 * @return int Ergebnis
	 */
	public function getResultForUser(ModelContext $context, $uid, $attempt) {
		$result = 0; //Zwischensumme
		$answers = Answer::getForQuestionUser($context, $this->getId(), $uid, $attempt);
		if ($answers == null) return $result; // Keine Antwort gewählt
		foreach ($answers as $answer) {
			// Result aufaddieren und Antwortenzähler um 1 erhöhen
			$result += $answer->getRating();
		}
		// Antworten normieren:
		if ($result < 0) $result = 0;
		if ($result > 100) $result = 100;
		return $result;
	}
	
	/**
	 * Liefert das Durchschnittsergebnis aller Nutzer auf diese Frage
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $attempt (optional) Einschränken auf einen bestimmten Versuch, standardmäßig 1
	 * @return int Ergebnis
	 */
	public function getAverageResult(ModelContext $context, $attempt = 1) {
		$users = User::getAll($context);
		$userCount = 0;
		$result = 0;
		foreach ($users as $user) {
			$result += $this->getResultForUser($context, $user->getId(), $attempt);
			$userCount++;
		}
		if ($userCount > 0) 
			return $result / $userCount;
		return null;
	}
	
	/**
	 * Speichert das Ergebnis eines Benutzers auf eine offene Frage.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die ID des Benutzers, dessen Ergebnis gewünscht wird.
	 * @param String $answerText Antworttext
	 * @param int $attempt Nummer des Ausfüllversuchs
	 */
	public function storeOpenAnswer(ModelContext $context, $uid, $answerText, $attempt) {
		$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question', 'storeOpenAnswer'), Array($this->id, $uid, $answerText, $attempt));
		if ($result == false) 
			throw new ModelException("Question::storeOpenAnswer: Error inserting into Database:<br>".$context->getDb()->getError(), 0);
	}
	
	/**
	 * Liefert das Ergebnis eines Benutzers auf eine offene Frage.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die ID des Benutzers, dessen Ergebnis gewünscht wird.
	 * @param int $attempt Nummer des Ausfüllversuchs
	 * @return String Der Antworttext oder null
	 */
	public function getOpenAnswer(ModelContext $context, $uid, $attempt) {
		$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question', 'getOpenAnswer'), Array($this->id, $uid, $attempt));
		if ($result == false) 
			throw new ModelException("Question::getOpenAnswer: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		if (($row = $context->getDb()->fetch_array($result)) != false) 
			return $row['openresult'];
		else
			return null;
	}
	
	/**
	 * Löscht alle Antworten eines Users auf diese Frage für einen Ausfüllversuch
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die ID des Benutzers, dessen Ergebnis gewünscht wird.
	 * @param int $attempt Nummer des Ausfüllversuchs
	 */
	public function clearAnswers(ModelContext $context, $uid, $attempt) {
		$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question', 'clearAnswers'), Array($this->id, $uid, $attempt));
		if ($result == false) 
			throw new ModelException("Question::clearAnswers: Error clearing Database:<br>".$context->getDb()->getError(), 0);
		$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question', 'clearOpenAnswers'), Array($this->id, $uid, $attempt));
		if ($result == false) 
			throw new ModelException("Question::clearAnswers: Error clearing Database:<br>".$context->getDb()->getError(), 0);
	}
}
?>
