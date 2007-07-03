<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: question_type.php
 * $Revision: 1.3 $
 * Erstellt am: 22.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/

/**
 * Die Klasse QuestionType stellt einen Fragetyp dar.
 * Fragen werden abhängig vom Typ unterschiedlich dargestellt.
 */
class QuestionType extends ModelHelper {
	
	private $title = null;
	private $questionTemplate = null;
	private $answerTemplate = null;
	
	private function __construct($qtid) {
		parent::__construct($qtid);
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
			$result = new QuestionType($row['qtid']);
			$result->title = $row['questiontype'];
			$result->questionTemplate = $row['questiontpl'];
			$result->answerTemplate = $row['answertpl'];
		}
		// Objekt zurückliefern
		return $result;
	}
	
	/**
	 * Liefert ein QuestionType-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $qtid QuestionType-ID, die das gewünschte Objekt identifiert.
	 * @return QuestionType Das gewünschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForId(ModelContext $context, $quid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question_type', 'getForId'), Array($quid));
		if ($resultSet == false) 
			throw new ModelException("QuestionType::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert alle QuestionType-Objekte aus der Datenbank gelesen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return Array von QuestionType-Objekten
	 */
	public static function getAll(ModelContext $context) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question_type', 'getAll'), Array());
		if ($resultSet == false) 
			throw new ModelException("QuestionType::getAll: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getQuestionTemplate() {
		return $this->questionTemplate;
	}
	
	public function getAnswerTemplate() {
		return $this->answerTemplate;
	}
	
	public function isRated() {
		return $this->title != Question::TYPE_OPEN_INPUT;
	}
	
}
?>
