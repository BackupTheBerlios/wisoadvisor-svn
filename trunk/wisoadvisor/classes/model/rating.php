<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: rating.php
 * $Revision: 1.4 $
 * Erstellt am: 15.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
/**
 * Die Klasse Rating stellt eine Zuordnung zwischen dem Ergebnis einer Umfrage
 * und der Auswertung her
 * 
 * @author Michael Gottfried
 */
class Rating extends ModelHelper {
	
	const LEVEL_POOR = 'poor';
	const LEVEL_AVERAGE = 'average';
	const LEVEL_GOOD = 'good';	
	const LEVEL_UNKNOWN = 'unknown';
	
	const TYPE_ABSOLUTE = 'absolute';
	const TYPE_BELOW_AVERAGE = 'below average';
	const TYPE_ABOVE_AVERAGE = 'above average';
	
	private $chId = null;
	private $lowerLimit = null;
	private $upperLimit = null;
	private $teId = null;
	private $tgid = null;
	private $level = null;
	
	private function __construct($raid) {
		parent::__construct($raid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enthält.
	 * @return Rating Das neu erzeugte User-Objekt.
	 */
	private function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen	
			$result = new Rating($row['raid']);
			$result->chId = $row['chid'];
			$result->lowerLimit = $row['lower_limit'];
			$result->upperLimit = $row['upper_limit'];
			$result->teId = $row['teid'];
			$result->tgid = $row['tgid'];
			$result->level = $row['level'];
		}
		// Objekt zurückliefern
		return $result;
	}
	
	/**
	 * Liefert ein Rating-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $raid Rating-ID, die das gewünschte Objekt identifiert.
	 * @return Rating Das gewünschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForId(ModelContext $context, $inid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'rating', 'getForId'), Array($inid));
		if ($resultSet == false) 
			throw new ModelException("Rating::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert alle TextElement-Objekte aus der Datenbank gelesen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return Array von TextElement-Objekten
	 */
	public static function getAll(ModelContext $context) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'rating', 'getAll'), Array());
		if ($resultSet == false) 
			throw new ModelException("Rating::getAll: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	
	/**
	 * Liefert ein Rating-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $chid Die ID des zu bewertenden Merkmals
	 * @param int $tgid Die ID der Zielgruppe
	 * @param int $result Das erzielte Ergebnis, anhand dessen das Rating bestimmt werden soll.
	 * @return Rating Das gewünschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForResult(ModelContext $context, $chid, $tgid, $result, $average) {
		// In DB suchen, ob existiert: Zunächst als statisches Rating
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'rating', 'getForResult'), Array($chid, $tgid, self::TYPE_ABSOLUTE, $result, $result));
		if ($resultSet == false) 
			throw new ModelException("Rating::getForResult: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		if ($context->getDb()->numRows($resultSet) > 0) {
			$row = $context->getDb()->fetch_array($resultSet);
			return self::getForDBRow($row);
		}
		if ($result >= $average) $type = self::TYPE_ABOVE_AVERAGE; else $type = self::TYPE_BELOW_AVERAGE;
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'rating', 'getForResult'), Array($chid, $tgid, $type, $result, $result));
		if ($resultSet == false) 
			throw new ModelException("Rating::getForResult: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	public function getLowerLimit() {
		return $this->lowerLimit;
	}
	
	public function getUpperLimit() {
		return $this->upperLimit;
	}
	
	public function getTeId() {
		return $this->teId;
	}
	
	public function getTextElement(ModelContext $context) {
		return TextElement::getForId($context, $this->teId);
	}
	
	public function getLevel() {
		return $this->level;
	}
	
	public function getTgId() {
		return $this->tgId;
	}
	
	public function getTargetGroup(ModelContext $context) {
		return TargetGroup::getForId($context, $this->tgid);
	}
	
}
?>
