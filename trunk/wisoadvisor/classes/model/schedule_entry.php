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
class ScheduleEntry extends ModelHelper {
	
	private $uid = null;
	private $modid = null;
  private $mark_planned = null;
  private $mark_real = null;
  private $semester = null;
  private $sem_year = null;
  private $majid = null;
  private $mgrpid = null;
  private $mod_name = null;
  private $sws = null;
  private $ects = null;
  private $angebot_semester = null;
  private $default_semester = null;
  private $assessment = null;
	
  private function __construct($schid) {
		parent::__construct($schid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enthaelt.
	 * @return ScheduleEntry Das neu erzeugte User-Objekt.
	 */
	private function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen	
      $result = new ScheduleEntry($row['schid']);
      $result->uid = $row['uid'];
      $result->modid = $row['modid'];
      $result->mark_planned = $row['mark_planned'];
      $result->mark_real = $row['mark_real'];
      $result->semester = $row['semester'];
      $result->sem_year = $row['sem_year'];
      $result->majid = $row['majid'];
      $result->mgrpid = $row['mgrpid'];
      $result->mod_name = $row['name'];
      $result->sws = $row['sws'];
      $result->ects = $row['ects'];
      $result->angebot_semester = $row['angebot_semester'];
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
	 * @return ScheduleEntry Das Objekt
	 */
	public static function getNew(ModelContext $context) {
		return new ScheduleEntry(ScheduleEntry::ID_NEW);
	}
	

	/**
	 * Loescht alle ScheduleEntries fuer einen Benutzer
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die Uid des Benutzers
	 */
	public static function deleteForUser(ModelContext $context, $uid) {
		$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'schedule', 'deleteForUser'), Array($uid));
	  if (!$result) {
	    throw new ModelException("ScheduleEntry::storeInDb: Fehler beim Schreiben in die Datenbank:<br>".$context->getDb()->getError(), 0);
	  }
  }
	
	/**
	 * Liefert alle ScheduleEntry-Objekte aus der Datenbank gelesen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param $uid User ID
	 * @param $majid Major ID
	 * @return Array von ScheduleEntry-Objekten
	 */
	public static function getForUser(ModelContext $context, $uid, $majid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'schedule', 'getForUser'), Array($uid, $majid));
		if ($resultSet == false) 
			throw new ModelException("ScheduleEntry::getForUser: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
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
		//Fallunterscheidung: 'neues' Objekt wird neu angelegt, 'altes' geupdated
		$result = null;
		
		if ($this->id==ScheduleEntry::ID_NEW) {
			$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'schedule', 'storeInsert'), 
														Array($this->uid, 
														      $this->modid, 
														      $this->mark_planned, 
														      $this->semester, 
														      $this->sem_year));
			//zusaetzlich ggf. die "richtige" ID aus der DB gleich setzen:
			if ($result) $this->setId( $context->getDb()->lastId() );
			else throw new ModelException("ScheduleEntry::storeInDb: Fehler beim Einf�gen in die Datenbank:<br>".$context->getDb()->getError(), 0);

		} else {
			//UPDATE an DB schicken:
			$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'schedule', 'storeUpdate'), 
														Array($this->mark_planned, 
														      $this->semester, 
														      $this->sem_year, 
														      $this->id));
			if (!$result) 
				throw new ModelException("ScheduleEntry::storeInDb: Fehler beim Schreiben in die Datenbank:<br>".$context->getDb()->getError(), 0);
		}

		return true;
	}
	
	/*
	 * only getters, but no setters for additional information
	 */
	public function getMajId() {
    return $this->majid;	  
	}
	
	public function getModName() {
	  return $this->mod_name;
	}
	
  public function getSws() {
    return $this->sws;
  }
  
  public function getEcts() {
    return $this->ects;
  }

  public function getSemesterAngebot() {
    return $this->angebot_semester;
  }
  public function getSemesterDefault() {
    return $this->default_semester;
  }
  public function isAssessment() {
    return $this->asessment;
  }
	
	/*
	 * getters and setters for advisor__schedule entries
	 */
	public function setUserId($uid) {
    $this->uid = $uid;
  }
  public function getUserId() {
    return $this->uid;
  }
  
	public function setModId($modid) {
    $this->modid = $modid;
  }
  public function getModId() {
    return $this->modid;
  }

	public function setMarkPlanned($mark_planned) {
    $this->mark_planned = $mark_planned;
  }
  public function getMarkPlanned() {
    return $this->mark_planned;
  }

	public function setMarkReal($mark_real) {
    $this->mark_real = $mark_real;
  }
  public function getMarkReal() {
    return $this->mark_real;
  }

	public function setSemester($semester) {
    $this->semester = $semester;
  }
  public function getSemester() {
    return $this->semester;
  }

	public function setSemYear($sem_year) {
    $this->sem_year = $sem_year;
  }
  public function getSemYear() {
    return $this->sem_year;
  }
	
}
?>
