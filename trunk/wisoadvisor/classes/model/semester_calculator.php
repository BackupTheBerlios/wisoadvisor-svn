<?php
/***********************************************************************************
 * WiSo@visor v2 - Studienmanagementsystem (Pruefungsplan)
 * (c) 2007 Lehrstuhl fuer Wirtschaftsinformatik 3, Uni Erlangen-Nuernberg
 *
 * Datei: semester_calculator.php
 * $Revision: 1.6 $
 * Erstellt am: 03.09.2007
 * Erstellt von: Florian Mattes
 ***********************************************************************************/

/** 
 * Die Klasse SemesterCalculator konvertiert und berechnet Semesterangaben
 * 
 * @author Florian MAttes
 */ 
class SemesterCalculator {
	
  private $sem_word = null;
  private $sem_year = null;
  
  public function __construct() {
    
    $this->sem_year = date('Y');
    $curMonth = date('m');
    if (($curMonth >= 4) && ($curMonth <= 9)) {
      $this->sem_word = 'ss';
    } else {
      $this->sem_word = 'ws';
    }
    
  }
  
	public function addSemester($numSemesters, $ignoreFirstSemester = true) {
	  
	  if ($ignoreFirstSemester) {
  	  $numSemesters--;
	  }
	  
	  if ($numSemesters > 0) {
      $this->sem_year += ($numSemesters/2);
	    if ($numSemesters % 2 != 0) {
        $this->sem_word = ($this->sem_word == 'ss' ? 'ws' : 'ss');
	    }
	  }
	}
	
  public function getSemesterWord() {
    return $this->sem_word;
  }
  
  public function getSemesterYear() {
    return $this->sem_year;
  }
  
  public function getSemesterReadable() {
	  return $this->getSemesterReadableStatic($this->sem_word, $this->sem_year);
	}
	
	public static function getSemesterReadableStatic($iSemWord, $iSemYear) {
    $ret = '';    
	  if ($iSemWord == 'ss') {
      // $ret = strtoupper($iSemWord).' '.$iSemYear; 
      $ret = 'Sommersemester '.$iSemYear;
	  } else {
	    $nextSem= $iSemYear+1;
	    $shortSem = substr($nextSem, strlen(rtrim($nextSem))-2, 2);
	    //$ret = strtoupper($iSemWord).' '.$iSemYear.'/'.$shortSem; 
	    $ret = 'Wintersemester '.$iSemYear.'/'.$shortSem; 
	  }
	  return $ret;
	}
	
  
}
?>
