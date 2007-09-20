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
				if (($schid) && ($dir)) {
   				$this->move($schid, $dir);
				} else {
				  throw new MissingParameterException('Ein  Parameter ist ungültig.');
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
 * Der Key eines Array-Elements lautet stets: plan_4711 (oder real_4711), wobei "plan_" fest ist, und 4711 die ID des ScheduleEntries
 * Der Value des Elements ist dann die geplante bzw. reale Note
 * Beispiel: paramArray['plan_4711'] = 1.0
 * Alles klar?
*/
private function savePlannedMarks() {

  $paramArray = $this->getParam()->getAllPostParameters();
  
  reset($paramArray);
  for($x=0;$x<sizeof($paramArray);$x++) {     

    $key = key($paramArray);
    $val = floatval(str_replace(',', '.', current($paramArray)));
    
    if (substr($key, 0, 4) == "plan") { // wenn "plan" davor steht, ...
      $schid = substr($key, 5); // ... schneide die advisor__schedule.schid raus, ...
      if (($val >= 1.0) && ($val <= 5.0)) { // ... und wenn die eingegebene note passt, ...
        $myentry = ScheduleEntry::getForId($this, $schid);
        $myentry->setMarkPlanned($val);
        $myentry->storeInDb($this);  // ... schreib das glump in die datenbank
      }
    } else if (substr($key, 0, 4) == "real") { // dasselbe spielchen, wenn "real" davor steht, ...
      if ($this->getConf()->getConfString('ucImporter', 'useimportedmarks') == 'false' || $user->getType() == 'admin') {
	      $schid = substr($key, 5); // ... schneide wieder die advisor__schedule.schid raus, ...
	      if (($val >= 0.0) && ($val <= 5.0)) { // ... und wenn die eingegebene note passt, ...
	        $myentry = ScheduleEntry::getForId($this, $schid);
	        $myentry->setMarkReal($val);
	        $myentry->storeInDb($this);  // ... ab damit in die datenbank
	      }
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
	
	// für jede module einen eintrag in schedule erstellen; semester ausgehend vom startsemester berechnen
	foreach ($modules as $myModule) {
	  
	  $myScheduleEntry = ScheduleEntry::getNew($this);
    $hCalc = new SemesterCalculator();
    $hCalc->setBoth($user->getSemStart());
    
    $myScheduleEntry->setUserId($user->getId());
    $myScheduleEntry->setModId($myModule->getId());
    $hCalc->addSemester($myModule->getSemesterDefault()-1);
    $myScheduleEntry->setSemester($hCalc->getSemesterWord());
    $myScheduleEntry->setSemYear($hCalc->getSemesterYear());
    $myScheduleEntry->setTry(1);
    $myScheduleEntry->setAlid($myModule->getAlId());
    $myScheduleEntry->setStid($myModule->getStId());
    
    $myScheduleEntry->storeInDb($this);
    
	}

}

private function move($schid, $dir) {

  $user = User::getForId($this, $this->getSess()->getUid());
  $entry = ScheduleEntry::getForId($this, $schid);
  
  if ($entry) {
    
    $hNewSemester = new SemesterCalculator();
    $hNewSemester->setSemesterWord($entry->getSemester());
    $hNewSemester->setSemesterYear($entry->getSemYear());
    
    $semToAdd = ($dir=='up' ? -1 : 1);
    $hNewSemester->addSemester($semToAdd);
    
    if (($entry->isMoveableUpwards($user) && ($dir=='up')) ||
        ($entry->isMoveableDownwards($user) && ($dir=='down'))) {
	    $entry->setSemester($hNewSemester->getSemesterWord());
	    $entry->setSemYear($hNewSemester->getSemesterYear());    
      $entry->storeInDb($this);
    }
  }
}

private function showPlan() {
  
	$duration = 0; // anzahl fachsemester
  $htmlForSchedule = ''; // alles generierte html des eigentlichen planes
  
	$user = User::getForId($this, $this->getSess()->getUid());
	$scheduleEntries = ScheduleEntry::getForUser($this, $user->getId(), $user->getMajId());

	// no schedule found => show link to create a new one
	if (empty($scheduleEntries)) {
	  $htmlForSchedule .= $this->printEmptySchedule($user);
	  
	// found schedule => create html and store it in $htmlForSchedule
	} else {

	  $sum_ects = 0; // pro semester
	  $firstrun = true; // beim ersten lauf keinen footer anzeigen
    $oldEntrySemCalculator = new SemesterCalculator(); // semester, fuer das der vorherige eintrag eingeplant wurde (aus advisor__schedule)
	  $curEntrySemCalculator = new SemesterCalculator(); // semester, fuer das der aktuelle eintrag eingeplant wurde (aus advisor__schedule)

	  $oldEntrySemCalculator->setBoth('ss1980');
	  
	  foreach ($scheduleEntries as $myentry) {
	    
	    $curEntrySemCalculator->setSemesterWord($myentry->getSemester());
	    $curEntrySemCalculator->setSemesterYear($myentry->getSemYear());
      
	    // wenn semester des letzten schleifendurchlaufs != semester des aktuellen eintrags, dann neue tabelle beginnen
	    if ($curEntrySemCalculator->compare($oldEntrySemCalculator) != 0) {
	      $oldEntrySemCalculator->setBoth($curEntrySemCalculator->getBoth());
	      if ($firstrun) {
	        $firstrun=false; // no footer before the first semester
	      } else {
	        $htmlForSchedule .= $this->printEntryFooter($sum_ects); // footer for single semester
	      }
        $sum_ects=0;
        $duration++; 
	      $htmlForSchedule .= $this->printEntryHeader($curEntrySemCalculator); // schedule header
	    }
	    
	    $sum_ects += $myentry->getEcts();                                                                                                	    
	    $htmlForSchedule .= $this->printEntry($user, $myentry); // entry
		
    } // foreach $myentry
		
    $htmlForSchedule .= $this->printEntryFooter($sum_ects); // final semester footer
    $htmlForSchedule = $this->printPrognose($user, $curEntrySemCalculator, $duration) . $htmlForSchedule; // add prognose before
    
  }
  
  // finally, build the page
	$this->appendOutput($this->printScheduleHeader($user));
  $this->appendOutput($htmlForSchedule);
  $this->appendOutput($this->printScheduleFooter($user));
  $this->setOutputType(USECASE_HTML);
  return true;
  
}

private function printScheduleHeader(User $user) {
	$gen = new HtmlGenerator( $this->getConf()->getConfString('ucPlaner', 'htmltemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$gen->apply($this->getConf()->getConfString('ucPlaner', 'username'), $user->getUserName());
	$gen->apply($this->getConf()->getConfString('ucPlaner', 'studies'), $user->getStudies());
  return $gen->getHTML();
}

private function printEntry(User $user, ScheduleEntry $myentry) {
  
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'entrytemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  
  //$avgMarkPlan = ScheduleEntryStatistics::getAvgPlanByLecture($this, $myentry);
  //$avgMarkReal = ScheduleEntryStatistics::getAvgRealByLecture($this, $myentry);
  
  // links und form zzgl. standards ins template parsen auffuellen
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'mod_name'), $myentry->getModName().($myentry->isAssessment()=='true'?'*':''));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'ects'), $myentry->getEcts());
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'try'), $myentry->getTry());
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'action_movedown'), $this->getEntryActionDown($user, $myentry));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'action_moveup'), $this->getEntryActionUp($user, $myentry));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'mark_plan'), $this->getEntryFormForPlan($user, $myentry));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'mark_real'), $this->getEntryFormForReal($user, $myentry));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'mark_plan_avg'), ($avgMarkPlan > 0 ? sprintf("%1.1f", $avgMarkPlan) : ''));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'mark_real_avg'), ($avgMarkReal > 0 ? sprintf("%1.1f", $avgMarkReal) : ''));
    return $gen->getHTML();
  
}

