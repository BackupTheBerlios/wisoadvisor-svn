<?php
class ucPerfOpt extends UseCase {

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
			
			case 'setup':
			  $this->showSetup();
			  break;
	    
			case 'details':
				$schid = $this->getParam()->getParameter(ucPlaner::PARAMETER_SCHID);
				if ($schid) {
   				$this->showDetails($schid);
				} else {
				  throw new MissingParameterException('Ein  Parameter ist ungültig.');
				}
        break;
      
			default:
        $this->showScorecard();        
	  }

    $ret = true;
	}
	return $ret;
}

private function showSetup() {
  
  /*
   * showSetup soll anzeigen
   *   -- POPT-Parameter (neue Tabelle definieren)
   *   -- eine Form, um diese zu ändern
   */
  
}

private function showDetails($schid) {
  
  $user = User::getForId($this, $this->getSess()->getUid());
  $entry = ScheduleEntry::getForId($this, $schid);
  
  /*
   * showDetails soll anzeigen
   *   -- Notenverteilungen (jpgraph)
   *   -- die eigene Position, siehe DA Berchtold
   */
  
}

private function showScorecard() {
  
  $htmlForScorecard = ''; // alles generierte html der eigentlichen scorecard
  
  $hMarksPlanTotalArray = Array();
  $hMarksPlanGroupArray = Array();
  
  $hMarksRealTotalArray = Array();
  $hMarksRealGroupArray = Array();
  
	$user = User::getForId($this, $this->getSess()->getUid());
	$scheduleEntries = ScheduleEntry::getForUserGrouped($this, $user->getId(), $user->getMajId());

	// no schedule found => show link to create a new one
	if (empty($scheduleEntries)) {
	  $this->appendOutput($this->printEmptyScorecard($user));
	  
	// found schedule => create html and store it in $htmlForScorecard
	} else {

	  // erstmal die durchschnittsnoten usw. berechnen
    foreach ($scheduleEntries as $myentry) {
      if ($myentry->getMarkReal() > 0 && $myentry->getMarkReal() <= 4.0) {        
        $hMarksRealGroupArray[$myentry->getMgrpId()]['ects'] += $myentry->getEcts();
        $hMarksRealGroupArray[$myentry->getMgrpId()]['mark'] += $myentry->getEcts() * $myentry->getMarkReal();        
        $hMarksRealTotalArray['ects'] += $myentry->getEcts();
        $hMarksRealTotalArray['mark'] += $myentry->getEcts() * $myentry->getMarkReal();
      }
      if ($myentry->getMarkPlanned() > 0 && $myentry->getMarkPlanned() <= 4.0) {
        $hMarksPlanGroupArray[$myentry->getMgrpId()]['ects'] += $myentry->getEcts();
        $hMarksPlanGroupArray[$myentry->getMgrpId()]['mark'] += $myentry->getEcts() * $myentry->getMarkPlanned();        
        $hMarksPlanTotalArray['ects'] += $myentry->getEcts();
        $hMarksPlanTotalArray['mark'] += $myentry->getEcts() * $myentry->getMarkPlanned();        
      }
	  }
	  
	  $firstrun = true; // beim ersten lauf keinen footer anzeigen, ...
    $oldGroupId = -1000017; // aber dafuer einen header :-)

	  // dann die einzelnen gruppen anzeigen (wie ucPlaner)
	  foreach ($scheduleEntries as $myentry) {
	    
	    // wenn grpid des letzten schleifendurchlaufs != grpid des aktuellen eintrags, dann neue tabelle beginnen
	    if ($oldGroupId != $myentry->getMgrpId()) {	      
	      $oldGroupId = $myentry->getMgrpId();
	      if ($firstrun) {
	        $firstrun=false; // no footer before the first semester
	      } else {
	        $htmlForScorecard .= $this->printGroupFooter(); // footer for single group
	      }
	      $htmlForScorecard .= $this->printGroupHeader($myentry->getMgrpId(), 
	                                                   $hMarksPlanGroupArray[$myentry->getMgrpId()], 
	                                                   $hMarksRealGroupArray[$myentry->getMgrpId()]);
	    }
	    $htmlForScorecard .= $this->printGroupEntry($user, $myentry); // entry
		
    } // foreach $myentry
		
    $htmlForScorecard .= $this->printGroupFooter(); // final group footer    
  
	  // finally, build the page
		$this->appendOutput($this->printScorecardHeader($user));
	  $this->appendOutput($htmlForScorecard);
	  $this->appendOutput($this->printScorecardFooter($user, $hMarksPlanTotalArray, $hMarksRealTotalArray));
  }
  return true;  
}

