<?php
/***********************************************************************************
 * WiSo@visor v2 - Studienmanagementsystem (Pruefungsplan)
 * (c) 2007 Lehrstuhl fuer Wirtschaftsinformatik 3, Uni Erlangen-Nuernberg
 *
 * Datei: schedule_entry_statistics.php
 * $Revision: 1.6 $
 * Erstellt am: 12.09.2007
 * Erstellt von: Florian Mattes
 ***********************************************************************************/

/** 
 * Die Klasse ScheduleEntryStatistics enthaelt diverse Datenbankroutinen, um statistische Werte fuer die
 * Eintraege des Pruefungsplanes zu ermitteln (Durchschnittsnoten, mit verschiedenen Bezugspunkten). 
 * 
 * @author Florian Mattes
 */ 
class ScheduleEntryStatistics {
  
  const AGGREGATION_LECTURE = 'lecture';
  const AGGREGATION_STUDIES = 'studies';
  const AGGREGATION_MAJOR = 'major';
	
	protected function getArrayFromDb(ModelContext $context, ScheduleEntry $entry, $sql, $paramArray) {

    $resultSet = $context->getDb()->preparedQuery($sql, $paramArray);
		if ($resultSet == false) 
			throw new ModelException("ScheduleEntryStatistics::getArrayFromDb: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = $row;
		}
		return $result;
	}

  protected function getFromDb(ModelContext $context, ScheduleEntry $entry, $sql, $paramArray) {

    $resultSet = $context->getDb()->preparedQuery($sql, $paramArray);
		if ($resultSet == false) 
			throw new ModelException("ScheduleEntryStatistics::getFromDb: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result = $row['avg_mark'];
		}
		return $result;
	}

	protected function getSemesterForPlannedMarks(ScheduleEntry $entry) {
	  
	  $ret = null;
	  $hCurSem = new SemesterCalculator();
	  $hEntrySem = new SemesterCalculator();
	  $hEntrySem->setSemesterWord($entry->getSemester());
	  $hEntrySem->setSemesterYear($entry->getSemYear());
	  
	  // wenn note vorhanden, dann durchschnitt des betreffenden semesters anzeigen
	  if ($entry->getMarkReal() > 0) {
	    $ret = $hEntrySem;
	  // ansonsten einfach den durchschnittswert des vergangenen vorlesungszyklus (-2 bei ws/ss, -1 bei both)
	  } else {	    
	    $hCurSem->addSemester($entry->getSemesterAngebot()=='both'?-1:-2);
	    $ret = $hCurSem;
	  }
	  return $ret;
	}
	
	
/* #################################################################################################################################################
 * GEPLANTE NOTEN, Durchschnitt
 */


	/**
	 * Liefert die durchschnittliche Plannote einer Veranstaltung
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param ScheduleEntry $entry ScheduleEntry
	 * @return durchschnittliche Plannote
	 */
	public static function getAvgPlanByLecture (ModelContext $context, ScheduleEntry $entry) {
	  
	  $hCalc = self::getSemesterForPlannedMarks($entry);
	  
    return self::getFromDb($context, 
                           $entry, 
                           $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getAvgPlanByLecture'), 
                           Array($entry->getAlid(),
                                 $hCalc->getSemesterWord(),
                                 $hCalc->getSemesterYear()));
  }
  
	/**
	 * Liefert die durchschnittliche Plannote einer Veranstaltung bezogen auf den Studiengang
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param ScheduleEntry $entry ScheduleEntry
	 * @return durchschnittliche Plannote
	 */
  public static function getAvgPlanByStudies (ModelContext $context, ScheduleEntry $entry) {
	  
    $hCalc = self::getSemesterForPlannedMarks($entry);
	  
    return self::getFromDb($context, 
                           $entry, 
                           $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getAvgPlanByStudies'), 
                           Array($entry->getAlid(),
                                 $hCalc->getSemesterWord(),
                                 $hCalc->getSemesterYear()));
  }

	/**
	 * Liefert die durchschnittliche Plannote einer Veranstaltung bezogen auf den Studienschwerpunkt
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param ScheduleEntry $entry ScheduleEntry
	 * @return durchschnittliche Plannote
	 */
  public static function getAvgPlanByMajor (ModelContext $context, ScheduleEntry $entry) {
	  
    $hCalc = self::getSemesterForPlannedMarks($entry);
	  
    return self::getFromDb($context, 
                           $entry, 
                           $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getAvgPlanByMajor'),
                           Array($entry->getModId(),
                                 $hCalc->getSemesterWord(),
                                 $hCalc->getSemesterYear()));
  }

  
/* #################################################################################################################################################
 * NOTENVERTEILUNG (gesamtgesamt :-))
 */
  /**
	 * Liefert die Notenverteilung
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param ScheduleEntry $entry ScheduleEntry
	 * @param Aggregierung der Verteilung (siehe Konstanten)
	 * @return Array mit Notenverteilung [mark_real] [cnt_mark]
	 */
public static function getCntReal (ModelContext $context, ScheduleEntry $entry, $sAggregationLevel) {
	  
	  switch ($sAggregationLevel) {
	    
	    case ScheduleEntryStatistics::AGGREGATION_LECTURE:
	      $ret = self::getCntRealByLecture($context, $entry);
	      break;
	      
	    case ScheduleEntryStatistics::AGGREGATION_STUDIES:
	      $ret = self::getCntRealByStudies($context, $entry);
	      break;
	    
	    case ScheduleEntryStatistics::AGGREGATION_MAJOR:
	      $ret = self::getCntRealByMajor($context, $entry);
	      break;
	    
	    default:
	      $ret = self::getCntRealByLecture($context, $entry);
	      
	  }
	  return $ret;
	}


/* #################################################################################################################################################
 * NOTENVERTEILUNG (gesamt)
 */


