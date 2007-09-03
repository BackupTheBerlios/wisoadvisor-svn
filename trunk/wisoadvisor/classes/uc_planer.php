<?php
class ucPlaner extends UseCase {

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
    
    $myScheduleEntry->setUserId($user->getId());
    $myScheduleEntry->setModId($myModule->getModId());
    $myScheduleEntry->setMarkPlanned(0);
    $hCalc->addSemester($myModule->getSemesterDefault(), true);
    $myScheduleEntry->setSemester($hCalc->getSemesterWord());
    $myScheduleEntry->setSemYear($hCalc->getSemesterYear());
    
    $myScheduleEntry->storeInDb($this);
    
	}

}

private function showPlan() {
  
	$user = User::getForId($this, $this->getSess()->getUid());
  
  // header of whole site
	$generator = new HtmlGenerator( $this->getConf()->getConfString('ucPlaner', 'htmltemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$generator->apply($this->getConf()->getConfString('ucPlaner', 'username'), $user->getUserName());
	$generator->apply($this->getConf()->getConfString('ucPlaner', 'studies'), $user->getStudies());
	$this->appendOutput($generator->getHTML());
	
	// and now for the schedule itself...
	$scheduleEntries = ScheduleEntry::getForUser($this, $user->getId(), $user->getMajId());

	// no schedule found => show link to create a new one
	if (empty($scheduleEntries)) {
	  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'linkcreatetemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		$gen->apply($this->getConf()->getConfString('ucPlaner', 'linkcreate'), $this->getUsecaseLink('planer', 'create'));
	  $this->appendOutput($gen->getHTML());
	  
	// print the found schedule
	} else {

    // entries in schedule
	  $oldSemester = '';
	  $firstrun = true;
    foreach ($scheduleEntries as $myentry) {
	    
	    $curSemester = SemesterCalculator::getSemesterReadableStatic($myentry->getSemester(), $myentry->getSemYear());
	    if ($curSemester != $oldSemester) {
	      $oldSemester = $curSemester;
	      if ($firstrun) {
	        $firstrun=false;
	      } else {
	        $this->printEntryFooter();
	      }
	      // schedule header
        $this->printEntryHeader($curSemester);         
	    }
	    
	    // hier unterscheiden, welches template benoetigt wird (normales oder vergangenes Semester)
      // normales semester: links fuer hoch und runter
      // vergangenes semester: keine links!
      $hEntryTemplate = $this->getConf()->getConfString('ucPlaner', 'entrytemplate');
      
	    $gen = new HtmlGenerator($hEntryTemplate, $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	    $gen->apply($this->getConf()->getConfString('ucPlaner', 'mod_name'), $myentry->getModName());
	    $gen->apply($this->getConf()->getConfString('ucPlaner', 'ects'), $myentry->getEcts());	    
	    $this->appendOutput($gen->getHTML());
	
		}
		// schedule footer
    $this->printEntryFooter();

  }	
  $this->setOutputType(USECASE_HTML);
  return true;
}

private function printEntryHeader($curSemester) {
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'entryheadtemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'semester_readable'), $curSemester);
  $this->appendOutput($gen->getHTML());
}

private function printEntryFooter() {
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'entryfoottemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  $this->appendOutput($gen->getHTML());  
}

} // class
?>