<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: uc_overview.php
 * $Revision: 1.35 $
 * Erstellt am: 11.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
class ucOverview extends UseCase
{
	//Ausführung: Business-Logik
	public function execute()
	{
		if (!$this->getSess()->isAuthenticated())
			header('location:'.$this->getUsecaseLink('login', '', Array('target='.urlencode($this->getUsecaseLink('overview')))));
			
		try {
			//Unterblöcke / Surveys laden
			$blocks = SurveyBlock::getAll($this);
			$htmlCode = '';
			foreach ($blocks as $block) {
				switch ($block->getType()) {
					case SurveyBlock::TYPE_BARS:
						$htmlCode .= $this->showBarsBlock($block);
						break;
					case SurveyBlock::TYPE_ICONS:
						$htmlCode .= $this->showIconsBlock($block);
						break;
				}
			}
			
			//Zur Anzeige des Formulars wird der HTML-Generator benutzt
			$generator = new HtmlGenerator( $this->getConf()->getConfString('ucOverview', 'overview_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
			//befülle den Generator mit den zu ersetzenden Anteilen...
			$formGen = new HtmlFormGenerator();
			$buttonData = new HtmlFormGeneratorData();
			$buttonData->setNameAndId('getpdf');
			$buttonData->setClass('surveyor_next_button');
			$buttonData->setTabIndex(1);
			$buttonData->setValue($this->getConf()->getConfString('ucOverview', 'button', 'email'));
			$button = $formGen->getSubmitButtonByDataObject($buttonData);
			
			$generator->apply('emailpdf', $formGen->getForm('pdfform', $this->getUsecaseLink('getpdf'), $button) );
			$generator->apply('blocks', $htmlCode);
			$generator->apply('legend', $this->getConf()->getConfString('messages', 'legend_relative'));
		
			//HTML in den Output schreiben...
			$this->appendOutput($generator->getHTML());
			$this->setOutputType(USECASE_HTML);
			return true;
		} catch (ModelException $e) {
			$this->setError('Bei der Verarbeitung ist ein Fehler aufgetreten.<br>'.$e->getMessage());
 			return false;
		}
	}
	
	/**
	 *  Hilfsmethode für die Abbildungen Haken oder Fragezeichen
	 */
	private function getCheck($completed, $yellowBackground = false) {
		if ($yellowBackground) {
			if ($completed)
				$checkImage = $this->getUsecaseLink('graphics',ucGraphics::STEP_CHECK, Array(ucGraphics::PAR_BACKGROUND.'='.ucGraphics::BACKGROUND_YELLOW));
			else
				$checkImage = $this->getUsecaseLink('graphics',ucGraphics::STEP_QUESTIONMARK, Array(ucGraphics::PAR_BACKGROUND.'='.ucGraphics::BACKGROUND_YELLOW));
		} else {
			if ($completed)
				$checkImage = $this->getUsecaseLink('graphics',ucGraphics::STEP_CHECK);
			else
				$checkImage = $this->getUsecaseLink('graphics',ucGraphics::STEP_QUESTIONMARK);
		}
		return $checkImage;	
	} 
	
	/**
	 * Hilfsmethode zur Smiley-Anzeige
	 */
	private function getSmiley($result = null, $yellowBackground = false) {
		// TODO : Result-Auswertung in DB!!
		if ($result <= 33.3) $smiley = ImageCreator::SMILEY_POOR;
		if ($result > 33.3 and $result <= 66.7) $smiley = ImageCreator::SMILEY_AVERAGE;
		if ($result > 66.7) $smiley = ImageCreator::SMILEY_GOOD;
		if ($result == null) $smiley = ImageCreator::SMILEY_UNKNOWN;
		if ($yellowBackground)
			return $this->getUsecaseLink('graphics',ucGraphics::STEP_SMILEY, Array(ucGraphics::PAR_SMILEY_ASPECT.'='.$smiley, ucGraphics::PAR_BACKGROUND.'='.ucGraphics::BACKGROUND_YELLOW));
		else
			return $this->getUsecaseLink('graphics',ucGraphics::STEP_SMILEY, Array(ucGraphics::PAR_SMILEY_ASPECT.'='.$smiley));
	} 
	
	/**
	 * Zeigt einen Block vom Typ 'bars' an, der sein Ergebnis als Statusbalken darstellt
	 * 
	 * @param SurveyBlock $block Der darzustellende SurveyBlock
	 */
	private function showBarsBlock(SurveyBlock $block) {
		$completed = $block->isCompleted($this, $this->getSess()->getUid());

		$infolink = $this->getUsecaseLink('info', ucInfo::STEP_BLOCK, Array(ucInfo::PARAMETER_BLID.'='.$block->getId()));
		
		$lines = '';
		$surveys = Survey::getForBlock($this, $block->getId());
		foreach ($surveys as $survey) {
			$lines .= $this->showBarsLine($survey);
		}

		//Zur Anzeige des Formulars wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucOverview', 'block_bars_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply('check_image', $this->getCheck($completed, true));
		$generator->apply('title', $block->getTitle());
		$generator->apply('infolink', $infolink);
		$generator->apply('lines', $lines);
		return $generator->getHTML();
	}
	
	/**
	 * Zeigt innerhalb eines Block die zugehörigen Surveys an.
	 * Der Block ist vom Typ 'bars', er stellt sein Ergebnis als Statusbalken dar.
	 * 
	 * @param Survey $survey Die darzustellende Survey
	 */
	private function showBarsLine(Survey $survey) {
		$attempt = $survey->getAttemptForUser($this, $this->getSess()->getUid());
		$completed = $attempt > 0;
		$ranking = null;
		$ratingImage = $this->getUsecaseLink('graphics',ucGraphics::STEP_EMPTY_RESULTBAR);
		$ratingText = 'Klick hier um den Test zu bearbeiten';
		if ($completed) {
			$surveyResult = new SurveyResult($this, $survey, $this->getSess()->getUid(), $attempt);
			$ranking = $surveyResult->getRanking(); 
			$ratingImage = $this->getUsecaseLink('graphics',ucGraphics::STEP_RANKINGBAR, Array(ucGraphics::PAR_RANKING.'='.$ranking));
			$ratingText = $this->getConf()->getConfString('ucOverview', 'text', 'bar_title');
			$ratingText = str_replace( '###:###result###:###', 100-round($ranking), $ratingText);
			if ((100-round($ranking))<1) $ratingText =  $this->getConf()->getConfString('ucOverview', 'text', 'bar_title_best');
		}
		
		$infolink = $this->getUsecaseLink('info', ucInfo::STEP_PAGE, Array(ucInfo::PARAMETER_SID.'='.$survey->getId()));
		$surveyLink = $this->getSurveyLink($completed, $survey);

		//Zur Anzeige des Formulars wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucOverview', 'line_bars_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply('check_image', $this->getCheck($completed));
		$generator->apply('link', $surveyLink);
		$generator->apply('title', $survey->getTitle());
		$generator->apply('smiley_image', $this->getSmiley($ranking));
		$generator->apply('rating_image', $ratingImage);
		$generator->apply('rating_text', $ratingText);
		$generator->apply('infolink', $infolink);
		return $generator->getHTML();
	}
	
	/**
	 * Zeigt einen Block vom Typ 'icons' an, der seine Ergebnisse als Smileys darstellt.
	 * Solche Blöcke enthalten jeweils Umfragen, die alle die gleichen Merkmale abprüfen,
	 * z.B. Die Eignung für die einzelnen Studiengänge/Bachelor.
	 * 
	 * @param SurveyBlock $block Der darzustellende SurveyBlock
	 */
	private function showIconsBlock(SurveyBlock $block) {
		$completed = $block->isCompleted($this, $this->getSess()->getUid());

		$infolink = $this->getUsecaseLink('info', ucInfo::STEP_BLOCK, Array(ucInfo::PARAMETER_BLID.'='.$block->getId()));

		$characteristics = Characteristic::getForBlock($this, $block->getId());
		
		$surveys = Survey::getForBlock($this, $block->getId());
		$lines = '';
		foreach ($surveys as $survey) {
			$lines .= $this->showIconsLine($survey, $characteristics);
		}
		
		//Zur Anzeige des Formulars wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucOverview', 'block_icons_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply('check_image', $this->getCheck($completed, true));
		$generator->apply('title', $block->getTitle());
		//$generator->apply('columns', $columns);
		$generator->apply('infolink', $infolink);
		$generator->apply('lines', $lines);
		return $generator->getHTML();
	}

	/**
	 * Zeigt innerhalb eines Block die zugehörigen Surveys an.
	 * Der Block ist vom Typ 'icons', er stellt sein Ergebnis als Smileys dar.
	 * 
	 * @param Survey $survey Die darzustellende Survey
	 * @param Array $characteristics Alle im Block dargestellten Mermale
	 */
	private function showIconsLine(Survey $survey, $characteristics) {
		$completed = $survey->isCompleted($this, $this->getSess()->getUid());

		$infolink = $this->getUsecaseLink('info', ucInfo::STEP_PAGE, Array(ucInfo::PARAMETER_SID.'='.$survey->getId()));
		$surveyLink = $this->getSurveyLink($completed, $survey);
	
		//Zur Anzeige des Formulars wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucOverview', 'line_icons_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		if ($characteristics != null) foreach ($characteristics as $char) {
			$result = null;
			$attempt = $survey->getAttemptForUser($this, $this->getSess()->getUid());
			if ($survey->isCompleted($this, $this->getSess()->getUid()))
				$result = $survey->getResultForUserChar($this, $this->getSess()->getUid(), $char->getId(), $attempt);
			$generator->apply('char'.$char->getId(), $this->getSmiley($result));
		}
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply('check_image', $this->getCheck($completed));
		$generator->apply('title', $survey->getTitle());
		$generator->apply('link', $surveyLink);
		$generator->apply('infolink', $infolink);
		return $generator->getHTML();
	}

	/**
	 * generiert den "surveylink", also einen Verweis auf eine Survey
	 * alternativ: einen Link auf die Loginseite für nicht angemeldete Nutzer
	 * @param $completed true, wenn der Nutzer die Umfrage schon ausgefüllt hatte
	 * @param $sid die SurveyId (optional)
	 * @return der survey-Link
	 */	
	 private function getSurveyLink($completed, Survey $survey)
	 {
		$surveyLink = '';
		
		if (!$completed)
			$surveyLink = $this->getUsecaseLink('survey', '', Array('sid='.$survey->getId()));
		else
			$surveyLink = $this->getUsecaseLink('survey_result', '', Array('sid='.$survey->getId(), 'attempt='.$survey->getAttemptForUser($this, $this->getSess()->getUid())));
		//falls der User nicht angemeldet ist: dann zeigt der survey-Link auf Login
		if (!$this->getSess()->isAuthenticated())
			$surveyLink = $this->getUsecaseLink('login');
	 	
	 	return $surveyLink;
	 }
}
?>
