<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: uc_info.php
 * $Revision: 1.13 $
 * Erstellt am: 24.05.2006
 ***********************************************************************************/


class ucInfo extends UseCase {
	
	const STEP_PAGE = 'page';
	const STEP_BLOCK = 'block';
	
	const PARAMETER_SID = 'sid';
	const PARAMETER_BLID = 'blid';
	
	//Ausführung: Business-Logik
	public function execute() {
		try {
			$step = $this->getStep();
			if ($step == null or $step == $this->getConf()->getConfString('standardstep'))
				throw new MissingParameterException('Ein Parameter fehlt.');
			
			switch ($step)
			{
				case ucInfo::STEP_PAGE:
					//zeige einzelne Infoseite an
					$pageId = $this->getParam()->getParameter(ucInfo::PARAMETER_SID);
					if ($pageId) $this->appendOutput($this->showSinglePage($pageId));
					else throw new MissingParameterException('Der Parameter '.ucInfo::PARAMETER_SID.' ist ungültig ('.$pageId.').');
					break;
					
				case ucInfo::STEP_BLOCK:
					//zeige eine Block-Infoseite an
					$blId = $this->getParam()->getParameter(ucInfo::PARAMETER_BLID);
					if ($blId) $this->appendOutput($this->showBlockPage($blId));
					else throw new MissingParameterException('Der Parameter '.ucInfo::PARAMETER_BLID.' ist ungültig ('.$blId.').');
					break;

				default:
					throw new MissingParameterException('Der Parameter ist ungültig ('.$step.').');
					break;	
			}
			
			$this->setOutputType(USECASE_HTML);
			return true;
		} catch (ModelException $e) {
 			$this->setError('Bei der Verarbeitung ist ein Fehler aufgetreten.<br>'.$e->getMessage());
 			return false;
 		}  catch (MissingParameterException $e) {
 			$this->setError($e->getMessage());
 			return false;
		}
	}
	
	/**
	 * showSinglePage() zeigt eine Infoseite zu EINEM Thema an
	 * 
	 * @param $sid eine SurveyID, zu der die Info gehört
	 * @return die Infoseite als HTML-Fragment
	 */
	private function showSinglePage($sid)
	{
		$infoPage = Info::getForSid($this, $sid);
		
		$template = $this->getConf()->getConfString('ucInfo', 'tpl_file', 'page');
		
		//Zur Anzeige wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $template, $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
			
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucInfo', 'tpl_replace', 'blocktitle'), $infoPage->getBlock($this)->getInfoTitle());
		$generator->apply($this->getConf()->getConfString('ucInfo', 'tpl_replace', 'title'), $infoPage->getTitle());
		$generator->apply($this->getConf()->getConfString('ucInfo', 'tpl_replace', 'infotext'), $infoPage->getLongInfo());
		$generator->apply($this->getConf()->getConfString('ucInfo', 'tpl_replace', 'testbutton'), $this->getTestBox($infoPage->getSId(), 1));
		
		return $generator->getHTML();
	}
	