private function printScorecardHeader(User $user) {
	$gen = new HtmlGenerator( $this->getConf()->getConfString('ucPerfOpt', 'htmltemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'username'), $user->getUserName());
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'studies'), $user->getStudies());
  return $gen->getHTML();
}

private function printScorecardFooter(User $user, $hMarksPlanTotalArray, $hMarksRealTotalArray) {

  $hMarkPlan = ($hMarksPlanTotalArray['ects'] > 0) ? substr(sprintf("%1.11f", $hMarksPlanTotalArray['mark']/$hMarksPlanTotalArray['ects']), 0, 3) : '';
  $hMarkReal = ($hMarksRealTotalArray['ects'] > 0) ? substr(sprintf("%1.11f", $hMarksRealTotalArray['mark']/$hMarksRealTotalArray['ects']), 0, 3) : '';
  
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPerfOpt', 'htmlfoottemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));  
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_total_plan'), $hMarkPlan);
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_total_real'), $hMarkReal);
	
	return $gen->getHTML();  
}

private function printEmptyScorecard(User $user) {
  
  $link = $this->getUsecaseLink('changeuserdata');
  $tpl = $this->getConf()->getConfString('ucPlaner', 'linkchangeusertemplate');
  if (trim($user->getMatNr()) != '' && trim($user->getSemStart()) != '' && $user->getMajId() > 0) {
    $link = $this->getUsecaseLink('ucPlaner', 'create');
    $tpl = $this->getConf()->getConfString('ucPlaner', 'linkcreatetemplate');
  }
  
	$gen = new HtmlGenerator($tpl, $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$gen->apply($this->getConf()->getConfString('ucPlaner', 'linkcreate'), $link);
  return $gen->getHTML();  
}

private function printGroupHeader($mgrpid, $hMarkPlanArray, $hMarkRealArray) {
  
  $hGroup = ModuleGroup::getForId($this, $mgrpid);
  $hMarkPlan = ($hMarkPlanArray['ects'] > 0) ? substr(sprintf("%1.11f", $hMarkPlanArray['mark']/$hMarkPlanArray['ects']), 0, 3) : '&nbsp;';
  $hMarkReal = ($hMarkRealArray['ects'] > 0) ? substr(sprintf("%1.11f", $hMarkRealArray['mark']/$hMarkRealArray['ects']), 0, 3) : '&nbsp;';
  
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPerfOpt', 'entryheadtemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'group'), $hGroup->getName());
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_group_plan'), $hMarkPlan);
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_group_real'), $hMarkReal);
	
  return $gen->getHTML();
}

private function printGroupFooter() {
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPerfOpt', 'entryfoottemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  return $gen->getHTML();  
}

private function printGroupEntry(User $user, ScheduleEntry $myentry) {
  
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPerfOpt', 'entrytemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  
  //$avgMarkPlan = ScheduleEntryStatistics::getAvgPlanByLecture($this, $myentry);
  //$avgMarkReal = ScheduleEntryStatistics::getAvgRealByLecture($this, $myentry);
  
  // links und form zzgl. standards ins template parsen auffuellen
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mod_name'), $myentry->getModName().($myentry->isAssessment()=='true'?'*':''));
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'ects'), $myentry->getEcts());
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'try'), $myentry->getTry());
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_plan'), ($myentry->getMarkPlanned() > 0 ? sprintf("%1.1f", $myentry->getMarkPlanned()) : ''));
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_plan_avg'), ($avgMarkPlan > 0 ? sprintf("%1.1f", $avgMarkPlan) : ''));
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_real'), ($myentry->getMarkReal() > 0 ? sprintf("%1.1f", $myentry->getMarkReal()) : ''));
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_real_avg'), ($avgMarkReal > 0 ? sprintf("%1.1f", $avgMarkReal) : ''));
  return $gen->getHTML();
  
}


} // class
?>