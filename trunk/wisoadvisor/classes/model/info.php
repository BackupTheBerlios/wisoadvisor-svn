<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakult�t
 * (c) 2006 Lehrstuhl f�r Wirtschaftsinformatik 3, Uni Erlangen-N�rnberg
 * R�ckfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: survey.php
 * $Revision: 1.3 $
 ***********************************************************************************/
 
/**
 * Die Klasse Info repr�sentiert alle Informationen f�r eine Infoseite
 * 
 * @author Florian Strecker
 */
class Info extends ModelHelper {
	
	private $title = null;
	private $blid = null;
	private $position = null;
	private $sid = null;
	private	$shortinfo = null;
	private	$longinfo = null;
	
	private function __construct($inid) {
		parent::__construct($inid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enth�lt.
	 * @return Info Das neu erzeugte Info-Objekt.
	 */
	private static function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen	
			$result = new Info($row['inid']);
			$result->title = $row['title'];
			$result->blid = $row['blid'];
			$result->position = $row['position'];
			$result->sid = $row['sid'];
			$result->shortinfo = $row['shortinfo'];
			$result->longinfo = $row['longinfo'];
		}
		// Objekt zur�ckliefern
		return $result;
	}
	
	/**
	 * Liefert ein Info-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $inid Info-ID, die das gew�nschte Objekt identifiert.
	 * @return Info Das gew�nschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForId(ModelContext $context, $inid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'info', 'getForId'), Array($inid));
		if ($resultSet == false) 
			throw new ModelException("Info::getForId: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert ein Info-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $sid Survey-ID, die das gew�nschte Objekt identifiziert.
	 * @return Info Das gew�nschte Objekt oder null, falls kein Objekt mit dieser ID existiert.
	 */
	public static function getForSid(ModelContext $context, $sid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'info', 'getForSid'), Array($sid));
		if ($resultSet == false) 
			throw new ModelException("Info::getForSid: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert alle Info-Objekte aus der Datenbank.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return Array von Info-Objekten
	 */
	public static function getAll(ModelContext $context) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'info', 'getAll'), Array());
		if ($resultSet == false) 
			throw new ModelException("Info::getAll: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	/**
	 * Liefert alle Info-Objekte aus der Datenbank, die zu einem SurveyBlock geh�ren.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $blid Die ID des Blockes, zu dem die Infos geh�ren m�ssen.
	 * @return Array von Info-Objekten
	 */
	public static function getForBlock(ModelContext $context, $blid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'info', 'getForBlock'), Array($blid));
		if ($resultSet == false) 
			throw new ModelException("Info::getForBlock: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getBlId() {
		return $this->blid;
	}
	
	public function getBlock(ModelContext $context) {
		return SurveyBlock::getForId($context, $this->blid);
	}
	
	public function getPosition() {
		return $this->position;
	}
	
	public function getSId() {
		return $this->sid;
	}
	
	public function getShortInfo() {
		return $this->shortinfo;
	}
	
	public function getLongInfo() {
		return $this->longinfo;
	}
	
	/**
	 * Liefert die n�chste Infoseite in der Bearbeitungsreihenfolge, die zum aktuellen Block geh�rt
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param Info n�chste Info oder null, falls letzte
	 */
	public function getSuccessor(ModelContext $context) {
		$next = null;
		// Zuerst innerhalb des aktuellen SurveyBlock Nachfolger suchen:
		$infos = Info::getForBlock($context, $this->getBlid());
		foreach ($infos as $info) {
			if ($info->getPosition() > $this->getPosition() and // gef. Info kommt nach aktueller
				($next == null or $next->getPosition() > $info->getPosition())) // noch kein Erg. oder neuer Erg. besser
				$next = $info;
		}
		return $next; 
	}

	/**
	 * Liefert due ERSTE Infoseite in einem Block.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $blid Die ID des Umfrageblocks, dessen Infoseiten gew�nscht werden.
	 * @return Info Erste Infoseite im Block
	 */
	public static function getFirstForSurveyBlock(ModelContext $context, $blid) {
		$result = null;		
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'info', 'getFirstForSurveyBlock'), Array($blid));
		if ($resultSet == false) 
			throw new ModelException("Info::getFirstForSurveyBlock: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		if (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result = self::getForDBRow($row);
		}
		return $result;
	}
	
}
?>
