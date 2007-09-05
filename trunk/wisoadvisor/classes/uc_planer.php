<?php
class ucPlaner extends UseCase {

	const PARAMETER_SCHID = 'schid';
	const PARAMETER_DIR = 'dir';
	
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
	
private function createNewScheduleInDb() {
  
	$user = User::getForId($this, $this->getSess()->getUid());
  
	// bisherigen Plan loeschen, ohne Ruecksicht auf Verluste :-)
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
    $myScheduleEntry->setMarkPlanned(0);
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
    
    // niy: parameter fuer vl-zyklus beruecksichtigen
    $semToAdd = ($dir=='down' ? 1 : -1);
    $hCurrentSemester->addSemester($semToAdd, false);
    
    $entry->setSemester($hCurrentSemester->getSemesterWord());
    $entry->setSemYear($hCurrentSemester->getSemesterYear());
    
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
	  
	// found schedule => create html and store it temporarily
	} else {

    // entries in schedule
	  $oldSemester = '';
	  $firstrun = true;
    foreach ($scheduleEntries as $myentry) {
	    
	    $curRealSemCalc = new SemesterCalculator();
      
	    $curEntrySemCalc = new SemesterCalculator();
	    $curEntrySemCalc->setSemesterWord($myentry->getSemester());
	    $curEntrySemCalc->setSemesterYear($myentry->getSemYear());
      
	    $curSemester = $curEntrySemCalc->getSemesterReadable();
	    if ($curSemester != $oldSemester) {
	      $oldSemester = $curSemester;
	      if ($firstrun) {
	        $firstrun=false; // no footer before the first semester
	      } else {
	        $htmlToAppend .= $this->printEntryFooter();
	        $duration++; 
	      }
	      // schedule header
        $htmlToAppend .= $this->printEntryHeader($curSemester);         
	    }
	    
	    // hier unterscheiden, welches template benoetigt wird (normales oder vergangenes Semester)
      // normales semester: links fuer hoch und runter
      // vergangenes semester: keine links!
      $hEntryTemplate = '';
      $hSemStartCalc = new SemesterCalculator();
      $hSemStartCalc->setBoth($user->getSemStart());

      if ($curRealSemCalc->compare($curEntrySemCalc) == 0) {
        $hEntryTemplate = $this->getConf()->getConfString('ucPlaner', 'entrylockeduptemplate'); // aktuelles semester => nur oben schieben verboten
      } else if ($curRealSemCalc->compare($curEntrySemCalc) == -1) {
        if ($curEntrySemCalc->compare($hSemStartCalc) <= 0) {
          $hEntryTemplate = $this->getConf()->getConfString('ucPlaner', 'entrylockeduptemplate'); // vor dem startsemester => nur oben schieben verboten
        } else {
          $hEntryTemplate = $this->getConf()->getConfString('ucPlaner', 'entrytemplate');  // kuenftiges semester => alles erlaubt
        }
      } else {
        $hEntryTemplate = $this->getConf()->getConfString('ucPlaner', 'entrylockedalltemplate'); // vergangenes semester => alles verboten
      }
      
	    $gen = new HtmlGenerator($hEntryTemplate, $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	    $gen->apply($this->getConf()->getConfString('ucPlaner', 'mod_name'), $myentry->getModName());
	    $gen->apply($this->getConf()->getConfString('ucPlaner', 'ects'), $myentry->getEcts());
	    $gen->apply($this->getConf()->getConfString('ucPlaner', 'moveup'), $this->getOwnLink('move', 
	                                                                                         Array(ucPlaner::PARAMETER_SCHID.'='.$myentry->getId(), 
	                                                                                               ucPlaner::PARAMETER_DIR.'=up')));
	    $gen->apply($this->getConf()->getConfString('ucPlaner', 'movedown'), $this->getOwnLink('move', 
	                                                                                           Array(ucPlaner::PARAMETER_SCHID.'='.$myentry->getId(), 
	                                                                                                 ucPlaner::PARAMETER_DIR.'=down')));
	                                                                                                    	    
	    $htmlToAppend .= $gen->getHTML();
	
		}
		
		// schedule footer
    $htmlToAppend .= $this->printEntryFooter();
    
    // oops, we almost forgot the prognose :-)
    // no, let's be serious: we only can add the prognose at the end of the schedule, for only now we finally know how long it takes
    // call it a hack, if you want to...
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
	$gen->apply($this->getConf()->getConfString('ucPlaner', 'linkcreate'), $this->getUsecaseLink('planer', 'create'));
  return $gen->getHTML();  
}

private function printEntryHeader($curSemester) {
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'entryheadtemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'semester_readable'), $curSemester);
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

private function printEntryFooter() {
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'entryfoottemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  return $gen->getHTML();  
}

} // class
?>