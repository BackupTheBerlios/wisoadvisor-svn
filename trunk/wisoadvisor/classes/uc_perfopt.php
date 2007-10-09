<?php
class ucPerfOpt extends UseCase {
	
  const PARAMETER_SCHID = 'schid';
  
	const PARAMETER_TOLERANCE = 'popt_tolerance';
	const PARAMETER_WORSTMARK = 'popt_worstmark';

	const DEFAULT_TOLERANCE = '0.5';
	const DEFAULT_WORSTMARK = 'true';
	
	
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
			
	    case 'configure':
	      $this->showConfiguration();
	      break;
	      
	    case 'details':
				$schid = $this->getParam()->getParameter(ucPerfOpt::PARAMETER_SCHID);
				if ($schid) {
   				$this->showDetails($schid);
				} else {
				  throw new MissingParameterException('Ein  Parameter ist ungültig.');
				}
        break;
      
			default:
	      $this->saveParams();
			  $this->showScorecard();        
	  }

    $ret = true;
	}
	return $ret;
}

private function saveParams() {
  
  $uid = $this->getSess()->getUid();
  
  $tol = $this->getParam()->getParameter(ucPerfOpt::PARAMETER_TOLERANCE);
  if ($tol) {
    $paramTolerance = UserParameter::getOneForUser($this, $uid, 'popt', 'param', 'tolerance');
    if (! $paramTolerance) {
      $paramTolerance = UserParameter::getNew($this);
      $paramTolerance->setUserId($uid);
      $paramTolerance->setKeys('popt', 'param', 'tolerance');
    }
    $tol = floatval(str_replace(',', '.', $tol));
    $paramTolerance->setValue($tol);
    $paramTolerance->storeInDb($this); 
  }
  
  
  $wrt = $this->getParam()->getParameter(ucPerfOpt::PARAMETER_WORSTMARK);
  if ($wrt) {
    $paramWorstMark = UserParameter::getOneForUser($this, $this->getSess()->getUid(), 'popt', 'param', 'worstmark');
    if (! $paramWorstMark) {
      $paramWorstMark = UserParameter::getNew($this);
      $paramWorstMark->setUserId($uid);
      $paramWorstMark->setKeys('popt', 'param', 'worstmark');
    }
    $paramWorstMark->setValue($wrt);
    $paramWorstMark->storeInDb($this);
  }
    
}

