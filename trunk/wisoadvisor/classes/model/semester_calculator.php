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
  
	public function addSemester($numSemesters) {
	  
    // bloody hack, sorry
    if ($numSemesters > 0) {
	    for ($i=1;$i<=$numSemesters;$i++) {
        if ($this->sem_word == 'ws') {
          $this->sem_year++;
          $this->sem_word = 'ss';
        } else {
          $this->sem_word = 'ws';
        }
      }
    } else if ($numSemesters < 0) {
	    for ($i=-1;$i>=$numSemesters;$i--) {
        if ($this->sem_word == 'ss') {
          $this->sem_year--;
          $this->sem_word = 'ws';
        } else {
          $this->sem_word = 'ss';
        }
      }
    }
	  
	}
	
  public function setBoth ($iBoth) {
    $this->sem_word = substr($iBoth, 0, 2);
    $this->sem_year = substr($iBoth, 2);    
  }
  
  public function getBoth () {
    return $this->sem_word . $this->sem_year;  
  }
  
  public function setSemesterWord($semword) {
    $this->sem_word = $semword;
  }

  public function getSemesterWord() {
    return $this->sem_word;
  }
  
  public function getSemesterWordReadable() {
    $ret = 'Wintersemester';
    if ($this->sem_word == 'ss') {
      $ret = 'Sommersemester';
    }
    return $ret;
  }
  
  public function setSemesterYear($semyear) {
    $this->sem_year = $semyear;
  }
  public function getSemesterYear() {
    return $this->sem_year;
  }
  
  public function getSemesterYearReadable() {
	  $ret='';
    if ($this->sem_word == 'ss') {
      $ret = $this->sem_year;
	  } else {
	    $nextSem= $this->sem_year+1;
	    $shortSem = substr($nextSem, strlen(rtrim($nextSem))-2, 2);
	    //$ret = strtoupper($iSemWord).' '.$iSemYear.'/'.$shortSem; 
	    $ret = $this->sem_year.'/'.$shortSem; 
	  }
    return $ret;
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
	
  public function compare($iSemesterCalculator) {

    $ret = 1;
    
    if ($iSemesterCalculator->getBoth() == $this->getBoth()) {
      $ret = 0;
    } else {
      if ($this->sem_year < $iSemesterCalculator->getSemesterYear()) {
        $ret = -1;
      } else if ($this->sem_year == $iSemesterCalculator->getSemesterYear()) {
        if (($this->sem_word == 'ss') && ($iSemesterCalculator->getSemesterWord() == 'ws')) {
          $ret = -1;
        }
      }
    }
    
    return $ret;
  }
  
  public function getPrevSemesterCalculator() {
    $ret = new SemesterCalculator();
    $ret->setBoth($this->getBoth());
    $ret->addSemester(-1);
    return $ret;
  }
  
  public function getNextSemesterCalculator() {
    $ret = new SemesterCalculator();
    $ret->setBoth($this->getBoth());
    $ret->addSemester(1);
    return $ret;    
  }
}
?>
