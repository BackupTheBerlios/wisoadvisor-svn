<?php
/***********************************************************************************
 * WiSo@visor - Online Studienberatung der WiSo-Fakult�t
 * (c) 2006 Lehrstuhl f�r Wirtschaftsinformatik 3, Uni Erlangen-N�rnberg
 * R�ckfragen zu dieser Software: kompetenzmanagement@floooooo.de
 * 
 * ucSurvey stellt eine Umfrage (=Einzeltest) dar
 * und tr�gt die Antworten des Nutzers in die DB ein
 * 
 * nach Abschluss der Umfrage wird auf den UseCase zur Anzeige der Ergebnisse weitergeleitet
 *
 * Datei: uc_survey.php
 * Erstellt am: 06.05.2006
 * Erstellt von: Florian Strecker
 ***********************************************************************************/

class ucSurvey extends UseCase {

	const STEP_SURVEY = 'survey';
	const STEP_JUMP = 'jump';
	const STEP_COMPLETE = 'complete';
	const STEP_SHOWCLEAR = 'showclear';
	const STEP_CLEAR = 'clear';

	/**
	 * execute() ist die Implementierung der abstrakten execute-Methode der Elternklasse - sie enth�lt die Business-Logik eines UseCase
	 * @see parent::execute()
	 */
	public function execute()
	{
		try {
		
			//ein Parameter, der IMMER erwartet wird, ist SID = er enth�lt die SurveyID, also der Test, der angezeigt werden soll
			//ausserdem gibt es optional noch den "position"-Parameter
			//beide werden gelesen und im UseCase gespeichert
			$sid = (int) $this->getParam()->getParameter('sid');
			
			//jetzt generiere ein Survey-Objekt zur SID und lege es hier im UseCase ab
			$survey = Survey::getForId($this, $sid);
			if ($survey == null) 
				throw new MissingParameterException('Die Survey mit der ID='.$this->sid.' existiert nicht.');


//			if ($survey->isCompleted($this, $this->getSess()->getUId()))
//				throw new MissingParameterException($this->getConf()->getConfString('messages', 'surveynotagainallowed'));
		
			//der step()-Parameter legt fest, in welcher Phase der Umfrage sich der Nutzer befindet.
			//jede Funktion sollte den Output-Type selbst setzen!
			switch ($this->getStep())
			{
				case ucSurvey::STEP_SURVEY: //"survey": Umfrage l�uft, d.h. eine Frage wird gestellt; der Stand der Umfrage wird durch "position" bestimmt
					$this->handleSurvey($survey);
					break;
					
				case ucSurvey::STEP_JUMP: //"survey": Umfrage l�uft, Sprung zur�ck zu einer anderen Frage, ohne die alte auszuwerten
					$this->handleJump($survey);
					break;
				
				case ucSurvey::STEP_COMPLETE: //"complete": schlie�t die Umfrage ab, konsolidiert die Ergebnisse in der Datenbank und zeigt die Seite, die ggf. den Link auf den Auswertungsusecase enth�lt, an
					$this->handleComplete($survey);
					break;
				
				case ucSurvey::STEP_SHOWCLEAR: //"clear": verwirft die bisher get�tigten Eingaben des Nutzers und l�scht alle seine Eintr�ge f�r diese Umfrage
					$this->showClearPage($survey);
					break;
					
				case ucSurvey::STEP_CLEAR: //"clear": verwirft die bisher get�tigten Eingaben des Nutzers und l�scht alle seine Eintr�ge f�r diese Umfrage
					$this->handleClearSurvey($survey);
					break;
				
				default: //"start" (default): die Begr��ungsseite der Umfrage wird angezeigt
					$this->showStartpage($survey);
					break;
			}
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
	 * getBackButtonData() liefert das Datenobjekt f�r den zur�ck-Button
	 * @return HtmlFormGeneratorData f�r den Back-Button
	 */
	private function getBackButtonData()
	{
		$buttonData = new HtmlFormGeneratorData();
		$buttonData->setNameAndId('surveyor_back_button');
		$buttonData->setValue($this->getConf()->getConfString('ucSurvey', 'button', 'back'));
		$buttonData->setTabIndex('998');
		
		$onClick = '';
		//f�r onClick muss unterschieden werden, ob der Nutzer sich eben erst eingeloggt hat oder schon eingeloggt war, also direkt von der Infoseite kam
		//kam der Nutzer �ber den Login, existiert der Parameter 'infotype', den der ucInfo in dem Fall dem Login mitgegeben hat
		$infotype = $this->getParam()->getParameter('infotype');
		if ($infotype)
		{
			//hier muss der richtige Link auf ucInfo generiert werden:
			$idParam = ($infotype==ucInfo::STEP_PAGE) ? ucInfo::PARAMETER_SID : ucInfo::PARAMETER_BLID;
			$link = $this->getUsecaseLink('info', $infotype, Array($idParam.'='.$this->getParam()->getParameter('infoid')));
			$onClick = "document.location.href='".$link."';";
		}
		else
		{
			//dann einfach: eine Seite zur�ck
			$onClick = 'history.back();';
		}
		
		$buttonData->setOnClick($onClick);
		
		return $buttonData;
	}
	
	/**
 	 * showStartpage() zeigt die Startseite einer Survey an
	 */
	private function showStartpage(Survey $survey)
	{
		$attempt = $survey->getAttemptForUser($this, $this->getSess()->getUid()) + 1;		
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucSurvey', 'startpage_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		$formGen = new HtmlFormGenerator();
		
		// Navigation erstellen
		$navButtons = $formGen->getSubmitButton('surveyor_next_button', $this->getConf()->getConfString('ucSurvey', 'button', 'next') );

		$buttonData = $this->getBackButtonData();
		
		$backButton = $formGen->getButtonByDataObject($buttonData);
		$navButtons = $backButton.$navButtons;
		// Navigation-Template ausf�llen
		$navGen = new HtmlGenerator( $this->getConf()->getConfString('ucSurvey', 'navigation_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		$navGen->apply($this->getConf()->getConfString('ucSurvey', 'clearsurvey'), '');
		$navGen->apply('navigation', $navButtons);
		$navGen->apply('whysurvey', $this->getWhySurveyButton($survey));
		$navGen->apply('feedback', $this->getFeedbackButton($survey));
		if ($survey->isCompleted($this, $this->getSess()->getUId()))
			$navGen->apply('results', $this->getResultsButton($survey, $attempt));
		else
			$navGen->apply('results', '');
		
		//Template f�r Startpage ausf�llen
		$generator->apply($this->getConf()->getConfString('ucSurvey', 'surveytitle'), $survey->getTitle());
		$generator->apply($this->getConf()->getConfString('ucSurvey', 'startpage'), $survey->getStartpage());
		$generator->apply($this->getConf()->getConfString('ucSurvey', 'navigation'), $navGen->getHTML());
		
		//HTML in den Output schreiben... au�enherum kommt nat�rlich ein Formular, das dann alle n�tigen Daten enth�lt:
		$content = $formGen->getHiddenInput('step', 'survey');
		$content .= $formGen->getHiddenInput('sid', $survey->getId());
		$content .= $formGen->getHiddenInput('attempt', $attempt);
		$content .= $generator->getHTML();
		
		$this->appendOutput($formGen->getForm('start', $this->getOwnLink(), $content));
		$this->setOutputType(USECASE_HTML);
	}
	
	/**
	 * Pr�ft die letzte Antwort und stellt dann die n�chste Frage.
	 * Falls die letzte Antwort fehlt, wird die vorherige Frage wiederholt.
	 */
	private function handleSurvey(Survey $survey) {
		$questionBlock = null;
		$success = false; // Speichert, ob alle erforderlichen Antworten gegeben wurden. 
		$attempt = $sid = (int) $this->getParam()->getParameter('attempt');
		if ($attempt == null or $attempt == '') 
			$attempt = $survey->getAttemptForUser($this, $this->getSess()->getUid()) + 1;
		
		// Aktuellen FrageBlock ermitteln
		$qbid = (int) $this->getParam()->getParameter('qbid');
		if ($qbid != '' and $qbid != null) {
			
			$questionBlock = QuestionBlock::getForId($this, $qbid);
			if ($questionBlock == null) 
				throw new MissingParameterException('Der Fragenblock mit der ID='.$qbid.' existiert nicht.');
				
			// Antworten pr�fen 
			$hasErrors = $this->handleBlockAnswers($questionBlock, $attempt);
			
			if (!$hasErrors) { // Falls alle Antworten gegeben wurden -> n�chste Frage anzeigen
				$questionBlock = $questionBlock->getSuccessor($this);
				if ($questionBlock == null) // Letzte Frage schon beantwortet
					//$this->showCompletePage($survey);
					$this->handleComplete($survey);
				else
					$this->showQuestionBlock($questionBlock, $attempt);
			} else { // Antworten fehlen -> letzte Frage wiederholen
				$this->showQuestionBlock($questionBlock, $attempt, $hasErrors);
			}
			
		} else { // Ausnahme ERSTE FRAGE
		
			$questionBlock = QuestionBlock::getFirstForSurvey($this, $survey->getId());
			if ($questionBlock == null) 
				throw new MissingParameterException('Die Umfrage enth�lt keine Fragenbl�cke..');
			$this->showQuestionBlock($questionBlock, $attempt);
			
		}
		
	}
	
	/**
	 * Springt zum �bergebenen Fragenblock, ohne die Antworten auszuwerten.
	 */
	private function handleJump(Survey $survey) {
		$questionBlock = null;
		$attempt = $sid = (int) $this->getParam()->getParameter('attempt');
		if ($attempt == null or $attempt == '') 
			$attempt = $survey->getAttemptForUser($this, $this->getSess()->getUid()) + 1;
		
		// ZielFrageBlock ermitteln
		$qbid = (int) $this->getParam()->getParameter('qbid');
		if ($qbid != '' and $qbid != null) {
			
			$questionBlock = QuestionBlock::getForId($this, $qbid);
			if ($questionBlock == null) 
				throw new MissingParameterException('Der Fragenblock mit der ID='.$qbid.' existiert nicht.');
			$this->showQuestionBlock($questionBlock, $attempt);
			
		} else { // Keine Zielfrage -> erste anzeigen
		
			$questionBlock = QuestionBlock::getFirstForSurvey($this, $survey->getId());
			if ($questionBlock == null) 
				throw new MissingParameterException('Die Umfrage enth�lt keine Fragenbl�cke..');
			$this->showQuestionBlock($questionBlock, $attempt);
			
		}
		
	}
	
	/**
	 * Pr�ft die Antworten einen Frageblocks
	 * Leitet f�r jede Frage an handleQuestionAnswers weiter
	 */
	private function handleBlockAnswers(QuestionBlock $questionBlock, $attempt) {
		$questions = Question::getForQuestionBlock($this, $questionBlock->getId());
		$hasErrors = null;
		foreach ($questions as $question) {
			$result = $this->handleQuestionAnswers($question, $attempt);
			if ($question->isRequired() and !$result) // verpflichtende Frage nicht beantwortet
				//$success = false;
				$hasErrors[] = $question->getId(); //wenn die Frage nicht beantwortet wurde, wird ihre ID in das Array mit aufgenommen
		}
		return $hasErrors;
	}
	
	/**
	 * Pr�ft die �bergebenen Antworten.
	 */
	private function handleQuestionAnswers(Question $question, $attempt) {
		$result = false;
		// Evtl. vorher alte l�schen.
		$question->clearAnswers($this, $this->getSess()->getUid(), $attempt);
		
		switch ($question->getQuestionType($this)->getTitle())	{
			
			case Question::TYPE_MULTIPLE_CHOICE:
				//Vorgehen bei multiple Choice: f�r jede Checkbox pr�fen,
				//ob sie angehakt ist
				$answers = Answer::getForQuestion($this, $question->getId());
				foreach ($answers as $answer) {
					$fieldname = 'answer_'.$question->getId().'_'.$answer->getId();
					$field = $this->getParam()->getParameter($fieldname);
					if ($field != null and $field != '') { // Diese Antwort ausgew�hlt?
						$answer->storeForUser($this, $this->getSess()->getUid(), $attempt);
						$result = true;
					}
				}
				break;
			
			case Question::TYPE_SINGLE_CHOICE:
				$fieldname = 'answer_'.$question->getId();
				$field = $this->getParam()->getParameter($fieldname);
				$answers = Answer::getForQuestion($this, $question->getId());
				foreach ($answers as $answer) {
					if ($field == $answer->getId()) { // Diese Antwort ausgew�hlt?
						$answer->storeForUser($this, $this->getSess()->getUid(), $attempt);
						$result = true;
					}
				}
				break;
			case Question::TYPE_SINGLE_CHOICE_OTHER:
				$fieldname = 'answer_'.$question->getId();
				$field = $this->getParam()->getParameter($fieldname);
				$answers = Answer::getForQuestion($this, $question->getId());
				$lastAnswer = end($answers);
				$otherSelected = false;
				foreach ($answers as $answer) {
					if ($field == $answer->getId()) { // Diese Antwort ausgew�hlt?
						$answer->storeForUser($this, $this->getSess()->getUid(), $attempt);
						$result = true;
						// Letzte Antwort gew�hlt?
						if ($answer == $lastAnswer) {
							$otherSelected = true;
							$result = false;
						}
					}
				}
				$fieldname = 'answer_'.$question->getId().'_other';
				$field = $this->getParam()->getParameter($fieldname);
				// Sonstige eingegeben und ausgew�hlt ?
				if ($field <> '' and $otherSelected) $result = true;
				if ($field != null and $field != '') {
					$result = true;
					$question->storeOpenAnswer($this, $this->getSess()->getUid(), $field, $attempt);
				}
				break;
			
			case Question::TYPE_TESTED_INPUT:
				$fieldname = 'answer_'.$question->getId();
				$field = trim($this->getParam()->getParameter($fieldname));
				if ($field <> '') $result = true;
				$answers = Answer::getForQuestion($this, $question->getId());
				foreach ($answers as $answer) {
					if ($field == $answer->getAnswer()) { // Eingegebene Antwort stimmt mit Antwortm�glichkeit �berein?
						$answer->storeForUser($this, $this->getSess()->getUid(), $attempt);
					} 
				}
				break;
				
			case Question::TYPE_RESTRICTED_INPUT:
				$fieldname = 'answer_'.$question->getId();
				$field = trim($this->getParam()->getParameter($fieldname));
				$answers = Answer::getForQuestion($this, $question->getId());
				foreach ($answers as $answer) {
					if ($field == $answer->getAnswer()) { // Eingegebene Antwort stimmt mit Antwortm�glichkeit �berein?
						$answer->storeForUser($this, $this->getSess()->getUid(), $attempt);
						$result = true;
					} 
				}
				break;
			
			case Question::TYPE_OPEN_INPUT:
			case Question::TYPE_OPEN_TEXT:
				$fieldname = 'answer_'.$question->getId();
				$field = $this->getParam()->getParameter($fieldname);
				if ($field <> '') $result = true;
				if ($field != null and $field != '') {
					$result = true;
					$question->storeOpenAnswer($this, $this->getSess()->getUid(), $field, $attempt);
				}
				break;
		}
		return $result;
	}
	
	/**
	 * Stellt eine Frageseite dar, dir alle Fragen eines Frageblocks enth�lt.
	 */
	private function showQuestionBlock(QuestionBlock $questionBlock, $attempt, $answerErrors = Array()) {
		$questions = Question::getForQuestionBlock($this, $questionBlock->getId());
		$survey = $questionBlock->getSurvey($this);
		
		$generator = new HtmlGenerator( $questionBlock->getBlockTemplate(), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		$formGen = new HtmlFormGenerator();
		
		// Fragen darstellen
		$questionHTML = '';
		$firstQuestion = true;
		foreach ($questions as $question) {
			$questionHTML .= $this->showSingleQuestion($question, $attempt, $firstQuestion, $answerErrors);
			$firstQuestion = false;
		}
		
		// Navigation darstellen
		$navGen = new HtmlGenerator( $this->getConf()->getConfString('ucSurvey', 'navigation_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		
		$clearAllButton = $formGen->getSubmitButton('clearall', $this->getConf()->getConfString('ucSurvey', 'button', 'clearsurvey'), 
												"document.getElementById('step').value='showclear';", '999' ); 
												//dieser SubmitButton setzt vorher noch den step auf
												//'clear' - das geht, da der Post-Parameter zuerst gezogen wird
												//es wird ein sehr hoher TabIndex gew�hlt, damit der Button mit der Tastatur erst zuletzt erreicht wird
		
		$nextButton = $formGen->getSubmitButton('surveyor_next_button', $this->getConf()->getConfString('ucSurvey', 'button', 'next') );
		$navButtons = $nextButton;
		
		$jumpUrl = $this->getOwnLink('', Array('sid='.$questionBlock->getSid())); // Link zur Startseite der Survey
		if ($questionBlock <> null)
			$targetblock = $questionBlock->getPredecessor($this);
			if ($targetblock <> null)
				$jumpUrl = $this->getOwnLink(ucSurvey::STEP_JUMP, Array('qbid='.$targetblock->getId(), 'sid='.$questionBlock->getSid())); 
		
		$buttonData = new HtmlFormGeneratorData();
		$buttonData->setNameAndId('surveyor_back_button');
		$buttonData->setValue($this->getConf()->getConfString('ucSurvey', 'button', 'back'));
		$buttonData->setOnClick('javascript:parent.location.href=\''.$jumpUrl.'\';');
		$buttonData->setTabIndex('998');
		
		$backButton = $formGen->getButtonByDataObject($buttonData);
		$navButtons = $backButton.$navButtons;
		//Einf�gen in Navigation-Template
		$navGen->apply('navigation', $navButtons);
		$navGen->apply('whysurvey', $this->getWhySurveyButton($questionBlock));
		$navGen->apply('feedback', $this->getFeedbackButton($questionBlock));
		$navGen->apply('clearsurvey', $clearAllButton);
		$navGen->apply('results', '');
		
		
		//Einf�gen in QuestionBlock-Template
		$generator->apply($this->getConf()->getConfString('ucSurvey', 'surveytitle'), $survey->getTitle());
		$generator->apply($this->getConf()->getConfString('ucSurvey', 'quotation'), $questionBlock->getQuotation());
		$generator->apply($this->getConf()->getConfString('ucSurvey', 'percentage'), $this->getPercentage($questionBlock));
		$generator->apply('blocktitle', $questionBlock->getTitle());
		$generator->apply('questions', $questionHTML);
		$generator->apply($this->getConf()->getConfString('ucSurvey', 'navigation'), $navGen->getHTML());
		
		//Au�en rum noch ein Formular
		$content = $formGen->getHiddenInput('step', 'survey');
		$content .= $formGen->getHiddenInput('sid', $questionBlock->getSid());
		$content .= $formGen->getHiddenInput('attempt', $attempt);
		$content .= $formGen->getHiddenInput('qbid', $questionBlock->getId());
		$content .= $generator->getHTML();
		
		//Das Ganze zur�ckliefern
		$this->appendOutput($formGen->getForm('question', $this->getOwnLink(), $content));
		$this->setOutputType(USECASE_HTML);
		
	}
	
	/**
	 * Stellt eine einzelne Frage dar.
	 */
	private function showSingleQuestion(Question $question, $attempt, $firstQuestion, $answerErrors = Array()) {

		$generator = new HtmlGenerator( $question->getQuestionType($this)->getQuestionTemplate(), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		$formGen = new HtmlFormGenerator();
		
		//nachsehen, ob die Frage vorher einen AnswerError produziert hat:
		$answerError = '';
		if (in_array($question->getId(), $answerErrors)) $answerError = $this->getConf()->getConfString('ucSurvey', 'message', 'required');

	  	switch ($question->getQuestionType($this)->getTitle()) {
	  		case Question::TYPE_MULTIPLE_CHOICE:
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'question'), $question->getTitle());
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answererror'), $answerError);
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'questionhelp'), $this->getConf()->getConfString('ucSurvey', 'replace', 'multiplechoice'));
				
				$answerHTML = '';
				$answers = Answer::getForQuestion($this, $question->getId());
				foreach ($answers as $answer) {
					$fieldName = 'answer_'.$question->getId().'_'.$answer->getId();
					$selected = $answer->isSelectedForUser($this, $this->getSess()->getUid(), $attempt);
					$ansGen = new HtmlGenerator( $question->getQuestionType($this)->getAnswerTemplate(), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
					$data = new HtmlFormGeneratorData();
 		  		  	$data->setNameAndId((string) $fieldName);
 		  			$data->setValue((string) $answer->getId());
 		  			$data->setClass((string) 'wiso_box');
 		  			if ($selected )$data->setSelected('checked');
 		  	
					//$ansGen->apply('answerbutton', $formGen->getCheckbox($fieldName, $answer->getId(), 'wiso_box')); //TODO: evtl Klassenangabe in Config auslagern
					$ansGen->apply('answerbutton', $formGen->getCheckboxByDataObject($data));
					$ansGen->apply('answertext', $formGen->getLabeledText($fieldName, $answer->getAnswer()));
					$answerHTML .= $ansGen->getHTML();
				}
				$focusFirst = '';
				if ($firstQuestion)
					$focusFirst = '<script type="text/javascript">document.getElementById(\'answer_'.$question->getId().'_'.reset($answers)->getId().'\').focus();</script>';
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answers'), $answerHTML);
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answerjavascript'), $focusFirst);
	 			break;
	 		

			case Question::TYPE_SINGLE_CHOICE:
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'question'), $question->getTitle());
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answererror'), $answerError);
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'questionhelp'), '');

	 			$answerHTML = '';
				$answers = Answer::getForQuestion($this, $question->getId());
				foreach ($answers as $answer) {
					$fieldName = 'answer_'.$question->getId();
					$selected = $answer->isSelectedForUser($this, $this->getSess()->getUid(), $attempt);
					$fieldId = 'answer_'.$question->getId().'_'.$answer->getId();
					$ansGen = new HtmlGenerator( $question->getQuestionType($this)->getAnswerTemplate(), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
					$data = new HtmlFormGeneratorData();
 		  		  	$data->setName((string) $fieldName);
 		  			$data->setId((string) $fieldId);
 		  			$data->setValue((string) $answer->getId());
 		  			$data->setClass((string) 'wiso_box');
 		  			if ($selected )$data->setSelected('checked');
					//$ansGen->apply('answerbutton', $formGen->getRadio($fieldName, $fieldId, $answer->getId(), 'wiso_box'));
					$ansGen->apply('answerbutton', $formGen->getRadioByDataObject($data));
					$ansGen->apply('answertext', $formGen->getLabeledText($fieldId, $answer->getAnswer()));
					$answerHTML .= $ansGen->getHTML();
				}
				$firstAnswer = reset($answers);
				$lastAnswer = end($answers);
				$focusFirst = '';
				if ($firstQuestion)
					$focusFirst = '<script type="text/javascript">document.getElementById(\'answer_'.$question->getId().'_'.reset($answers)->getId().'\').focus();</script>';
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answers'), $answerHTML);
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answerjavascript'), $focusFirst);
				$generator->apply('leftanswer', $firstAnswer->getAnswer());
				$generator->apply('rightanswer', $lastAnswer->getAnswer());
				break;
				
			case Question::TYPE_SINGLE_CHOICE_OTHER:
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'question'), $question->getTitle());
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answererror'), $answerError);
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'questionhelp'), '');