private function getEntryActionDown(User $user, ScheduleEntry $myentry) {
  
  $entrySemCalc = new SemesterCalculator(); // semester, fuer das der eintrag eingeplant wurde (siehe advisor__schedule)
	$entrySemCalc->setSemesterWord($myentry->getSemester());
	$entrySemCalc->setSemesterYear($myentry->getSemYear());

  $ret = '';  
  if ($myentry->isMoveableDownwards($user)) {
	  $ret = '<a href="' . $this->getOwnLink('move', Array(ucPlaner::PARAMETER_SCHID.'='.$myentry->getId(), 
	                                                       ucPlaner::PARAMETER_DIR.'=down', 
	                                                       ucPlaner::PARAMETER_CUR.'='.$entrySemCalc->getBoth(), 
	                                                       '#'.$entrySemCalc->getNextSemesterCalculator()->getBoth()))
	                     . '"><img alt="Um ein Semester schieben" title="Um ein Semester schieben" src="grafik/nach_unten.gif"/></a>';
  }
  return $ret;
}

private function getEntryActionUp(User $user, ScheduleEntry $myentry) {

  $entrySemCalc = new SemesterCalculator(); // semester, fuer das der eintrag eingeplant wurde (siehe advisor__schedule)
	$entrySemCalc->setSemesterWord($myentry->getSemester());
	$entrySemCalc->setSemesterYear($myentry->getSemYear());

  $ret = '';  
  if ($myentry->isMoveableUpwards($user)) {
    $ret = '<a href="' . $this->getOwnLink('move', Array(ucPlaner::PARAMETER_SCHID.'='.$myentry->getId(), 
                                                         ucPlaner::PARAMETER_DIR.'=up', 
                                                         ucPlaner::PARAMETER_CUR.'='.$entrySemCalc->getBoth(), 
                                                         '#'.$entrySemCalc->getPrevSemesterCalculator()->getBoth()))
                       . '"><img alt="Um ein Semester vorziehen" title="Um ein Semester vorziehen" src="grafik/nach_oben.gif"/></a>';
  }
  return $ret;  
}

