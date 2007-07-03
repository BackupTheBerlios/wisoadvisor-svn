<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: characteristic.php
 * $Revision: 1.7 $
 * Erstellt am: 15.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
/**
 * Die Klasse Characteristic repräsentiert ein Merkmal, das über bestimmte Fragen innerhalb eines
 * Surveys abgeprüft wird.
 * 
 * @author Michael Gottfried
 */
class Characteristic extends ModelHelper {
	
	const SHOW_RESULT_NO = 'no';
	const SHOW_RESULT_TEXT_ONLY = 'text_only';
	const SHOW_RESULT_ABSOLUTE = 'absolute';
	const SHOW_RESULT_RELATIVE = 'relative';
	
	private $title = null;
	private $gid = null;
	private $lowerTarget = null;
	private $upperTarget = null;
	private $showResult = null;
	
	private function __construct($chid) {
		parent::__construct($chid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enthält.
	 * @return Characteristic Das neu erzeugte Characteristic-Objekt.
	 */
	private function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen	
			$result = new Characteristic($row['chid']);
			$result->title = $row['characteristic'];
			$result->gid = $row['gid'];
			$result->lowerTarget = $row['lower_target'];
			$result->upperTarget = $row['upper_target'];
			$result->showResult = $row['show_result'];
		}
		// Objekt zurückliefern
		return $result;
	}
	
	/**
	 * Liefert ein Characteristic-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $chid Characteristic-ID, die das gewünschte Objekt identifiert.
	 * @return Characteristic Das gewünschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForId(ModelContext $context, $chid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'characteristic', 'getForId'), Array($chid));
		if ($resultSet == false) 
			throw new ModelException("Characteristic::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert alle Characteristic-Objekte aus der Datenbank gelesen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return Array von Characteristic-Objekten
	 */
	public static function getAll(ModelContext $context) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'characteristic', 'getAll'), Array());
		if ($resultSet == false) 
			throw new ModelException("Characteristic::getAll: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	/**
	 * Liefert alle Mermale, für die in einer Umfrage geprüft werden. Dazu werden die Fragen der Survey untersucht.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $sid Die ID der Survey, deren Merkmale gewünscht werden.
	 * @return Array von Characteristic-Objekten
	 */
	public static function getForSurvey(ModelContext $context, $sid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'characteristic', 'getForSurvey'), Array($sid));
		if ($resultSet == false) 
			throw new ModelException("Characteristic::getForSurvey: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$result = null;
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	
	/**
	 * Liefert alle Mermale, für die in einer Umfrage geprüft werden. Dazu werden die Fragen der Survey untersucht.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $sid Die ID der Survey, deren Merkmale gewünscht werden.
	 * @return Array von Characteristic-Objekten
	 */
	public static function getForBlock(ModelContext $context, $blid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'characteristic', 'getForBlock'), Array($blid));
		if ($resultSet == false) 
			throw new ModelException("Characteristic::getForBlock: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$result = null;
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getGId() {
		return $this->gid;
	}
	
	public function getGroup(ModelContext $context) {
		return Group::getForId($context, $this->gid);
	}
	
	public function getLowerTarget() {
		return $this->lowerTarget;
	}
	
	public function getUpperTarget() {
		return $this->upperTarget;
	}
	
	public function getShowResult() {
		return $this->showResult;
	}
}
?>
