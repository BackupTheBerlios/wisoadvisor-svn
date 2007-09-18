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
  private $try = null;
  private $alid = null;
  private $stid = null;
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
      $result->try = $row['try'];
      $result->alid = $row['alid'];
      $result->stid = $row['stid'];
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
	    throw new ModelException("ScheduleEntry::deleteForUser: Fehler beim Schreiben in die Datenbank:<br>".$context->getDb()->getError(), 0);
	  }
  }
	
	/**
	 * Liefert ein ScheduleEntry-Objekte aus der Datenbank gelesen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param $schid ScheduleEntry ID
	 * @return ScheduleEntry-Objekt
	 */
	public static function getForId(ModelContext $context, $schid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'schedule', 'getForId'), Array($schid));
		if ($resultSet == false) 
			throw new ModelException("ScheduleEntry::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result = self::getForDBRow($row);
		}
		return $result;
	}
	
  /**
	 * Liefert alle ScheduleEntry-Objekte aus der Datenbank, geordnet nach Semester.
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
	 * Liefert alle ScheduleEntry-Objekte aus der Datenbank, geordnet nach Bereichen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param $uid User ID
	 * @param $majid Major ID
	 * @return Array von ScheduleEntry-Objekten
	 */
	public static function getForUserGrouped(ModelContext $context, $uid, $majid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'schedule', 'getForUserGrouped'), Array($uid, $majid));
		if ($resultSet == false) 
			throw new ModelException("ScheduleEntry::getForUser: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	/**
	 * speichert das ScheduleEntry-Objekt wieder neu in der DB ab
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
														      $this->mark_real,
														      $this->semester, 
														      $this->sem_year,
														      $this->try,
														      $this->alid,
														      $this->stid));
			//zusaetzlich ggf. die "richtige" ID aus der DB gleich setzen:
			if ($result) $this->setId( $context->getDb()->lastId() );
			else throw new ModelException("ScheduleEntry::storeInDb: Fehler beim Einfügen in die Datenbank:<br>".$context->getDb()->getError(), 0);

		} else {
			//UPDATE an DB schicken:
			$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'schedule', 'storeUpdate'), 
														Array($this->mark_planned, 
														      $this->mark_real,
														      $this->semester, 
														      $this->sem_year,
														      $this->try,
														      $this->alid,
														      $this->stid,
														      $this->id));
			if (!$result) 
				throw new ModelException("ScheduleEntry::storeInDb: Fehler beim Schreiben in die Datenbank:<br>".$context->getDb()->getError(), 0);
		}

		return true;
	}
	
	/*
	 * only getters, but no setters for additional information
	 */
  public function getMgrpId() {
    return $this->mgrpid;	  
	}
	
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
    return $this->assessment;
  }
	
	/*
	 * getters and setters for advisor__schedule entries
	 */
	public function setTry($try) {
    $this->try = $try;
  }
  public function getTry() {
    return $this->try;
  }
  
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
  
  public function setAlid($alid) {
    $this->alid = $alid;
  }
  
  public function getAlid() {
    return $this->alid;
  }
  
  public function setStid($stid) {
    $this->stid = $stid;
  }
  
  public function getStid() {
    return $this->stid;
  }
  
  public function isMoveableUpwards($user) {
    
    $ret = false;    
	  $realSemCalc = new SemesterCalculator(); // tatsaechliches, aktuelles semester (initialisiert ueber date)
	  $entrySemCalc = new SemesterCalculator(); // semester, fuer das der eintrag eingeplant wurde (siehe advisor__schedule)
		$entrySemCalc->setSemesterWord($this->semester);
		$entrySemCalc->setSemesterYear($this->sem_year);
		$startSemCalc = new SemesterCalculator(); // semester, in dem der user zu studieren angefangen hat (siehe advisor__user)
	  $startSemCalc->setBoth($user->getSemStart());
    
    // eine nach oben verschiebbare pruefung ...
	  if ($this->mark_real <= 0) { // ... darf noch nicht abgelegt worden sein ...
      if ($this->try == 1) { // ... darf keine wiederholungspruefung sein ...
        if ($entrySemCalc->compare($realSemCalc) > 0) { // ... muss in der zukunft liegen ...
		      if ($entrySemCalc->compare($startSemCalc) > 0) { // ... und darf nicht vor das startsemester geschoben werden
		        $ret = true;
		      }
		    }
      }
    }	     
    return $ret;
  }
	
  public function isMoveableDownwards($user) {
    
    $ret = false; 
	  $entrySemCalc = new SemesterCalculator(); // semester, fuer das der eintrag eingeplant wurde (siehe advisor__schedule)
		$entrySemCalc->setSemesterWord($this->semester);
		$entrySemCalc->setSemesterYear($this->sem_year);
    
    $maxAssSemCalc = new SemesterCalculator();
    $maxAssSemCalc->setBoth($user->getSemStart());
    $maxAssSemCalc->addSemester(2);
    
	  // eine nach unten verschiebbare pruefung ...
	  if ($this->mark_real <= 0) { // ... darf noch nicht abgelegt worden sein ...
      if ($this->try == 1) { // ... darf keine wiederholungspruefung sein ...
        if ($this->assessment == 'true') { // ... und muss im falle einer assessment-pruefung ...
          if ($entrySemCalc->compare($maxAssSemCalc) < 0) { // ... vor dem 3. semester abgelegt sein ...
            $ret = true;
          }
		    } else { // ... bei bachelor-pruefungen sehen wir das nicht so eng wie die PO
		      $ret = true;
		    }
      }
    }	     
    
    return $ret;

  }
	
}
?>