private function getEntryFormForPlan(User $user, ScheduleEntry $myentry) {

  $realSemCalc = new SemesterCalculator(); // tatsaechliches, aktuelles semester (initialisiert ueber date)
  
  $entrySemCalc = new SemesterCalculator(); // semester, fuer das der eintrag eingeplant wurde (siehe advisor__schedule)
	$entrySemCalc->setSemesterWord($myentry->getSemester());
	$entrySemCalc->setSemesterYear($myentry->getSemYear());

  $mark_plan_readable = ($myentry->getMarkPlanned() > 0 ? sprintf("%1.1f", $myentry->getMarkPlanned()) : '');
  $input_mark_plan = '<input style="width:66px;" type="text" name="plan_' . $myentry->getId() . '" value="' . $mark_plan_readable . '" />';

  $ret = ''; // per default goar nix  
  //if ($myentry->getMarkReal() > 0) {
  //  $ret = $mark_plan_readable; // wenn eine pruefungsnote note im system existiert: einfach planwert anzeigen, nicht bearbeitbar
  //} else {    
    // plannote soll immer eingegeben werden koennen, wenn noch keine echte note vorhanden
    //if ($entrySemCalc->compare($realSemCalc) >= 0) {
      $ret = $input_mark_plan; // wenn aktuelles semester oder in der zukunft: form anzeigen
    //}
  //}
  
  return $ret;
  
}

private function getEntryFormForReal(User $user, ScheduleEntry $myentry) {

  $mark_real_readable = ($myentry->getMarkReal() > 0 ? sprintf("%1.1f", $myentry->getMarkReal()) : '');
  $input_mark_real = '<input style="width:66px;" type="text" name="real_' . $myentry->getId() . '" value="' . $mark_real_readable . '" />';

  //$ret = ''; // per default goar nix  
  //if ($this->getConf()->getConfString('ucImporter', 'useimportedmarks') == 'false' || $user->getType() == 'admin') {
    $ret = $input_mark_real;
  //} else {    
  // $ret = $mark_real_readable;
  //}
  
  return $ret;
  
}


private function printEmptySchedule(User $user) {
  
  $link = $this->getUsecaseLink('changeuserdata');
  $tpl = $this->getConf()->getConfString('ucPlaner', 'linkchangeusertemplate');
  if (trim($user->getMatNr()) != '' && trim($user->getSemStart()) != '' && $user->getMajId() > 0) {
    $link = $this->getOwnLink('create');
    $tpl = $this->getConf()->getConfString('ucPlaner', 'linkcreatetemplate');
  }
  
	$gen = new HtmlGenerator($tpl, $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$gen->apply($this->getConf()->getConfString('ucPlaner', 'linkcreate'), $link);
  return $gen->getHTML();  
}

private function printEntryHeader(SemesterCalculator $curSemester) {
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'entryheadtemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$gen->apply($this->getConf()->getConfString('ucPlaner', 'linkplan'), $this->getOwnLink('plan', Array('#'.$curSemester->getBoth())));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'semester_short'), $curSemester->getBoth());
	$gen->apply($this->getConf()->getConfString('ucPlaner', 'semester_readable'), $curSemester->getSemesterReadable());
  return $gen->getHTML();
}

private function printPrognose(User $user, SemesterCalculator $lastSemester, $duration) {
  
  $hFirstSemester = new SemesterCalculator();
  $hFirstSemester->setBoth($user->getSemStart());
  
  $hCurSemester = new SemesterCalculator();
  
  // text for prognose
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'prognosetemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'firstsemester'), $hFirstSemester->getSemesterReadable());
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'lastsemester'), $lastSemester->getSemesterReadable());
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'duration'), $duration);
  
  // progress bar, hard-coded layout, but who cares...
  $i=0;
  $itSemester = new SemesterCalculator();
  $itSemester->setBoth($hFirstSemester->getBoth());
  $bar = '<table style="border-width:2px; border-style:solid; border-color:#003366;"><tr>';
  while ($itSemester->compare($lastSemester) <= 0) {
    $color = ' bgcolor="#FFFFFF" ';
    if ($itSemester->compare($hCurSemester) == 0) {
      $color = ' bgcolor="#FFC30A" ';
    }
    $bar .= '<td ' .$color. ' width="50px" align="center" style="font-size:9pt;">';
    $bar .= '<a href="#'.$itSemester->getSemesterWord().$itSemester->getSemesterYear().'">';
    $bar .= strtoupper($itSemester->getSemesterWord()).'<br/>'.$itSemester->getSemesterYearReadable();
    $bar .= '</a></td>';
    $itSemester->addSemester(1);
    $i++;
  }
  
  
  $bar .= '</tr></table>';
  
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'duration_total'), $i);
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'progbar'), $bar);
  
  return $gen->getHTML();
}

private function printEntryFooter($sum_ects) {
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'entryfoottemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  $gen->apply($this->getConf()->getConfString('ucPlaner', 'sum_ects'), $sum_ects);
  return $gen->getHTML();  
}

private function printScheduleFooter(User $user) {
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPlaner', 'schedulefoottemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  return $gen->getHTML();  
}

} // class
?>