	 			$answerHTML = '';
				$answers = Answer::getForQuestion($this, $question->getId());
				$firstAnswer = reset($answers);
				$lastAnswer = end($answers);
				foreach ($answers as $answer) {
					$fieldName = 'answer_'.$question->getId();
					$selected = $answer->isSelectedForUser($this, $this->getSess()->getUid(), $attempt);
					$fieldId = 'answer_'.$question->getId().'_'.$answer->getId();
					$ansGen = new HtmlGenerator( $question->getQuestionType($this)->getAnswerTemplate(), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
					$data = new HtmlFormGeneratorData();
 		  		  	$data->setName((string) $fieldName);
 		  			$data->setId((string) $fieldId);
 		  			$data->setValue((string) $answer->getId());
 		  			$data->setClass((string) 'wiso_box');
 		  			if ($selected )$data->setSelected('checked');
					//$ansGen->apply('answerbutton', $formGen->getRadio($fieldName, $fieldId, $answer->getId(), 'wiso_box'));
					$ansGen->apply('answerbutton', $formGen->getRadioByDataObject($data));
					$answertext = $formGen->getLabeledText($fieldId, $answer->getAnswer());
					// Letzte Antwort erh�lt zus�tzlich Textfeld
					if ($answer == $lastAnswer) {
						$textField = $formGen->getInput($fieldName.'_other', $question->getOpenAnswer($this, $this->getSess()->getUid(), $attempt));
						$answertext .= '&nbsp;'.$textField;
					}
					$ansGen->apply('answertext', $answertext);
					$answerHTML .= $ansGen->getHTML();
				}
				$focusFirst = '';
				if ($firstQuestion)
					$focusFirst = '<script type="text/javascript">document.getElementById(\'answer_'.$question->getId().'_'.reset($answers)->getId().'\').focus();</script>';
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answers'), $answerHTML);
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answerjavascript'), $focusFirst);
				$generator->apply('leftanswer', $firstAnswer->getAnswer());
				$generator->apply('rightanswer', $lastAnswer->getAnswer());
				break;
			
			case Question::TYPE_TESTED_INPUT:
			case Question::TYPE_RESTRICTED_INPUT:
				$fieldName = 'answer_'.$question->getId();
				$returnValue = $formGen->getInput($fieldName);
				
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'question'), $question->getTitle());
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answererror'), $answerError);
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'questionhelp'), '');
		
	 			$answerHTML = $formGen->getInput($fieldName);
	 			$focusFirst = '';
				if ($firstQuestion)
					$focusFirst = '<script type="text/javascript">document.getElementById(\'answer_'.$question->getId().'\').focus();</script>';
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answerjavascript'), $focusFirst);
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answers'), $answerHTML);
				break;
			
			case Question::TYPE_OPEN_INPUT:
				$fieldName = 'answer_'.$question->getId();
				$value = $question->getOpenAnswer($this, $this->getSess()->getUid(), $attempt);
				
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'question'), $question->getTitle());
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answererror'), $answerError);
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'questionhelp'), '');

	 			$answerHTML = $formGen->getInput($fieldName, $value);
	 			$focusFirst = '';
				if ($firstQuestion)
					$focusFirst = '<script type="text/javascript">document.getElementById(\'answer_'.$question->getId().'\').focus();</script>';
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answers'), $answerHTML);
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answerjavascript'), $focusFirst);
				break;
				
			case Question::TYPE_OPEN_TEXT:
				$fieldName = 'answer_'.$question->getId();
				$value = $question->getOpenAnswer($this, $this->getSess()->getUid(), $attempt);
				
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'question'), $question->getTitle());
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answererror'), $answerError);
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'questionhelp'), '');

	 			$answerHTML = $formGen->getTextArea($fieldName, $value);
	 			$focusFirst = '';
				if ($firstQuestion)
					$focusFirst = '<script type="text/javascript">document.getElementById(\'answer_'.$question->getId().'\').focus();</script>';
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answerjavascript'), $focusFirst);
				$generator->apply($this->getConf()->getConfString('ucSurvey', 'answers'), $answerHTML);
				break;
	  	}
	  	return $generator->getHTML();
	}
	
	/**
	 * getWhySurveyButton() liefert zur aktuellen Survey und Frage den "Warum muss ich das machen?" Button
	 * @param $questionObject entweder vom Typ Survey oder QuestionBlock
	 * @return den WhySurveyButton als String
	 */
	private function getWhySurveyButton($questionObject)
	{
 		$step = '';
 		$paramname = '';
 		$id = '';
 		//der Typ kann survey oder questionBlock sein:
 		if ($questionObject instanceof Survey)
 		{
 			$step = ucSurveyHelp::STEP_SURVEY_HELP;
 			$paramname = ucSurveyHelp::PARAMETER_SID;
 			$id = $questionObject->getId();
 		}
 		else if ($questionObject instanceof QuestionBlock)
 		{
 			$step = ucSurveyHelp::STEP_QUESTIONBLOCK_HELP;
 			$paramname = ucSurveyHelp::PARAMETER_QUBL;
 			$id = $questionObject->getId();
 		}
 		
 		$buttonData = new HtmlFormGeneratorData();
		$buttonData->setNameAndId('whysurvey');
		$buttonData->setValue($this->getConf()->getConfString('ucSurvey', 'button', 'whysurvey'));
		$buttonData->setOnClick("window.open('".$this->getUsecaseLink('surveyhelp', $step, Array($paramname.'='.$id))."', 'WiSoAdvisorPopup', 'dependent=yes,height=600,width=600,locationbar=no,toolbar=no,menubar=no,directories=no,scrollbars=yes,resizable=yes,status=no,screenX=100,screenY=100');return false;");
		$buttonData->setTabIndex('997');

		$formGen = new HtmlFormGenerator();		
		return $formGen->getButtonByDataObject($buttonData);
	}
	
	/**
	 * getFeedbackButton() liefert zur aktuellen Survey und Frage den Feedback-Button
	 * @param $questionObject entweder vom Typ Survey oder QuestionBlock
	 * @return den FeedbackButton als String
	 */
	private function getFeedbackButton($questionObject)
	{
 		$value = '';
 		//der Typ kann survey oder questionBlock sein:
 		if ($questionObject instanceof Survey)
 		{
 			$value='Test: '.$questionObject->getTitle().'. URL: '.$_SERVER['QUERY_STRING'];
 		}
 		else if ($questionObject instanceof QuestionBlock)
 		{
 			$value='Test: '.$questionObject->getSurvey($this)->getTitle().', Frage: '.$questionObject->getPosition().'. URL: '.$_SERVER['QUERY_STRING'];
 		}
			
		$buttonData = new HtmlFormGeneratorData();
		$buttonData->setNameAndId('feedbackbutton');
		$buttonData->setValue($this->getConf()->getConfString('ucSurvey', 'button', 'feedback'));
		$buttonData->setOnClick("window.open('".$this->getUsecaseLink('feedback', '', Array(ucFeedback::PARAMETER_REFERENCE.'='.urlencode($value)))."', 'WiSoAdvisorPopup', 'dependent=yes,height=600,width=600,locationbar=no,toolbar=no,menubar=no,directories=no,scrollbars=yes,resizable=yes,status=no,screenX=100,screenY=100');");
		$buttonData->setTabIndex('996');

		$formGen = new HtmlFormGenerator();		
		return $formGen->getButtonByDataObject($buttonData);
	}
	
	
	/**
	 * getResultsButton() liefert zur aktuellen Survey einen Button, der zum letzten Ergebnis springt
	 * @param Survey $survey
	 * @param int $attempt
	 * @return den Ergebnis-Button als String
	 */
	private function getResultsButton(Survey $survey, $attempt)
	{
 		$attempt--; // hier ist der letzte vollst�ndige Attempt gemeint, nicht der aktuelle (unvollst�ndige)			
 		$formGen = new HtmlFormGenerator();
 		return $formGen->getButton('resultsbutton', 'Ergebnisse', 
			'javascript:parent.location.href=\''.$this->getUsecaseLink('survey_result', '', Array(ucSurveyResult::PARAMETER_SURVEY.'='.$survey->getId(), ucSurveyResult::PARAMETER_ATTEMPT.'='.$attempt)).'\';');
	}
	