	/**
	 * showBlockPage() zeigt eine Block-Infoseite an
	 * 
	 * @param $blid eine Blockid
	 * @return die Infoseite als HTML-Fragment
	 */
	private function showBlockPage($blid)
	{
		$infoPage = Info::getFirstForSurveyBlock($this, $blid); //"erste" Info
		$counter = 0; //Zähler initialisieren
		
		//um das richtige Template für den Generator zu bekommen, können wir das erste Infoobjekt benutzen (es gehören ja alle zum selben Block)
		$template = $infoPage->getBlock($this)->getInfoTemplate();
		$title = $infoPage->getBlock($this)->getInfoTitle();
		$generator = new HtmlGenerator( $template, $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		$generator->apply($this->getConf()->getConfString('ucInfo', 'tpl_replace', 'blocktitle'), $title);
		
		$infoPages = Info::getForBlock($this, $blid);
		
		foreach ($infoPages as $infoPage)
		{
			$counter ++;
			
			//ACHTUNG: Ersetzungen werden "durchnummeriert", also z.B. ###:###TEST1###:###, ###:###TEST2###:### usw.!
			$generator->apply($this->getConf()->getConfString('ucInfo', 'tpl_replace', 'title').$counter, $infoPage->getTitle());
			$generator->apply($this->getConf()->getConfString('ucInfo', 'tpl_replace', 'infotext').$counter, $infoPage->getShortInfo());
			$generator->apply($this->getConf()->getConfString('ucInfo', 'tpl_replace', 'testbutton').$counter, $this->getTestBox($infoPage->getSId(), ($counter*2) )); //conter * 2 wegen: 2 Buttons pro Info...
			$generator->apply($this->getConf()->getConfString('ucInfo', 'tpl_replace', 'infobutton').$counter, $this->getInfoBox($infoPage->getSId(), (($counter*2)-1) )); //conter * 2 - 1 wegen: 2 Buttons pro Info...
		}
		
		return $generator->getHTML();
	}
	
	/**
	 * getTestBox() liefert eine Box, die einen Button zum jeweiligen Test enthält
	 * @param int $sid die Survey-ID für den Test
	 * @param int $tabIndex Tabindex für den Button
	 * @return das HTML-Fragment als String
	 */
	 private function getTestBox($sid, $tabIndex = 1)
	 {
	 		$formGen = new HtmlFormGenerator();
	 		
			$buttonData = new HtmlFormGeneratorData();
			$buttonData->setNameAndId('test'.$sid);
			$buttonData->setClass('surveyor_next_button');
			$buttonData->setTabIndex($tabIndex);
			$buttonData->setValue($this->getConf()->getConfString('ucInfo', 'button', 'test'));
			$button = $formGen->getSubmitButtonByDataObject($buttonData);
			
			return $formGen->getForm('testform'.$sid, $this->getSurveyLink($sid), $button);
	 }

	/**
	 * getTestBox() liefert eine Box, die einen Button zur jeweiligen Infoseite enthält
	 * @param int $sid dieID für die Infoseite
	 * @param int $tabIndex Tabindex für den Button
	 * @return das HTML-Fragment als String
	 */
	 private function getInfoBox($sid, $tabIndex = 1)
	 {
	 		$formGen = new HtmlFormGenerator();
	 		
			$buttonData = new HtmlFormGeneratorData();
			$buttonData->setNameAndId('info_'.$sid);
			$buttonData->setTabIndex($tabIndex);
			$buttonData->setValue($this->getConf()->getConfString('ucInfo', 'button', 'infos'));
			$button = $formGen->getSubmitButtonByDataObject($buttonData);
			
			return $formGen->getForm('infoform'.$sid, $this->getUsecaseLink('info', ucInfo::STEP_PAGE, Array(ucInfo::PARAMETER_SID.'='.$sid)), $button);
	 }

//********************************************************************
//TODO: alles hier unten ist aus ucOverview kopiert und muss konsolidiert werden
//*********************************************************************

	/**
	 * generiert den "surveylink", also einen Verweis auf eine Survey
	 * alternativ: einen Link auf die Loginseite für nicht angemeldete Nutzer
	 * @param $sid die SurveyId
	 * @return der survey-Link
	 */	
	 private function getSurveyLink($sid)
	 {
		$surveyLink = $this->getUsecaseLink('survey', '', Array('sid='.$sid));
		//falls der User nicht angemeldet ist: dann zeigt der survey-Link auf Login, inkl. Weiterleitungsziel
		if (!$this->getSess()->isAuthenticated())
		{
			$infotype = $this->getStep();
			$infoid = ($infotype==ucInfo::STEP_PAGE) ? $this->getParam()->getParameter(ucInfo::PARAMETER_SID) : $this->getParam()->getParameter(ucInfo::PARAMETER_BLID);
			$surveyLink = $this->getUsecaseLink('login', '', Array('target='.urlencode($this->getUsecaseLink('survey', '', Array('sid='.$sid, 'infotype='.$infotype, 'infoid='.$infoid)))));
		}
	 	
	 	return $surveyLink;
	 }



}
?>