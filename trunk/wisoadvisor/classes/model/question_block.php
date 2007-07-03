<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: question_block.php
 * $Revision: 1.7 $
 * Erstellt am: 23.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
 /**
 * Die Klasse QuestionBlock stellt einen Frageblock dar, der auf einmal angezeigt wird.
 * Fragen werden abhängig vom Typ unterschiedlich dargestellt.
 */
class QuestionBlock extends ModelHelper {
	
	private $title = null;
	private $sid = null;
	private $position = null;
	private $helptext = null;
	private $blocktpl = null;
	private $quotation = null;
	
	private function __construct($qtid) {
		parent::__construct($qtid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enthält.
	 * @return QuestionBlock Das neu erzeugte QuestionBlock-Objekt.
	 */
	private static function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen
			$result = new QuestionBlock($row['qbid']);
			$result->title = $row['qublock'];
			$result->sid = $row['sid'];
			$result->position = $row['position'];
			$result->helptext = $row['helptext'];
			$result->blocktpl = $row['blocktpl'];
			$result->quotation = $row['quotation'];
		}
		// Objekt zurückliefern
		return $result;
	}
	
	/**
	 * Liefert ein QuestionBlock-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $qtid QuestionType-ID, die das gewünschte Objekt identifiert.
	 * @return QuestionBlock Das gewünschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForId(ModelContext $context, $quid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question_block', 'getForId'), Array($quid));
		if ($resultSet == false) 
			throw new ModelException("QuestionBlock::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert alle QuestionBlock-Objekte aus der Datenbank gelesen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return Array von QuestionBlock-Objekten
	 */
	public static function getAll(ModelContext $context) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question_block', 'getAll'), Array());
		if ($resultSet == false) 
			throw new ModelException("QuestionBlock::getAll: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	/**
	 * Liefert alle Fragen-Blöcke, die zu einer Umfrage gehören.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $sid Die ID der Umfrage, deren Fragen gewünscht werden.
	 * @return Array von QuestionBlock-Objekten
	 */
	public static function getForSurvey(ModelContext $context, $sid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question_block', 'getForSurvey'), Array($sid));
		if ($resultSet == false) 
			throw new ModelException("QuestionBlock::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	
	/**
	 * Liefert den ERSTEN Fragen-Block, die zu einer Umfrage gehören.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $sid Die ID der Umfrage, deren Fragen gewünscht werden.
	 * @return QuestionBlock Erster in der Survey
	 */
	public static function getFirstForSurvey(ModelContext $context, $sid) {
		$result = null;		
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'question_block', 'getFirstForSurvey'), Array($sid, $sid));
		if ($resultSet == false) 
			throw new ModelException("QuestionBlock::getFirstForSurvey: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		if (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result = self::getForDBRow($row);
		}
		return $result;
	}
	
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getBlockTemplate() {
		return $this->blocktpl;
	}

	public function getQuotation() {
		return $this->quotation;
	}
	
	public function getHelpText() {
		return $this->helptext;
	}

	public function getSid() {
		return $this->sid;
	}
	
	public function getSurvey(ModelContext $context) {
		return Survey::getForId($context, $this->sid);
	}
	
	public function getPosition() {
		return $this->position;
	}
	
	/**
	 * Liefert den nächsten Frageblock in einer Survey zurück
	 */
	public function getSuccessor(ModelContext $context) {
		//TODO: Effizienter implementieren
		$result = null;
		$blocks = self::getForSurvey($context, $this->sid);
		foreach ($blocks as $block) {
			// Block steht nach aktuellem, aber vor dem bisherigen Ergebnis ?
			if ($block->position > $this->position and ($result == null or $block->position < $result->position)) {
				//echo "block=".$block->id." result=".$result->id;				
				$result = $block;
			}
		}
		return $result;
	}
	
	
	/**
	 * Liefert den nächsten Frageblock in einer Survey zurück
	 */
	public function getPredecessor(ModelContext $context) {
		//TODO: Effizienter implementieren
		$result = null;
		$blocks = self::getForSurvey($context, $this->sid);
		foreach ($blocks as $block) {
			// Block steht vor aktuellem, aber nach dem bisherigen Ergebnis ?
			if ($block->position < $this->position and ($result == null or $block->position > $result->position)) {
				//echo "block=".$block->id." result=".$result->id;				
				$result = $block;
			}
		}
		return $result;
	}
	
}
?>