//	/**
//	 * getNavigationPanel() liefert das "Navigationspanel" am Ende einer Frageseite
//	 * @param $clearAll boolean: wenn false, wird KEIN Umfrage verwerfen Button angezeigt; Standard: true
//	 * @param $completeAndNotNext boolean: wenn true, dann wird statt dem "next"-Button der "abschlie�en"-Button benutzt; Standard: false
//	 * @param $questionBlock: (optional) Der QuestionBlock, in dem man gerade ist. Wird f�r die Navigation genutzt. 
//	 * @return das Navigationspanel als String (HTML-Fragment)
//	 */
//	private function getNavigationPanel($clearAll, $completeAndNotNext, QuestionBlock $questionBlock = null)
//	{
//		$formGen = new HtmlFormGenerator();
//		
//		$clearAllButton = ''; //wenn kein ClearAll gew�nscht, muss das Template mit "leer" gef�llt werden
//		if ($clearAll) $clearAllButton = $formGen->getSubmitButton('clearall', 
//												$this->getConf()->getConfString('ucSurvey', 'button', 'clearsurvey'), 
//												"document.getElementById('step').value='showclear';", 
//												'999' ); 
//												//dieser SubmitButton setzt vorher noch den step auf
//												//'clear' - das geht, da der Post-Parameter zuerst gezogen wird
//												//es wird ein sehr hoher TabIndex gew�hlt, damit der Button mit der Tastatur erst zuletzt erreicht wird
//		//jetzt noch der "normale" weiter-Button: (oder der "abschlie�en"-Button - je nachdem...)
//		$buttonText = 'next';
//		if ($completeAndNotNext) $buttonText = 'savesurvey';
//		
//		$nextButton = $formGen->getSubmitButton('next', $this->getConf()->getConfString('ucSurvey', 'button', $buttonText) );
//		$navButtons = $nextButton;
//		if ($questionBlock <> null) {
//			$targetblock = $questionBlock->getPredecessor($this);
//			if ($targetblock <> null) {
//				$jumpUrl = $this->getOwnLink(ucSurvey::STEP_JUMP, Array('qbid='.$targetblock->getId(), 'sid='.$questionBlock->getSid())); 
//				
//				$buttonData = new HtmlFormGeneratorData();
//				$buttonData->setNameAndId('back');
//				$buttonData->setValue($this->getConf()->getConfString('ucSurvey', 'button', 'back'));
//				$buttonData->setOnClick('javascript:parent.location.href=\''.$jumpUrl.'\';');
//				$buttonData->setTabIndex('998');
//				
//				$backButton = $formGen->getButtonByDataObject($buttonData);
//				$navButtons = $backButton.$navButtons;
//			}
//		}
//		
//		//nun ein HTML-Generator, der mit den Buttons bef�llt wird:
//		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucSurvey', 'navigation_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
//		//bef�lle den Generator mit den zu ersetzenden Anteilen...
//		$generator->apply($this->getConf()->getConfString('ucSurvey', 'clearsurvey'), $clearAllButton);
//		$generator->apply($this->getConf()->getConfString('ucSurvey', 'navbuttons'), $navButtons);
//		
//		return $generator->getHTML();
//	}
	
	
	/**
	 * getPercentage() gibt die Fortschrittsanzeige zur�ck
	 * @param $showFull wenn dieser Parameter angegeben wird, wird nicht Frage x von y, sondern 'vollst�ndig' unter dem Balken angezeigt
	 * @return die Fortschrittsanzeige (HTML-Fragment) als String
	 */
	private function getPercentage(QuestionBlock $questionBlock = null, $showFull = false)
	{
	 	//TODO: evtl. in Template auslagern?
	 	if ($showFull) {
	 		$imgLink = $this->getUsecaseLink($this->getConf()->getConfString('ucSurvey', 'graphics', 'usecase'), $this->getConf()->getConfString('ucSurvey', 'graphics', 'step'), Array(ucGraphics::PAR_PERCENTAGE.'=100'));
	 		return '<img src="'.$imgLink.'"> vollst�ndig';
	 	}
	 	
	 	$maxQuestion = count(QuestionBlock::getForSurvey($this, $questionBlock->getSurvey($this)->getId()));
	 	$actualPos = $questionBlock->getPosition();
	 	
	 	$percentage = round(($actualPos-1) / $maxQuestion * 100);

	 	//link auf Grafik erzeugen:
	 	$imgLink = $this->getUsecaseLink($this->getConf()->getConfString('ucSurvey', 'graphics', 'usecase'), $this->getConf()->getConfString('ucSurvey', 'graphics', 'step'), Array(ucGraphics::PAR_PERCENTAGE.'='.$percentage));
	 	$returnString = '<img src="'.$imgLink.'"> Frage '.$actualPos.' von '.$maxQuestion;
	 	
	 	// Ausnahme: Frage 99 ist die Konzentrationsfrage
	 	if ($actualPos == 99) 
	 		$returnString = '<img src="'.$imgLink.'"> Konzentrationsfrage';
	 	
	 	return $returnString;
	}

	
	  
	/**
	 * completeSurvey() schlie�t die Umfrage ab und leitet auf den UseCase zur Ergebnisberechnung weiter
	 * @return true, wenn alles geklappt hat
	 */
	private function showCompletePage(Survey $survey)
	{
		
		$attempt = $sid = (int) $this->getParam()->getParameter('attempt');
		if ($attempt == null or $attempt == '') 
			throw new MissingParameterException('Es wurde kein Attempt �bergeben.');
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucSurvey', 'complete_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		$formGen = new HtmlFormGenerator();
		
		// Navigation erstellen
		$navButtons = $formGen->getSubmitButton('surveyor_next_button', $this->getConf()->getConfString('ucSurvey', 'button', 'savesurvey') );
		// Navigation-Template ausf�llen
		$navGen = new HtmlGenerator( $this->getConf()->getConfString('ucSurvey', 'navigation_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		$navGen->apply('clearsurvey', '');
		$navGen->apply('navigation', $navButtons);
		$navGen->apply('whysurvey', $this->getWhySurveyButton($survey));
		$navGen->apply('feedback', $this->getFeedbackButton($survey));
		$navGen->apply('results', '');
		
		//Template f�r Startpage ausf�llen
		$generator->apply('surveytitle', $survey->getTitle());
		$generator->apply('endpage', $survey->getEndpage());
		$generator->apply('navigationpanel', $navGen->getHTML());
		$generator->apply('percentage', $this->getPercentage(null, true));
		
		//HTML in den Output schreiben... au�enherum kommt nat�rlich ein Formular, das dann alle n�tigen Daten enth�lt:
		$content = $formGen->getHiddenInput('step', 'complete');
		$content .= $formGen->getHiddenInput('complete', 'yes');
		$content .= $formGen->getHiddenInput('sid', $survey->getId());
		$content .= $formGen->getHiddenInput('attempt', $attempt);
		$content .= $generator->getHTML();
		
		$this->appendOutput($formGen->getForm('complete_form', $this->getOwnLink(), $content));
		$this->setOutputType(USECASE_HTML);
	}

	/**
	 * handleComplete() schlie�t die Survey ab und leitet auf die Auswertung weiter
	 * @return true, wenn alles ok war, sonst false
	 */
	private function handleComplete(Survey $survey)
	{
	 	//prinzipiell sind zu diesem Zeitpunkt alle Antworten in der DB eingetragen
	 	//also muss nur noch eingetragen werden, dass der User JETZT die Umfrage abgeschlossen hat:
	 	$attempt = $sid = (int) $this->getParam()->getParameter('attempt');
		if ($attempt == null or $attempt == '') 
			throw new MissingParameterException('Es wurde kein Attempt �bergeben.');
	 	$maxAttempt = $survey->getAttemptForUser($this, $this->getSess()->getUid());
	 	// Nur speichern, wenn nicht der Zur�ck-Button genutzt wurde
	 	if ($attempt > $maxAttempt)
	 		$survey->storeComplete($this, $this->getSess()->getUid(), $attempt);
		header('location:'.$this->getUsecaseLink($this->getConf()->getConfString('ucSurvey', 'result_target_uc'), '', Array(ucSurveyResult::PARAMETER_SURVEY.'='.$survey->getId(), ucSurveyResult::PARAMETER_ATTEMPT.'='.$attempt)));
		$this->setOutputType(USECASE_NOTYPE);
	}

	/**
	 * clearSurvey() verwirft alle Antworten und leitet auf das angegebene Ziel weiter
	 * @return true, wenn alles geklappt hat
	 */
	private function showClearPage(Survey $survey)
	{
		
	  	//wir benutzen zum Anzeigen den HTML-Generator und das entsprechende Template
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucSurvey', 'clear_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		$formGen = new HtmlFormGenerator();

		$clearButton = $formGen->getSubmitButton('clearall', 
   									$this->getConf()->getConfString('ucSurvey', 'button', 'clearsurvey')
   									 );
   		//der BackButton muss ENTWEDER auf den step=survey oder den step=completed verweisen!
   		//das kann festgestellt werden, da vom completed-step immer der Parameter complete=yes mitgegeben wird
   		$nextStep = 'survey';
   		if ($this->getParam()->getParameter('complete')) $nextStep = 'complete';
   		
   		$backButton = $formGen->getSubmitButton('back', 
   									$this->getConf()->getConfString('ucSurvey', 'button', 'back'), 
   									"document.getElementById('step').value='".$nextStep."';" );
		
		//bef�lle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucSurvey', 'surveytitle'), $survey->getTitle());
		$generator->apply($this->getConf()->getConfString('ucSurvey', 'clearsurvey'), $clearButton);
		$generator->apply($this->getConf()->getConfString('ucSurvey', 'navbuttons'), $backButton);

		//HTML in den Output schreiben... au�enherum kommt nat�rlich ein Formular, das dann alle n�tigen Daten enth�lt:
		$content = $formGen->getHiddenInput('step', 'clear');
		$content .= $formGen->getHiddenInput('clear', 'yes');
		$content .= $formGen->getHiddenInput('sid', $survey->getId());
		$content .= $generator->getHTML();
		
		$this->appendOutput($formGen->getForm('clear_form', $this->getOwnLink(), $content));
		$this->setOutputType(USECASE_HTML);
	}

	/**
	 * handleClearSurvey() verwirft die Survey und leitet weiter
	 * @return true, wenn alles ok war, sonst false
	 */
	private function handleClearSurvey(Survey $survey)
	{
	 	//prinzipiell sind zu diesem Zeitpunkt alle Antworten in der DB eingetragen
	 	//also m�ssen diese gel�scht werden:
	 	$attempt = $survey->getAttemptForUser($this, $this->getSess()->getUid()) + 1;
		$survey->clearAnswers($this, $this->getSess()->getUid(), $attempt);
		header('location:'.$this->getUsecaseLink($this->getConf()->getConfString('ucSurvey', 'clear_target_uc')));
		$this->setOutputType(USECASE_NOTYPE);
	}

}
?>