private function showConfiguration() {
  
  $user = User::getForId($this, $this->getSess()->getUid());
  
  $paramTolerance = UserParameter::getOneForUser($this, $user->getId(), 'popt', 'param', 'tolerance');
  $paramWorstMark = UserParameter::getOneForUser($this, $user->getId(), 'popt', 'param', 'worstmark');
  
  $valTolerance = ($paramTolerance ? $paramTolerance->getValue() : ucPerfOpt::DEFAULT_TOLERANCE);
  $valWorstMark = ($paramWorstMark ? $paramWorstMark->getValue() : ucPerfOpt::DEFAULT_WORSTMARK);
  
  $generator = new HtmlGenerator($this->getConf()->getConfString('ucPerfOpt', 'configurationtemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$formGen = new HtmlFormGenerator();
	
	$generator->apply($this->getConf()->getConfString('ucPerfOpt', 'config', 'tolerance'), $formGen->getInput(ucPerfOpt::PARAMETER_TOLERANCE, $valTolerance));

	$cboWorstMark = $formGen->getDropDownFromArray(ucPerfOpt::PARAMETER_WORSTMARK, 
	                                               Array('true' => 'Schlechteste Note im Bereich',
	                                                     'false' => 'Durchschnittsnote'),
	                                               $valWorstMark);
	$generator->apply($this->getConf()->getConfString('ucPerfOpt', 'config', 'worstmark'), $cboWorstMark);

	
	$generator->apply($this->getConf()->getConfString('ucPerfOpt', 'config', 'submit'), $formGen->getSubmitButton('surveyor_next_button', 'Speichern'));
	
	$output = $formGen->getForm('poptconfigform', $this->getOwnLink(), $generator->getHTML());
		
	$this->appendOutput($output);

}

private function showDetails($schid) {
  
  $user = User::getForId($this, $this->getSess()->getUid());
  $entry = ScheduleEntry::getForUserAndId($this, $user, $schid);
  
  if (! $entry) {
    // schid wurde in der URL manuell veraendert => detailansicht ist nur fuer schids des angemeldeten benutzers erlaubt
    $this->appendOutput('<h2>Achti Krachti!</h2>Cheats sind nicht erlaubt! <br/>Eine Meldung an den Administrator wurde abgesetzt.');
    return true;
  }
  
  $cntParticipants = 0;
  $cntBetter = 0;
  $cntWorse = 0;
  $cntEqual = 0;
  
  $aggLevel = $this->getParam()->getParameter('level');
  $this->appendOutput($this->printDetailsHeader($user, $entry, $aggLevel));
 
 /* *** start graph => wird direkt hier erzeugt, wegen des HTML-Tags <img ...> (Graph wird on-the-fly erzeugt) *** */
  $realMarksArray = ScheduleEntryStatistics::getCntReal($this, $entry, $aggLevel);

  /* test data 
  $realMarksArray = Array(Array('mark_real' => '1.0',
                                'cnt_mark'  => '3'),
                          Array('mark_real' => '1.3',
                                'cnt_mark'  => '8'),
                          Array('mark_real' => '1.7',
                                'cnt_mark'  => '16'),
                          Array('mark_real' => '2.0',
                                'cnt_mark'  => '9'),
                          Array('mark_real' => '2.3',
                                'cnt_mark'  => '17'),
                          Array('mark_real' => '2.7',
                                'cnt_mark'  => '12'),
                          Array('mark_real' => '3.0',
                                'cnt_mark'  => '8'),
                          Array('mark_real' => '3.3',
                                'cnt_mark'  => '8'),
                          Array('mark_real' => '3.7',
                                'cnt_mark'  => '2'),
                          Array('mark_real' => '4.0',
                                'cnt_mark'  => '21'),
                          Array('mark_real' => '4.3',
                                'cnt_mark'  => '8'),                                
                          Array('mark_real' => '4.7',
                                'cnt_mark'  => '2'),                                
                          Array('mark_real' => '5.0',
                                'cnt_mark'  => '5'));
    */
    if ($realMarksArray) {
    
	  // necessary init, because graph won't show it otherwise
	  $xArray = array("1.0", "1.3", "1.7", "2.0", "2.3", "2.7", "3.0", "3.3", "3.7", "4.0", "4.3", "4.7", "5.0");  
	  $yArray = Array(0,0,0,0,0,0,0,0,0,0,0,0,0);
	  
	  foreach ($realMarksArray as $hMark) {
	    $pos = array_search($hMark['mark_real'], $xArray);
		  if ($pos !== false) { // important: use special operator here ($pos can both be false and 0, but 0 is a valid pos in xArray)
	      $yArray[$pos] = $hMark['cnt_mark'];
	    }
	    // besser hier als in der datenbank zaehlen 
      // => keine extra db routinen notwendig, um zwischen import und schedule zu unterscheiden
      $cntParticipants += $hMark['cnt_mark'];
	    if ($hMark['mark_real'] < $entry->getMarkReal()) {
	      $cntBetter += $hMark['cnt_mark'];
	    } else if ($hMark['mark_real'] == $entry->getMarkReal()) {
	      $cntEqual += $hMark['cnt_mark'];	      
	    } else {
	      $cntWorse += $hMark['cnt_mark'];	      
	    }
      
	  }
	  
	  // hand over data by session variables to graph script; ugly but works
	  $_SESSION['daten']['y'] = $yArray; 
	  $_SESSION['title'] = '';
	  $_SESSION['daten']['x'] = $xArray;		  
	  
	  $this->appendOutput('<p><img src="graph.php?' .time(). '" /></p>');
    
  }	  
  /* *** ende graph *** */

  // rankings; equal -1, weil der user selbst ausgenommen werden soll
  $this->appendOutput($this->printDetailsFooter($user, $entry, $cntParticipants, $cntBetter, $cntEqual-1, $cntWorse));
  
  return true;
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
      if ($myentry->getMarkReal() > 0) { // && $myentry->getMarkReal() <= 4.0) {        
        $hMarksRealGroupArray[$myentry->getMgrpId()]['ects'] += $myentry->getEcts();
        $hMarksRealGroupArray[$myentry->getMgrpId()]['mark'] += $myentry->getEcts() * $myentry->getMarkReal();
        if ($myentry->getMarkReal() > $hMarksRealGroupArray[$myentry->getMgrpId()]['mark_max']) {
          $hMarksRealGroupArray[$myentry->getMgrpId()]['mark_max'] = $myentry->getMarkReal(); // die schlechteste note bestimmt das symbol in der bsc
        }
        $hMarksRealTotalArray['ects'] += $myentry->getEcts();
        $hMarksRealTotalArray['mark'] += $myentry->getEcts() * $myentry->getMarkReal();
      }
      if ($myentry->getMarkPlanned() > 0) { // && $myentry->getMarkPlanned() <= 4.0) {
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
	      $htmlForScorecard .= $this->printGroupHeader($user,
	                                                   $myentry->getMgrpId(), 
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

private function printDetailsHeader(User $user, ScheduleEntry $entry, $aggLevel) {
  
  $hCalc = new SemesterCalculator();
  $hCalc->setSemesterWord($entry->getSemester());
  $hCalc->setSemesterYear($entry->getSemYear());
  
	$gen = new HtmlGenerator( $this->getConf()->getConfString('ucPerfOpt', 'detailheadtemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'username'), $user->getUserName());
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'studies'), $user->getStudies());
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'matnr'), $user->getMatNr());
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mod_name'), $entry->getModName());
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'semester_readable'), $hCalc->getSemesterReadable());
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_plan'), ($entry->getMarkPlanned() > 0 ? sprintf("%1.1f", $entry->getMarkPlanned()) : ''));
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_real'), ($entry->getMarkReal() > 0 ? sprintf("%1.1f", $entry->getMarkReal()) : ''));
	
	// dropdown_levels
  $input = HtmlFormGenerator::getDropDownFromArray('level', 
                                                   Array(ScheduleEntryStatistics::AGGREGATION_LECTURE => 'Notenverteilung gesamt',
                                                         ScheduleEntryStatistics::AGGREGATION_STUDIES => 'Notenverteilung nach Studiengang',
                                                         ScheduleEntryStatistics::AGGREGATION_MAJOR   => 'Notenverteilung nach Schwerpunkt'), 
                                                   $aggLevel);

	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'linkdetails'), $this->getOwnLink('details'));
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'schid'), $entry->getId());
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'dropdown_levels'), $input);
	
	
	return $gen->getHTML();
}

