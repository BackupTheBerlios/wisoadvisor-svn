<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: uc_survey_result.php
 * $Revision: 1.25 $
 * Erstellt am: 08.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
 /**
  * Der SurveyResult-UseCase stellt das Ergebnis einer Umfrage nach deren Beendigung dar.
  */
class ucSurveyResult extends UseCase {
 	
 	const PARAMETER_SURVEY = 'sid';
 	const PARAMETER_ATTEMPT = 'attempt';
 	
	
 	public function execute() {
 		try {
			// PARAMETER BESTIMMEN 			
 			$sid = $this->getParam()->getParameter(self::PARAMETER_SURVEY);
 			if ($sid == null or $sid == '') 
 				throw new MissingParameterException('Es wurde keine Survey-ID übergeben.');
 			// Model-Objekte erzeugen
 			$survey = Survey::getForId($this, $sid);
 			if ($survey == null) {
 				$this->setError('Die Survey mit der ID='.$sid.' existiert nicht.');
 				return false;
 			}
 			
 			$attempt = $this->getParam()->getParameter(self::PARAMETER_ATTEMPT);
 			if ($attempt == null or $attempt == '' or $attempt == 0) 
 				throw new MissingParameterException('Es wurde keine Attempt-Nummer übergeben.');
 				
 			if (!$survey->isCompleted($this, $this->getSess()->getUId(), $attempt)) {
 				$this->setError('Die Survey wurde vom angemeldeten User noch nicht ausgefüllt.');
 				return false;
 			}
			
 			$surveyResult = new SurveyResult($this, $survey, $this->getSess()->getUid(), $attempt);
			
 			$charHtml = '';
 			$showLegend = false;
 			
 			foreach ($surveyResult->getCharResults() as $charResult) {
 				if ($charResult->isVisible() and $charResult->isResultBarVisible())
 				{
					$charHtml .= $this->showCharResult($charResult);
					//sobald mind. 1 Balken angezeigt wird, gibts auch eine Legende
					$showLegend = true;
 				}
				else if ($charResult->isVisible() and !$charResult->isResultBarVisible())
					$charHtml .= $this->showCharResultTextOnly($charResult);
 			}
 			
 			if ($charHtml != '') { // Überleitung zu den Einzelauswertungen einfügen, falls nötig.
 				$generator = new HtmlGenerator( $this->getConf()->getConfString('ucSurveyResult', 'char_header_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
 				$generator->apply('ranking_image', $this->getUsecaseLink('graphics',ucGraphics::STEP_RANKINGBAR, Array(ucGraphics::PAR_RANKING.'='.$surveyResult->getRanking())));
 				$generator->apply('char_header', $this->getConf()->getConfString('messages', 'charheader'));
 				$charHtml = $generator->getHTML().$charHtml;
 			}
 			
 			$legend = ($showLegend) ? $this->getConf()->getConfString('messages', 'legend') : '';
 			
 			//Zur Anzeige des Formulars wird der HTML-Generator benutzt
			$generator = new HtmlGenerator( $this->getConf()->getConfString('ucSurveyResult', 'survey_result_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
			//befülle den Generator mit den zu ersetzenden Anteilen...
			$generator->apply('survey_title', $survey->getTitle());
			$generator->apply('result_header', $surveyResult->getResultHeader());
			
			$generator->apply('legend', $legend);
			
			//für den zurück-Button wird ein Formular generiert:
			$formGen = new HtmlFormGenerator();
			
			$buttonData = new HtmlFormGeneratorData();
			$buttonData->setNameAndId('overviewButton');
			$buttonData->setValue('Ergebnisübersicht');
			$jumpUrl = $this->getUsecaseLink('overview');
			$buttonData->setOnClick('javascript:parent.location.href=\''.$jumpUrl.'\';');
			$generator->apply('left_navigation', $formGen->getButtonByDataObject($buttonData));
			
			$buttonData = new HtmlFormGeneratorData();
			$buttonData->setNameAndId('surveyor_next_button');
			$buttonData->setValue($this->getConf()->getConfString('ucSurvey', 'button', 'next'));
			$nextSurvey = $survey->getSuccessor($this);
			//echo 'nextSurvey = '.$nextSurvey->getId();
			if ($nextSurvey == null) {
				$block = $survey->getBlock($this)->getSuccessor($this); 	// nächsten Block ermitteln
				if ($block != null)
					$nextSurvey = Survey::getFirstForSurveyBlock($this, $block->getId());
			}
			if ($nextSurvey != null) 
				$jumpUrl = $this->getUsecaseLink('survey', '', Array('sid='.$nextSurvey->getId()));
			else
				$jumpUrl = $this->getUsecaseLink('overview');
			$buttonData->setOnClick('javascript:parent.location.href=\''.$jumpUrl.'\';');
			$generator->apply('right_navigation', $formGen->getButtonByDataObject($buttonData));
			
			//Gespeicherten Output aller Subusecases einfügen
			$generator->apply('characteristics', $charHtml);
		
			//HTML in den Output schreiben... vorher ein Form drum herum bauen:
			$content = $generator->getHTML();
			$out = $formGen->getForm('backform', $this->getUsecaseLink('start'), $content);
			$this->appendOutput($out);
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
 	 * Liefert den Ergebnistext für ein Merkmal
 	 * 
 	 * @param CharResult $charResult
 	 * @return String 
 	 */
 	private function showCharResult(CharResult $charResult) {
		// RESULTAT ERMITTELN
		$result = $charResult->getResult();
		$smileyImage = $this->getUsecaseLink('graphics',ucGraphics::STEP_SMILEY, Array(ucGraphics::PAR_SMILEY_ASPECT.'='. $charResult->getSmileyAspect(), ucGraphics::PAR_BACKGROUND.'='.ucGraphics::BACKGROUND_YELLOW));
		$ratingImage = $this->getUsecaseLink('graphics',ucGraphics::STEP_RESULTBAR, Array(ucGraphics::PAR_MINIMUM.'='.$charResult->getResultBarMinimum(), ucGraphics::PAR_MAXIMUM.'='.$charResult->getResultBarMaximum(), ucGraphics::PAR_RESULT.'='.$result));
		
		// AUSGABE GENERIEREN
		//zur Anzeige des Formulars wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucSurveyResult', 'char_result_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply('char_title', $charResult->getTitle());
		//Gespeicherten Output aller Subusecases einfügen
		$generator->apply('char_result', $charResult->getResultText());
		$generator->apply('rating_image', $ratingImage);
		$generator->apply('char_result_value',  $result);
		$generator->apply('char_icon', $smileyImage);
		
		return $generator->getHTML();	
 	}
 	
 	/**
 	 * Liefert den Ergebnistext für ein Merkmal
 	 * 
 	 * @param CharResult $charResult
 	 * @return String 
 	 */
 	private function showCharResultTextOnly(CharResult $charResult) {
		// RESULTAT ERMITTELN
		$smileyImage = $this->getUsecaseLink('graphics',ucGraphics::STEP_SMILEY, Array(ucGraphics::PAR_SMILEY_ASPECT.'='.$charResult->getSmileyAspect(), ucGraphics::PAR_BACKGROUND.'='.ucGraphics::BACKGROUND_YELLOW));
		
		// AUSGABE GENERIEREN
		//zur Anzeige des Formulars wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucSurveyResult', 'char_result_text_only_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply('char_title', $charResult->getTitle());
		//Gespeicherten Output aller Subusecases einfügen
		$generator->apply('char_result', $charResult->getResultText());
		$generator->apply('char_result_value',  $charResult->getResult());
		$generator->apply('char_icon', $smileyImage);
		
		return $generator->getHTML();	
 	}
}
?>
