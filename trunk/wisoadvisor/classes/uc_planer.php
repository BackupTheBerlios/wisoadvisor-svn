<?php
class ucPlaner extends UseCase {

	const PARAMETER_SCHID = 'schid';
	const PARAMETER_DIR = 'dir';
	const PARAMETER_CUR = 'cur';
	
//Ausführung: Business-Logik
public function execute()	{

	$ret = false;
	
  if (!$this->getSess()->isAuthenticated()) {
		header('location:'.$this->getMainLink());
		$this->setOutputType(USECASE_NOTYPE);
		$ret = true;
	
	} else {

	  $this->setOutputType(USECASE_HTML);

    switch ($this->getStep()) {
			
			case 'create':
				// first, create a new plan; then display this one
				$this->createNewScheduleInDb();
        $this->showPlan();
        break;
        
			case 'plan':
			  $this->savePlannedMarks();
			  $this->showPlan();
			  break;
			  
      case 'move':
				$schid = $this->getParam()->getParameter(ucPlaner::PARAMETER_SCHID);
				$dir = $this->getParam()->getParameter(ucPlaner::PARAMETER_DIR);
				if ($schid) {
				  if ($dir) {
    				$this->move($schid, $dir);
				  } else {
            throw new MissingParameterException('Der Parameter '.ucPlaner::PARAMETER_DIR.' ist ungültig ('.$dir.').');
				  }
				} else {
				  throw new MissingParameterException('Der Parameter '.ucPlaner::PARAMETER_SCHID.' ist ungültig ('.$schid.').');
				}
				$this->showPlan(); 
			  break; 
			  
			default:
        $this->showPlan();
		}
		
    $ret = true;
	}
	return $ret;
}

/*
 * Die Plannoten werden als _POST-Argumente uebergeben:
 * Der Key eines Array-Elements lautet stets: schid_4711, wobei "schid" fest ist, und 4711 die ID des ScheduleEntries
 * Der Value des Elements ist dann die Plannote
 * Beispiel: paramArray['schid_4711'] = 1.0
 * Alles klar?
*/
private function savePlannedMarks() {

  $paramArray = $this->getParam()->getAllPostParameters();
  
  reset($paramArray);
  for($x=0;$x<sizeof($paramArray);$x++) {     

    $key = key($paramArray);
    $val = floatval(str_replace(',', '.', current($paramArray)));
    
    if (substr($key, 0, 5) == "schid") { // wenn "schid" davor steht, ...
      $schid = substr($key, 6); // ... schneide die advisor__schedule.schid raus, ...
      if (($val >= 1.0) && ($val <= 5.0)) { // ... und wenn die eingegebene note passt, ...
        $myentry = ScheduleEntry::getForId($this, $schid);
        $myentry->setMarkPlanned($val);
        $myentry->storeInDb($this);  // ... schreib das glump in die datenbank
      }
    }
    next($paramArray); // und so weiter.
  }  
    
}

private function createNewScheduleInDb() {
  
	$user = User::getForId($this, $this->getSess()->getUid());
  
	// bisherigen Plan loeschen, ohne Ruecksicht auf Verluste :-)
  // TODO: Abgleich mit Studiengang, zwecks gleicher, bereits eingeplanter Module !!!
  ScheduleEntry::deleteForUser($this, $user->getId());
  
	// alle modules für majid holen
	$modules = Module::getForMajor($this, $user->getMajId());
	
	// für jede module einen eintrag in schedule erstellen; semester ausgehend vom startsemester berechnen !!!
	foreach ($modules as $myModule) {
	  
	  $myScheduleEntry = ScheduleEntry::getNew($this);
    $hCalc = new SemesterCalculator();
    $hCalc->setBoth($user->getSemStart());
    
    $myScheduleEntry->setUserId($user->getId());
    $myScheduleEntry->setModId($myModule->getModId());
    $hCalc->addSemester($myModule->getSemesterDefault(), true);
    $myScheduleEntry->setSemester($hCalc->getSemesterWord());
    $myScheduleEntry->setSemYear($hCalc->getSemesterYear());
    
    $myScheduleEntry->storeInDb($this);
    
	}

}

private function move($schid, $dir) {

  $entry = ScheduleEntry::getForId($this, $schid);
  
  if ($entry) {
    
    $hCurrentSemester = new SemesterCalculator();
    $hCurrentSemester->setSemesterWord($entry->getSemester());
    $hCurrentSemester->setSemesterYear($entry->getSemYear());
    
    // debugging code
    //$str = $entry->getId() . '...' . $hCurrentSemester->getBoth() .'...' . $dir . '...';
    
    $semToAdd = ($dir=='down' ? 1 : -1);
    $hCurrentSemester->addSemester($semToAdd, false);
    
    $entry->setSemester($hCurrentSemester->getSemesterWord());
    $entry->setSemYear($hCurrentSemester->getSemesterYear());
    
    // debugging code
    //$str .= $hCurrentSemester->getBoth() .'...';
    //$this->appendOutput($str);
    $entry->storeInDb($this);
    
  }
  
}

private function showPlan() {
  
	$curSemester = '';
	$duration = 1;
  $htmlToAppend = '';
	
	$user = User::getForId($this, $this->getSess()->getUid());
  
	// everything concerning the schedule is put into $this->htmlToAppend; this will be appended to the page later on
	$scheduleEntries = ScheduleEntry::getForUser($this, $user->getId(), $user->getMajId());

	// no schedule found => show link to create a new one
	if (empty($scheduleEntries)) {
	  $htmlToAppend .= $this->printEmptySchedule();
	  
	// found schedule => create html and store it in $htmlToAppend
	} else {

	  $sum_ects = 0; // pro semester
    $oldSemester = '';
	  $firstrun = true; // beim ersten lauf keinen footer anzeigen
	  
    foreach ($scheduleEntries as $myentry) {
	    
	    $curRealSemCalc = new SemesterCalculator(); // tatsaechliches aktuelles semester      
	    $curEntrySemCalc = new SemesterCalculator(); // semester, fuer das der eintrag eingeplant wurde
	    $curEntrySemCalc->setSemesterWord($myentry->getSemester());
	    $curEntrySemCalc->setSemesterYear($myentry->getSemYear());
      
	    // wenn altes semester != semester des eintrags, dann neue tabelle beginnen
	    $curSemester = $curEntrySemCalc->getSemesterReadable();
	    if ($curSemester != $oldSemester) {
	      $oldSemester = $curSemester;
	      if ($firstrun) {
	        $firstrun=false; // no footer before the first semester
	      } else {
	        $htmlToAppend .= $this->printEntryFooter($sum_ects);
	        $sum_ects=0;
	        $duration++; 
	      }
	      // schedule header
        $htmlToAppend .= $this->printEntryHeader($curEntrySemCalc);         
	    }
	    
      $hEntryTemplate = '';
      $hSemStartCalc = new SemesterCalculator();
      $hSemStartCalc->setBoth($user->getSemStart());

	    // unterschiedliche semester brauchen unterschiedliche templates bzgl. der aktionen
      if ($curRealSemCalc->compare($curEntrySemCalc) == 0) {
        $hEntryTemplate = $this->getConf()->getConfString('ucPlaner', 'entrylockeduptemplate'); // aktuelles semester => nur oben schieben verboten
      } else if ($curRealSemCalc->compare($curEntrySemCalc) == -1) {
        if ($curEntrySemCalc->compare($hSemStartCalc) <= 0) {
          $hEntryTemplate = $this->getConf()->getConfString('ucPlaner', 'entrylockeduptemplate'); // akt. semester ist vor dem startsemester => nur oben schieben verboten
        } else {
          $hEntryTemplate = $this->getConf()->getConfString('ucPlaner', 'entrytemplate');  // kuenftiges semester => alles erlaubt
        }
      } else {
        $hEntryTemplate = $this->getConf()->getConfString('ucPlaner', 'entrylockedalltemplate'); // vergangenes semester => alles verboten
      }
      
	    // template ausfuellen
      $gen = new HtmlGenerator($hEntryTemplate, $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	    $gen->apply($this->getConf()->getConfString('ucPlaner', 'schid'), $myentry->getId());
	    $gen->apply($this->getConf()->getConfString('ucPlaner', 'mark_plan'), ($myentry->getMarkPlanned() > 0 ? sprintf("%1.1f", $myentry->getMarkPlanned()) : ''));
	    $gen->apply($this->getConf()->getConfString('ucPlaner', 'mod_name'), $myentry->getModName());
	    $gen->apply($this->getConf()->getConfString('ucPlaner', 'ects'), $myentry->getEcts());
	    $gen->apply($this->getConf()->getConfString('ucPlaner', 'moveup'), $this->getOwnLink('move', Array(ucPlaner::PARAMETER_SCHID.'='.$myentry->getId(), ucPlaner::PARAMETER_DIR.'=up', ucPlaner::PARAMETER_CUR.'='.$curEntrySemCalc->getBoth(), '#'.$curEntrySemCalc->getBoth())));
	    $gen->apply($this->getConf()->getConfString('ucPlaner', 'movedown'), $this->getOwnLink('move', Array(ucPlaner::PARAMETER_SCHID.'='.$myentry->getId(), ucPlaner::PARAMETER_DIR.'=down', ucPlaner::PARAMETER_CUR.'='.$curEntrySemCalc->getBoth(), '#'.$curEntrySemCalc->getBoth())));

	    $sum_ects += $myentry->getEcts();                                                                                                	    
	    $htmlToAppend .= $gen->getHTML();
		
    } // foreach $myentry
		
		// semester footer
    $htmlToAppend .= $this->printEntryFooter($sum_ects);
    
    // prognose wird live berechnet und ist daher erst am ende bekannt
    $htmlToAppend = $this->printPrognose($user, $curSemester, $duration) . $htmlToAppend;
    
  }
  
  // finally, build the page
	$gen = new HtmlGenerator( $this->getConf()->getConfString('ucPlaner', 'htmltemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$gen->apply($this->getConf()->getConfString('ucPlaner', 'username'), $user->getUserName());
	$gen->apply($this->getConf()->getConfString('ucPlaner', 'studies'), $user->getStudies());

	$this->appendOutput($gen->getHTML()); 
  $this->appendOutput($htmlToAppend);
  $this->setOutputType(USECASE_HTML);
  return true;
  
}

private function printEmptySchedule() {
	$gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'linkcreatetemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$gen->apply($this->getConf()->getConfString('ucPlaner', 'linkcreate'), $this->getOwnLink('create'));
  return $gen->getHTML();  
}

private function printEntryHeader($curSemester) {
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'entryheadtemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$gen->apply($this->getConf()->getConfString('ucPlaner', 'linkplan'), $this->getOwnLink('plan', Array('#'.$curSemester->getBoth())));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'semester_short'), $curSemester->getBoth());
	$gen->apply($this->getConf()->getConfString('ucPlaner', 'semester_readable'), $curSemester->getSemesterReadable());
  return $gen->getHTML();
}

private function printPrognose($user, $curSemester, $duration) {
  
  $hFirstSemester = new SemesterCalculator();
  $hFirstSemester->setBoth($user->getSemStart());
  
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'prognosetemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'firstsemester'), $hFirstSemester->getSemesterReadable());
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'lastsemester'), $curSemester);
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'duration'), $duration);
  
  return $gen->getHTML();
}

private function printEntryFooter($sum_ects) {
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'entryfoottemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'sum_ects'), $sum_ects);
  return $gen->getHTML();  
}

} // class
?>