private function printDetailsFooter($user, $entry, $cntParticipants, $cntBetter, $cntEqual, $cntWorse) {
	
  $percentBetter = ScheduleEntryStatistics::perCent($cntBetter, $cntParticipants);
  $percentEqual  = ScheduleEntryStatistics::perCent($cntEqual, $cntParticipants);
  $percentWorse  = ScheduleEntryStatistics::perCent($cntWorse, $cntParticipants);
  
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPerfOpt', 'detailfoottemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'cnt_participants'), $cntParticipants);
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'cnt_better'), $cntBetter);
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'cnt_equal'), $cntEqual);
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'cnt_worse'), $cntWorse);
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'percent_better'), $percentBetter);
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'percent_equal'), $percentEqual);
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'percent_worse'), $percentWorse);
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'link_popt'), $this->getOwnLink());
  
  return $gen->getHTML();
}

private function printScorecardHeader(User $user) {
	$gen = new HtmlGenerator( $this->getConf()->getConfString('ucPerfOpt', 'htmltemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'username'), $user->getUserName());
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'studies'), $user->getStudies());
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'linkconfigure'), $this->getOwnLink('configure'));
	return $gen->getHTML();
}

private function printScorecardFooter(User $user, $hMarksPlanTotalArray, $hMarksRealTotalArray) {

  $paramTolerance = UserParameter::getOneForUser($this, $user->getId(), 'popt', 'param', 'tolerance');
  $valTolerance = ($paramTolerance ? $paramTolerance->getValue() : ucPerfOpt::DEFAULT_TOLERANCE);
  
  $hMarkPlan = ($hMarksPlanTotalArray['ects'] > 0) ? substr(sprintf("%1.11f", $hMarksPlanTotalArray['mark']/$hMarksPlanTotalArray['ects']), 0, 3) : '&nbsp;';
  $hMarkReal = ($hMarksRealTotalArray['ects'] > 0) ? substr(sprintf("%1.11f", $hMarksRealTotalArray['mark']/$hMarksRealTotalArray['ects']), 0, 3) : '&nbsp;';
  
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPerfOpt', 'htmlfoottemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));  
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_total_plan'), $hMarkPlan);
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_total_real'), $hMarkReal);
  
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'smiley_tolerance_text'), $valTolerance);
	
  $smiley = $this->getConf()->getConfString('ucPerfOpt', 'img_good');
  if ($hMarkPlan + $valTolerance < $hMarkReal) {
    $smiley = $this->getConf()->getConfString('ucPerfOpt', 'img_bad');
  }
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'smiley_total'), $smiley);
	
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

