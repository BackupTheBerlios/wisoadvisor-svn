<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: uc_forgot_pwd.php
 * $Revision: 1.1 $
 * Erstellt am: 06.06.2006
 * Erstellt von: Florian Strecker
 ***********************************************************************************/


class ucForgotPwd extends UseCase {
	
	const STEP_CHECK = 'check';
	
	const PARAMETER_EMAIL = 'email';
	
	//Ausführung: Business-Logik
	public function execute() {
			
			switch ($this->getStep())
			{
				case ucForgotPwd::STEP_CHECK:
					//die eingegebene eMailadresse muss überprüft werden:
					$this->checkEmailAddy();
					break;
					
				default:
					$this->showPasswordForm();
			}

			$this->setOutputType(USECASE_HTML);
			return true;	
	}

	/**
	 * checkEmailAddy() überprüft die eingegebene eMailadresse und generiert ggf. Mail mit PWD und Username
	 */
	private function checkEmailAddy()
	{
		//zuerst die übergebenen Parameter einlesen:
		$email = $this->getParam()->getParameter(ucForgotPwd::PARAMETER_EMAIL);
		
		//zuerst checken: ist die eMailadresse gültig?
  		if (!preg_match($this->getConf()->getConfString('ucForgotPwd', 'regex', 'email'), $email)) $this->showPasswordForm($email, $this->getConf()->getConfString('ucForgotPwd', 'error', 'email').'<br/>');
		else
		{
			//dann doch gucken, ob die Adresse auch in der DB steht: wenn ja, wird eine $uid geliefert
			$result = $this->getDb()->fetchPreparedRecord( $this->getConf()->getConfString('sql', 'user', 'validEmail'), Array($email) );
			if ((!$result) || ($result['uid']=='')) $this->showPasswordForm($email, $this->getConf()->getConfString('ucForgotPwd', 'error', 'nosuchemail').'<br/>');
			else
			{
				//ok: eMailadresse gültig und in der Datenbank vorhanden:
				//also zuerst versuchen, die eMail zu verschicken
				if (!$this->sendPasswordMail($result['uid'])) $this->showPasswordForm($email, $this->getConf()->getConfString('ucForgotPwd', 'error', 'emailerror').'<br/>');
				else
				{
					//ansonsten: Erfolg anzeigen
					$generator = new HtmlGenerator( $this->getConf()->getConfString('ucForgotPwd', 'confirmation_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
					$generator->apply($this->getConf()->getConfString('ucForgotPwd', 'emailaddy'), $email);
					
					$this->appendOutput($generator->getHTML());
				}
			}
		}
	}

	/**
	 * showPasswordForm() zeigt das Passwort-Anforderungs-Formular, ggf. mit Fehlermeldungen an
	 * @param $email eMailadresse, wenn bereits bekannt
	 * @param $errorMessage eine Fehlermeldung als String
	 * @return das entsprechende HTML-Fragment (direkt im UseCase-Output)
	 */
	private function showPasswordForm($email = '', $errorMessage = '')
	{
		//wenn keine eMailadresse übergeben wird, nehmen wir die aus den Userdaten (wenn vorhanden):
		if ((!$email) || ($email=='')) $email = $this->getSess()->getUserData('email');
		
		//zur Anzeige des Formulars wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucForgotPwd', 'pwdform_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucForgotPwd', 'showerror'), $errorMessage);
		
		//für alles, was ein Formularelement ist, wird der Formgenerator benutzt:
		$formGen = new HtmlFormGenerator();
		
		$generator->apply($this->getConf()->getConfString('ucForgotPwd', 'emailaddy'), $formGen->getInput(ucForgotPwd::PARAMETER_EMAIL, $email));
		$generator->apply($this->getConf()->getConfString('ucFeedback', 'submit'), $formGen->getSubmitButton('surveyor_next_button', 'Anfordern'));
		
		//..und um die Formularelemente kommt noch ein Formular:
		$output = $formGen->getForm('pwdform', $this->getOwnLink(ucForgotPwd::STEP_CHECK), $generator->getHTML());
			
		//HTML in den Output schreiben...
		$this->appendOutput($output);
	}

	/**
	 * sendPasswordMail() generiert eine eMail mit den Zugangs-Daten
	 * @param $uid die Uid für die zu generierende eMail
	 * @return true, wenn alles geklappt hat, sonst false
	 */
	private function sendPasswordMail($uid)
	{
		$user = User::getForId($this, $uid);
		
		//zum Erstellen des Mailbodys kann wieder der "HTML-Generator" benutzt werden (ist eigentlich ein "Template-Generator"...)
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucForgotPwd', 'email', 'template'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucForgotPwd', 'emailaddy'), $user->getEMail());
		$generator->apply($this->getConf()->getConfString('ucForgotPwd', 'username'), $user->getUserName());
		$generator->apply($this->getConf()->getConfString('ucForgotPwd', 'userpwd'), $user->getPassword());
		//damit können alle Teile der eMail erzeugt werden:
		$body = $generator->getHTML();
		$subject = $this->getConf()->getConfString('ucForgotPwd', 'email', 'subject').$subject;
		$header = 'From: '.$this->getConf()->getConfString('ucForgotPwd', 'email', 'sender')."\nReply-To: ".$this->getConf()->getConfString('ucForgotPwd', 'email', 'replyto')."\n";
		$receiver = $user->getEMail();
		
		//eMail verschicken:
		return mail($receiver, $subject, $body, $header);
	}



}
?>