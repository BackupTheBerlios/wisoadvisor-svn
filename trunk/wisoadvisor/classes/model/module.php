<?php
/***********************************************************************************
 * WiSo@visor v2 - Studienmanagementsystem (Pruefungsplan)
 * (c) 2007 Lehrstuhl fuer Wirtschaftsinformatik 3, Uni Erlangen-Nuernberg
 *
 * Datei: schedule_entry.php
 * $Revision: 1.6 $
 * Erstellt am: 30.08.2007
 * Erstellt von: Florian Mattes
 ***********************************************************************************/

/** 
 * Die Klasse ScheduleEntry einen Eintrag im Pruefungsplan
 * 
 * @author Florian MAttes
 */ 
class Module extends ModelHelper {
	
  private $stid = null;
	private $majid = null;
  private $mgrpid = null;
	private $alid = null;
  private $default_semester = null;
  private $assessment = null;
  
  private function __construct($modid) {
		parent::__construct($modid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enthaelt.
	 * @return Module Das neu erzeugte User-Objekt.
	 */
	private function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen	
      $result = new Module($row['modid']);
      $result->stid = $row['stid'];
      $result->majid = $row['majid'];
      $result->mgrpid = $row['mgrpid'];
      $result->alid = $row['alid'];
      $result->default_semester = $row['default_semester'];
      $result->assessment = $row['assessment'];
		}
		// Objekt zurueckliefern
		return $result;
	}
	
	/**
	 * Liefert ein neues, leeres User-Objekt mit id = 'new'
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return User Das Objekt
	 */
	public static function getNew(ModelContext $context) {
		return new Module(Module::ID_NEW);
	}
	

	/**
	 * Liefert ein Modul anhand seiner ID
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param $modid Module ID
	 * @return Array von Module-Objekten
	 */
	public static function getForId(ModelContext $context, $modid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'module', 'getForId'), Array($modid));
		if ($resultSet == false) 
			throw new ModelException("Module::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	/**
	 * Liefert alle Module fuer einen Schwerpunkt
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param $majid Major ID
	 * @return Array von Module-Objekten
	 */
	public static function getForMajor(ModelContext $context, $majid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'module', 'getForMajor'), Array($majid));
		if ($resultSet == false) 
			throw new ModelException("Module::getForMajor: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	

  public function getSemesterDefault() {
    return $this->default_semester;
  }
  
  public function getMajId() {
    return $this->majid;	  
	}
	
  public function isAssessment() {
    return $this->asessment;
  }
	
  public function setStId($stid) {
    $this->stid = $stid;
  }

  public function getStId() {
    return $this->stid;
  }

  public function setAlId($alid) {
    $this->alid = $alid;
  }

  public function getAlId() {
    return $this->alid;
  }

  public function setMgrpId($mgrpid) {
    $this->mgrpid = $modid;
  }

  public function getMgrpId() {
    return $this->mgrpid;
  }

  
  }
?>