private function printGroupHeader($user, $mgrpid, $hMarkPlanArray, $hMarkRealArray) {
  
  $paramWorstMark = UserParameter::getOneForUser($this, $user->getId(), 'popt', 'param', 'worstmark');
  $valWorstMark = ($paramWorstMark ? $paramWorstMark->getValue() : ucPerfOpt::DEFAULT_WORSTMARK);
  
  $hGroup = ModuleGroup::getForId($this, $mgrpid);
  $hMarkPlan = ($hMarkPlanArray['ects'] > 0) ? substr(sprintf("%1.11f", $hMarkPlanArray['mark']/$hMarkPlanArray['ects']), 0, 3) : '&nbsp;';
  $hMarkReal = ($hMarkRealArray['ects'] > 0) ? substr(sprintf("%1.11f", $hMarkRealArray['mark']/$hMarkRealArray['ects']), 0, 3) : '&nbsp;';
  
  if ($valWorstMark == 'true') {
    $hImageForGroup = substr(sprintf("%1.11f", $hMarkRealArray['mark_max']), 0, 3); 
  } else {
    $hImageForGroup = $hMarkReal;
  }
  
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPerfOpt', 'entryheadtemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'group'), $hGroup->getName());
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_group_plan'), $hMarkPlan);
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_group_real'), $hMarkReal);
	
	$gen->apply($this->getConf()->getConfString('ucPerfOpt', 'smiley'), $this->getScorecardIcon($hImageForGroup, 48));
	
  return $gen->getHTML();
}

private function printGroupFooter() {
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPerfOpt', 'entryfoottemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  return $gen->getHTML();  
}

private function printGroupEntry(User $user, ScheduleEntry $myentry) {
  
  $gen = new HtmlGenerator($this->getConf()->getConfString('ucPerfOpt', 'entrytemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
  
  $avgMarkPlan = ScheduleEntryStatistics::getAvgPlanByLecture($this, $myentry);
  $avgMarkReal = ScheduleEntryStatistics::getAvgRealByLecture($this, $myentry);
  
  $hName = $myentry->getModName().($myentry->isAssessment()=='true'?'*':'');
  if ($myentry->getMarkReal() > 0) {
    $hName = '<a href="' . $this->getOwnLink('details', Array(ucPerfOpt::PARAMETER_SCHID.'='.$myentry->getId())) . '">' . $hName . '</a>';
  }

  // links und form zzgl. standards ins template parsen auffuellen
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mod_name'), $hName);
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'ects'), $myentry->getEcts());
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'try'), $myentry->getTry());
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_plan'), ($myentry->getMarkPlanned() > 0 ? sprintf("%1.1f", $myentry->getMarkPlanned()) : ''));
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_plan_avg'), ($avgMarkPlan > 0 ? sprintf("%1.1f", $avgMarkPlan) : ''));
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_real'), ($myentry->getMarkReal() > 0 ? sprintf("%1.1f", $myentry->getMarkReal()) : ''));
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'mark_real_avg'), ($avgMarkReal > 0 ? sprintf("%1.1f", $avgMarkReal) : ''));
  $gen->apply($this->getConf()->getConfString('ucPerfOpt', 'smiley'), $this->getScorecardIcon($myentry->getMarkReal(), 22)); 
  return $gen->getHTML();
  
}

protected function getScorecardIcon($mark, $size='22') {
    
  $stages = Array (1.3, 2.0, 3.0, 4.0);
   
  $ret = 'leer.gif';
  if ($mark > 0) {
    $finalStage=1;
    foreach ($stages as $hStage) {
      if ($mark > $hStage) {
        $finalStage++;
      }
    }
    $ret = 'sc_stage' . $finalStage . '_' . $size . '.png';
  }
  return $ret;    
}
	


} // class
?>