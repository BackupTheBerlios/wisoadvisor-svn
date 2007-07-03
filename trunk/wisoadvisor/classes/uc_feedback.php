<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: uc_feedback.php
 * $Revision: 1.4 $
 * Erstellt am: 06.06.2006
 * Erstellt von: Florian Strecker
 ***********************************************************************************/


class ucFeedback extends UseCase {
	
	const STEP_CHECK_FEEDBACK = 'check';
	
	const PARAMETER_REFERENCE = 'reference';
	const PARAMETER_SUBJECT = 'subject';
	const PARAMETER_MESSAGE = 'message';
	const PARAMETER_EMAIL = 'email';
	const PARAMETER_SENDER = 'sender';
	
	//Ausführung: Business-Logik
	public function execute() {
			
			switch ($this->getStep())
			{
				case ucFeedback::STEP_CHECK_FEEDBACK:
					//der Feedbackbogen muss überprüft werden:
					$this->checkFeedback();
					break;
					
				default:
					$this->showFeedbackForm(urldecode($this->getParam()->getParameter(ucFeedback::PARAMETER_REFERENCE)));
			}

			//wir erzeugen ein Popup-Fenster:
			$this->setTemplateName('popup');
			$this->setOutputType(USECASE_HTML);
			return true;	
	}

