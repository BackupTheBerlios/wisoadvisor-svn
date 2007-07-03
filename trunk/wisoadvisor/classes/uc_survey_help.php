<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: uc_survey_help.php
 * $Revision: 1.4 $
 * Erstellt am: 17.05.2006
 * Erstellt von: Florian Strecker
 ***********************************************************************************/


class ucSurveyHelp extends UseCase {
	
	const STEP_SURVEY_HELP = 'survey';
	const STEP_QUESTIONBLOCK_HELP = 'questionblock';
	
	const PARAMETER_SID = 'sid';
	const PARAMETER_QUBL = 'qbid';
	
	//Ausführung: Business-Logik
	public function execute() {
		try {
			//in $helpText wird die Hilfeseite gespeichert, in $title der "Titel" der Hilfeseite
			$helpText = '';
			$title = '';
			
			switch ($this->getStep())
			{
				case ucSurveyHelp::STEP_SURVEY_HELP:
					//"Umfrageebene" (allgemeine Hilfe)
					$sid = $this->getParam()->getParameter(ucSurveyHelp::PARAMETER_SID);
					$survey = Survey::getForId($this, $sid);
					if ($survey == null)
						throw new MissingParameterException('Der Parameter ist ungültig, Seite existiert nicht.');
					
					$helpText = $survey->getWhypage();
					$title = $survey->getTitle();
					
					break;
				
				case ucSurveyHelp::STEP_QUESTIONBLOCK_HELP:
					//"Blockebene" (spezifische Hilfe)
					$qbid = $this->getParam()->getParameter(ucSurveyHelp::PARAMETER_QUBL);
					$questionBlock = QuestionBlock::getForId($this, $qbid);
					if ($questionBlock == null)
						throw new MissingParameterException('Der Parameter ist ungültig, Seite existiert nicht.');
					
					$helpText = $questionBlock->getHelpText();
					$title = $questionBlock->getSurvey($this)->getTitle();
					
					break;
					
				default:
					throw new MissingParameterException('Ein Parameter fehlt.');
			}

			//Zur Anzeige wird der HTML-Generator benutzt
			$generator = new HtmlGenerator( $this->getConf()->getConfString('ucSurveyHelp', 'template'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
			//befülle den Generator mit den zu ersetzenden Anteilen...
			$generator->apply('content', $helpText);
			$generator->apply('title', $title);

			$this->appendOutput($generator->getHTML());
			
			//wir erzeugen ein Popup-Fenster:
			$this->setTemplateName('popup');
			$this->setOutputType(USECASE_HTML);
			return true;	
					
		} catch (ModelException $e) {
 			$this->setError('Bei der Verarbeitung ist ein Fehler aufgetreten.<br/>'.$e->getMessage());
 			return false;
 		}  catch (MissingParameterException $e) {
 			$this->setError($e->getMessage());
 			return false;
		}
	}
}
?>