  /**
	 * Liefert die Notenverteilung einer Veranstaltung (gesamt)
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param ScheduleEntry $entry ScheduleEntry
	 * @return Array mit Notenverteilung [mark_real] [cnt_mark]
	 */
	public static function getCntRealByLecture (ModelContext $context, ScheduleEntry $entry) {

	  $ret=-1;
 	  if ($context->getConf()->getConfString('ucImporter', 'useimportedmarks') == 'true') {
	    $ret = self::getAvgRealFromImportByLecture($context, $entry);
	  } else {
	    $ret = self::getCntRealFromScheduleByLecture($context, $entry);
	  }
	  return $ret;
  }
  
  protected function getCntRealFromScheduleByLecture (ModelContext $context, ScheduleEntry $entry) {
    
    return self::getArrayFromDb($context, 
                                $entry, 
                                $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getCntRealFromScheduleByLecture'), 
                                Array($entry->getAlid(), 
                                      $entry->getSemester(), 
                                      $entry->getSemYear()));
  }
  
  protected function getCntRealFromImportByLecture (ModelContext $context, ScheduleEntry $entry) {
    
    return self::getArrayFromDb($context, 
                                $entry, 
                                $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getCntRealFromImportByLecture'), 
                                Array($entry->getAlid(), 
                                      $entry->getSemester(), 
                                      $entry->getSemYear()));
  }  
  
  
/* #################################################################################################################################################
 * NOTENVERTEILUNG (Studiengang)
 */


  /**
	 * Liefert die Notenverteilung einer Veranstaltung (Studiengang)
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param ScheduleEntry $entry ScheduleEntry
	 * @return Array mit Notenverteilung [mark_real] [cnt_mark]
	 */
	public static function getCntRealByStudies (ModelContext $context, ScheduleEntry $entry) {

	  $ret=-1;
	  if ($context->getConf()->getConfString('ucImporter', 'useimportedmarks') == 'true') {
	     $ret = self::getCntRealFromImportByStudies($context, $entry);
	  } else {
	    $ret = self::getCntRealFromScheduleByStudies($context, $entry);
	  }
	  return $ret;
  }
  
  protected function getCntRealFromScheduleByStudies (ModelContext $context, ScheduleEntry $entry) {
    return self::getArrayFromDb($context, 
                           $entry, 
                           $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getCntRealFromScheduleByStudies'), 
                           Array($entry->getAlid(), 
                                 $entry->getStid(), 
                                 $entry->getSemester(), 
                                 $entry->getSemYear()));
  }

  protected function getCntRealFromImportByStudies (ModelContext $context, ScheduleEntry $entry) {
    return self::getArrayFromDb($context, 
                           $entry, 
                           $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getCntRealFromImportByStudies'), 
                           Array($entry->getAlid(), 
                                 $entry->getStid(), 
                                 $entry->getSemester(), 
                                 $entry->getSemYear()));
  }

  
  
  
/* #################################################################################################################################################
 * NOTENVERTEILUNG (Studienschwerpunkt)
 */


 /**
	 * Liefert die Notenverteilung einer Veranstaltung (Studienschwerpunkt)
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param ScheduleEntry $entry ScheduleEntry
	 * @return Array mit Notenverteilung [mark_real] [cnt_mark]
	 * 	 */
	public static function getCntRealByMajor (ModelContext $context, ScheduleEntry $entry) {

	  $ret=-1;
	  if ($context->getConf()->getConfString('ucImporter', 'useimportedmarks') == 'true') {
      $ret = self::getCntRealFromImportByMajor($context, $entry);
	  } else {
	    $ret = self::getCntRealFromScheduleByMajor($context, $entry);
	  }
	  return $ret;
  }
  
  protected function getCntRealFromScheduleByMajor (ModelContext $context, ScheduleEntry $entry) {
    return self::getArrayFromDb($context, 
                                $entry, 
                                $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getCntRealFromScheduleByMajor'), 
                                Array($entry->getModId(), 
                                      $entry->getSemester(), 
                                      $entry->getSemYear()));
  }
  