	/**
	 * checkFeedback() überprüft die Eingaben und generiert ggf. Mail und DB-Eintrag, oder zeigt Fehler an
	 */
	private function checkFeedback()
	{
		//zuerst die übergebenen Parameter einlesen:
		$email = $this->getParam()->getParameter(ucFeedback::PARAMETER_EMAIL);
		$sendername = $this->getParam()->getParameter(ucFeedback::PARAMETER_SENDER);
		$message = $this->getParam()->getParameter(ucFeedback::PARAMETER_MESSAGE);
		$subject = $this->getParam()->getParameter(ucFeedback::PARAMETER_SUBJECT);
		$reference = $this->getParam()->getParameter(ucFeedback::PARAMETER_REFERENCE);
		
		//in $ error werden evtl. Fehlermeldungen gesammelt.
		$error = '';
		
		//zuerst checken: ist die eMailadresse gültig?
  		if ((!preg_match($this->getConf()->getConfString('ucFeedback', 'regex', 'email'), $email)) && ($email!='')) $error .= $this->getConf()->getConfString('ucFeedback', 'error', 'email').'<br/>';
		//ist der angegebene Name ok?
  		if (!preg_match($this->getConf()->getConfString('ucFeedback', 'regex', 'sender'), $sendername)) $error .= $this->getConf()->getConfString('ucFeedback', 'error', 'sender').'<br/>';
		//ist der Betreff ok?
  		if (!preg_match($this->getConf()->getConfString('ucFeedback', 'regex', 'subject'), $subject)) $error .= $this->getConf()->getConfString('ucFeedback', 'error', 'subject').'<br/>';
		//ist die Nachricht ok?
  		if (!preg_match($this->getConf()->getConfString('ucFeedback', 'regex', 'message'), $message)) $error .= $this->getConf()->getConfString('ucFeedback', 'error', 'message').'<br/>';
		//ist die Reference ok? - da wird kein Fehler angezeigt, sondern die Reference umgesetzt (da interner Parameter)
  		if (!preg_match($this->getConf()->getConfString('ucFeedback', 'regex', 'reference'), $reference)) $reference = 'NO REFERENCE AVAILABLE';
		
		//wenn $error nicht leer ist, werden die Fehler angezeigt
		if ($error != '')
		{
			$this->showFeedbackForm($reference, $email, $sendername, $subject, $message, $error);
		}
		else
		{
			//sodala: Dann mal los:	
			$uid = $this->getSess()->getUid();
			if (!$uid) $uid = 0; //Bugfix, weil im SQL-Statement unbedingt eine uid und nicht "null" übergeben werden muss

			//Daten in Datenbank ablegen...
			$result = $this->getDb()->preparedQuery($this->getConf()->getConfString('sql', 'feedback', 'saveInDb'), Array($subject, $reference, $email, $sendername, $uid, $message));		
			//wenn $result false ist, hat's nicht geklappt - schade!
			//wurde aber zumindest die eMail verschickt?
			$sentMail = $this->sendFeedbackMail($email, $sendername, $subject, $reference, $message);
			if ((!$result) || (!$sentMail))
			{
				$sendError = $this->getConf()->getConfString('ucFeedback', 'error', 'senderror').'<br/>';
				$this->showFeedbackForm($reference, $email, $sendername, $subject, $message, $sendError);
			}
			else
			{
				//Bestätigungsseite anzeigen
				$generator = new HtmlGenerator( $this->getConf()->getConfString('ucFeedback', 'confirmation_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
				$generator->apply($this->getConf()->getConfString('ucFeedback', 'emailaddy'), $email);
				$this->appendOutput($generator->getHTML());
			}
		}
	}

	/**
	 * showFeedbackForm() zeigt das Feedback-Formular, ggf. mit Fehlermeldungen an
	 * @param $reference interne Referenz (wenn bekannt)
	 * @param $email eMailadresse, wenn bereits bekannt
	 * @param $sendername Name, wenn bereits bekannt
	 * @param $subject Betreff, wenn bereits bekannt
	 * @param $message die Nachricht
	 * @param $errorMessage eine Fehlermeldung als String
	 * @return das entsprechende HTML-Fragment (direkt im UseCase-Output)
	 */
	private function showFeedbackForm($reference = '', $email = '', $sendername = '', $subject = '', $message = '', $errorMessage = '')
	{
		//wenn keine eMailadresse übergeben wird, nehmen wir die aus den Userdaten (wenn vorhanden):
		if ((!$email) || ($email=='')) $email = $this->getSess()->getUserData('email');
		//wenn kein Name übergeben wird, nehmen wir den aus den Userdaten (wenn vorhanden):
		if ((!$sendername) || ($sendername=='')) $sendername = $this->getSess()->getUserData('username');
		
		//zur Anzeige des Formulars wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucFeedback', 'feedbackform_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'showerror'), $errorMessage);
		
		//für alles, was ein Formularelement ist, wird der Formgenerator benutzt:
		$formGen = new HtmlFormGenerator();
		
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'emailaddy'), $formGen->getInput(ucFeedback::PARAMETER_EMAIL, $email));
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'sender'), $formGen->getInput(ucFeedback::PARAMETER_SENDER, $sendername));
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'subject'), $formGen->getInput(ucFeedback::PARAMETER_SUBJECT, $subject));
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'message'), $formGen->getTextArea(ucFeedback::PARAMETER_MESSAGE, $message));
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'reference'), $formGen->getHiddenInput(ucFeedback::PARAMETER_REFERENCE, $reference));
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'reset'), $formGen->getResetButton('resetbutton', 'Zurücksetzen'));
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'submit'), $formGen->getSubmitButton('surveyor_next_button', 'Absenden'));
		
		//..und um die Formularelemente kommt noch ein Formular:
		$output = $formGen->getForm('feedbackform', $this->getOwnLink(ucFeedback::STEP_CHECK_FEEDBACK), $generator->getHTML());
			
		//HTML in den Output schreiben...
		$this->appendOutput($output);
	}

	/**
	 * sendFeedbackMail() generiert eine eMail mit den Feedback-Daten
	 * @param $email eMailadresse des Empfängers
	 * @param $sendername Name des Empfängers
	 * @param $subject Betreff
	 * @param $reference interne Referenz (=2. Subject)
	 * @param $message Nachricht
	 * @return true, wenn alles geklappt hat, sonst false
	 */
	private function sendFeedbackMail($email, $sendername, $subject, $reference, $message)
	{
		//zum Erstellen des Mailbodys kann wieder der "HTML-Generator" benutzt werden (ist eigentlich ein "Template-Generator"...)
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucFeedback', 'email', 'template'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'emailaddy'), $email);
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'sender'), $sendername);
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'subject'), $subject);
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'message'), $message);
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'reference'), $reference);
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'userid'), $this->getSess()->getUid());
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'username'), $this->getSess()->getUserData('username'));
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'useremail'), $this->getSess()->getUserData('email'));
		//damit können alle Teile der eMail erzeugt werden:
		$body = $generator->getHTML();
		$subject = $this->getConf()->getConfString('ucFeedback', 'email', 'subject').$subject;
		$header = 'From: '.$this->getConf()->getConfString('ucFeedback', 'email', 'sender')."\nReply-To: ".$this->getConf()->getConfString('ucFeedback', 'email', 'replyto')."\n";
		$receiver = $this->getConf()->getConfString('ucFeedback', 'email', 'receiver');
		
		//eMail verschicken:
		return mail($receiver, $subject, $body, $header);
	}



}
?>