  protected function getCntRealFromImportByMajor (ModelContext $context, ScheduleEntry $entry) {
    return self::getArrayFromDb($context, 
                                $entry, 
                                $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getCntRealFromImportByMajor'), 
                                Array($entry->getAlid(), 
                                      $entry->getMajId(), 
                                      $entry->getSemester(), 
                                      $entry->getSemYear()));
  }
  

  /* #################################################################################################################################################
 * ECHTE NOTEN, Durchschnitt (gesamt)
 */


  /**
	 * Liefert die durchschnittliche Note einer Veranstaltung
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param ScheduleEntry $entry ScheduleEntry
	 * @return durchschnittliche Plannote
	 */
	public static function getAvgRealByLecture (ModelContext $context, ScheduleEntry $entry) {

	  $ret=-1;
 	  if ($context->getConf()->getConfString('ucImporter', 'useimportedmarks') == 'true') {
	    $ret = self::getAvgRealFromImportByLecture($context, $entry);
	  } else {
	    $ret = self::getAvgRealFromScheduleByLecture($context, $entry);
	  }
	  return $ret;
  }
  
  protected function getAvgRealFromScheduleByLecture (ModelContext $context, ScheduleEntry $entry) {
    return self::getFromDb($context, 
                           $entry, 
                           $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getAvgRealFromScheduleByLecture'), 
                           Array($entry->getAlid(), 
                                 $entry->getSemester(), 
                                 $entry->getSemYear()));
  }
  
  protected function getAvgRealFromImportByLecture (ModelContext $context, ScheduleEntry $entry) {
    return self::getFromDb($context, 
                           $entry, 
                           $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getAvgRealFromImportByLecture'), 
                           Array($entry->getAlid(), 
                                 $entry->getSemester(), 
                                 $entry->getSemYear()));
  }
  
  
  
/* #################################################################################################################################################
 * ECHTE NOTEN, Durchschnitt (Studiengang)
 */
  
  
  /**
	 * Liefert die durchschnittliche Note einer Veranstaltung bezogen auf den Studiengang
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param ScheduleEntry $entry ScheduleEntry
	 * @return durchschnittliche Plannote
	 */
	public static function getAvgRealByStudies (ModelContext $context, ScheduleEntry $entry) {

	  $ret=-1;
	  if ($context->getConf()->getConfString('ucImporter', 'useimportedmarks') == 'true') {
	     $ret = self::getAvgRealFromImportByStudies($context, $entry);
	  } else {
	    $ret = self::getAvgRealFromScheduleByStudies($context, $entry);
	  }
	  return $ret;
  }
  
  protected function getAvgRealFromScheduleByStudies (ModelContext $context, ScheduleEntry $entry) {
    return self::getFromDb($context, 
                           $entry, 
                           $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getAvgRealFromScheduleByStudies'), 
                           Array($entry->getAlid(), 
                                 $entry->getStid(), 
                                 $entry->getSemester(), 
                                 $entry->getSemYear()));
  }

  protected function getAvgRealFromImportByStudies (ModelContext $context, ScheduleEntry $entry) {
    return self::getFromDb($context, 
                           $entry, 
                           $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getAvgRealFromImportByStudies'), 
                           Array($entry->getAlid(), 
                                 $entry->getStid(), 
                                 $entry->getSemester(), 
                                 $entry->getSemYear()));
  }

  
/* #################################################################################################################################################
 * ECHTE NOTEN, Durchschnitt (Schwerpunkt)
 */


 /**
	 * Liefert die durchschnittliche Note einer Veranstaltung
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param ScheduleEntry $entry ScheduleEntry
	 * @return durchschnittliche Plannote
	 */
	public static function getAvgRealByMajor (ModelContext $context, ScheduleEntry $entry) {

	  $ret=-1;
	  if ($context->getConf()->getConfString('ucImporter', 'useimportedmarks') == 'true') {
      $ret = self::getAvgRealFromImportByMajor($context, $entry);
	  } else {
	    $ret = self::getAvgRealFromScheduleByMajor($context, $entry);
	  }
	  return $ret;
  }
  
  protected function getAvgRealFromScheduleByMajor (ModelContext $context, ScheduleEntry $entry) {
    return self::getFromDb($context, 
                           $entry, 
                           $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getAvgRealFromScheduleByMajor'), 
                           Array($entry->getModId(), 
                                 $entry->getSemester(), 
                                 $entry->getSemYear()));
  }
  
  protected function getAvgRealFromImportByMajor (ModelContext $context, ScheduleEntry $entry) {
    return self::getFromDb($context, 
                           $entry, 
                           $context->getConf()->getConfString('sql', 'scheduleentrystatistics', 'getAvgRealFromImportByMajor'), 
                           Array($entry->getAlid(), 
                                 $entry->getMajId(), 
                                 $entry->getSemester(), 
                                 $entry->getSemYear()));
  }
  
}
?>
