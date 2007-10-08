<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: uc_change_userdata.php
 * $Revision: 1.7 $
 * Erstellt am: 06.06.2006
 * Erstellt von: Florian Strecker
 ***********************************************************************************/

class ucChangeUserData extends UseCase
{
	const STEP_CHECK = 'check';
	
	const PARAMETER_EMAIL = 'email';
	const PARAMETER_USERNAME = 'username';
	const PARAMETER_PWD = 'password';
	const PARAMETER_PWD_REPEAT = 'password_repeat';
	const PARAMETER_GENDER = 'gender';
	const PARAMETER_BIRTHDAY = 'birthday';
	const PARAMETER_STUDIES = 'studies';
	const PARAMETER_MATNR = 'matnr';
	const PARAMETER_SEMSTART = 'sem_start';
	

	//Ausführung: Business-Logik
	public function execute()
	{
		//abhängig vom Step...
		switch ($this->getStep())
		{
			case ucChangeUserData::STEP_CHECK:
				//Daten kontrollieren und ggf. in der DB ändern
				$this->checkUserData();
				break;
			
			default:
				//zeige das Änderungs-Formular
				$this->showChangeForm();
				break;
		}

		$this->setOutputType(USECASE_HTML);
		return true;
	}

	/**
	 * checkUserData() überprüft die eingegebenen Daten und trägt sie ggf. in die DB ein
	 */
	private function checkUserData()
	{
		//zuerst die übergebenen Parameter einlesen:
		$email = $this->getParam()->getParameter(ucChangeUserData::PARAMETER_EMAIL);
		$username = $this->getParam()->getParameter(ucChangeUserData::PARAMETER_USERNAME);
		$password = $this->getParam()->getParameter(ucChangeUserData::PARAMETER_PWD);
		$passwordRepeat = $this->getParam()->getParameter(ucChangeUserData::PARAMETER_PWD_REPEAT);
		$gender = $this->getParam()->getParameter(ucChangeUserData::PARAMETER_GENDER);
		$birthday = $this->getParam()->getParameter(ucChangeUserData::PARAMETER_BIRTHDAY);
		$studies = $this->getParam()->getParameter(ucChangeUserData::PARAMETER_STUDIES);
		$matnr = $this->getParam()->getParameter(ucChangeUserData::PARAMETER_MATNR);
		$sem_start = $this->getParam()->getParameter(ucChangeUserData::PARAMETER_SEMSTART);
		
		//in $message werden alle Rueckmeldungen gesammelt.
		$message = '';
		
		//in $error werden evtl. Fehlermeldungen gesammelt.
		$error = '';
		
		//zuerst checken: ist die eMailadresse gültig?
  		if (!preg_match($this->getConf()->getConfString('ucChangeUserData', 'regex', 'email'), $email)) $error .= $this->getConf()->getConfString('ucChangeUserData', 'error', 'email').'<br/>';
		//ist der Username ok?
  		if (!preg_match($this->getConf()->getConfString('ucChangeUserData', 'regex', 'username'), $username)) $error .= $this->getConf()->getConfString('ucRegistration', 'error', 'username').'<br/>';
		//ist das Geschlecht ok?
  		if (!preg_match($this->getConf()->getConfString('ucChangeUserData', 'regex', 'gender'), $gender)) $error .= $this->getConf()->getConfString('ucChangeUserData', 'error', 'gender').'<br/>';
		//ist der Geburtstag ok?
  		if (!preg_match($this->getConf()->getConfString('ucChangeUserData', 'regex', 'birthday'), $birthday)) $error .= $this->getConf()->getConfString('ucChangeUserData', 'error', 'birthday').'<br/>';
		//ist das Passwort ok?
  		if (!preg_match($this->getConf()->getConfString('ucChangeUserData', 'regex', 'password'), $password)) $error .= $this->getConf()->getConfString('ucChangeUserData', 'error', 'password').'<br/>';
		//ist das MatNr ok?
  		if (!preg_match($this->getConf()->getConfString('ucChangeUserData', 'regex', 'matnr'), $matnr)) $error .= $this->getConf()->getConfString('ucChangeUserData', 'error', 'matnr').'<br/>';
  		//stimmen Passwort und -Wiederholung überein?
		if ($password != $passwordRepeat) $error .= $this->getConf()->getConfString('ucChangeUserData', 'error', 'password_repeat').'<br/>';
		
		//wenn $error nicht leer ist, werden die Fehler angezeigt
		if ($error != '') {
		  $message = $error;
		} else {
		  
			//einfach das Formular wieder mit dem Hinweis, dass alles gespeichert wurde, anzeigen:
			$message .= $this->getConf()->getConfString('ucChangeUserData', 'message_text', 'stored').'<br/>';
		  
			//ansonsten: die geänderten Daten schreiben	
			//dazu wird erst ein User-Objekt angelegt und befüllt:
			$user = User::getForId($this, $this->getSess()->getUid());
			
			$user->setEMail($email);
			$user->setUserName($username);
			$user->setGender($gender);
			$user->setBirthday($birthday);
			$user->setPassword($password);
      $user->setMatNr($matnr);
      
      // wenn startsemester oder schwerpunkt veraendert: pruefungsplan ausradieren
      if (($user->getMajId() != $studies) || ($user->getSemStart() != $sem_start)) {
        ScheduleEntry::deleteAllForUser($this, $user->getId());
			  $message .= $this->getConf()->getConfString('ucChangeUserData', 'message_text', 'schedule').'<br/>';        
      }
      
      $user->setMajId($studies);
      $user->setSemStart($sem_start);
      
			$user->storeInDb($this);
			
			//ausserdem: Das Session-Objekt aktualisieren; dazu wird neu Authentifiziert:
			$this->getSess()->authenticate($user->getEMail(), $user->getPassword());
			
		}
		// form wieder anzeigen, aber mit botschaft
		$this->showChangeForm($message);
	}

	/**
	 * showChangeForm() zeigt das Daten-Änderungs-Formular, ggf. mit Fehlermeldungen an
	 * @param $message eine Nachricht oder Fehlermeldung als String
	 * @return das entsprechende HTML-Fragment (direkt im UseCase-Output)
	 */
	private function showChangeForm($message = '')
	{
		//ein Userobjekt wird zur Anzeige der Daten benutzt
		$user = User::getForId($this, $this->getSess()->getUid());
		
		//zur Anzeige des Formulars wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucChangeUserData', 'changeform_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucChangeUserData', 'message'), $message);
		
		//für alles, was ein Formularelement ist, wird der Formgenerator benutzt:
		$formGen = new HtmlFormGenerator();
		
		//die gender-Radio-Boxen müssen extra bereitgestellt werden:
		//TODO: dazu den Form-Generator verwenden!!!
		$check['u'] = ''; $check['m'] = ''; $check['w'] = '';
		$check[$user->getGender()] = 'checked="checked"';
		$genderRadio = '<input type="radio" name="gender" id="gender_u" '.$check['u'].' value="u"/><label for="gender_u"> keine Angabe</label><br/><input type="radio" name="gender" id="gender_m" '.$check['m'].' value="m"/><label for="gender_m"> männlich</label><br/><input type="radio" name="gender" id="gender_w" '.$check['w'].' value="w"/><label for="gender_w"> weiblich</label>';
		
		//das Birthday-Select muss auch gebaut werden 
		//TODO: auch hier den FormGenerator verwenden
		$selected = Array();
		$selected[(string) $user->getBirthday()] = ' selected="selected"';
		$birthdaySelect = '<select height="1" name="birthday"><option value="0"'.@$selected['0'].'>keine Angabe</option><option value="1900"'.@$selected['1900'].'>vor 1970</option><option value="1970"'.@$selected['1970'].'>1970</option><option value="1971"'.@$selected['1971'].'>1971</option><option value="1972"'.@$selected['1972'].'>1972</option><option value="1973"'.@$selected['1973'].'>1973</option><option value="1974"'.@$selected['1974'].'>1974</option><option value="1975"'.@$selected['1975'].'>1975</option><option value="1976"'.@$selected['1976'].'>1976</option><option value="1977"'.@$selected['1977'].'>1977</option><option value="1978"'.@$selected['1978'].'>1978</option><option value="1979"'.@$selected['1979'].'>1979</option><option value="1980"'.@$selected['1980'].'>1980</option><option value="1981"'.@$selected['1981'].'>1981</option><option value="1982"'.@$selected['1982'].'>1982</option><option value="1983"'.@$selected['1983'].'>1983</option><option value="1984"'.@$selected['1984'].'>1984</option><option value="1985"'.@$selected['1985'].'>1985</option><option value="1986"'.@$selected['1986'].'>1986</option><option value="1987"'.@$selected['1987'].'>1987</option><option value="1988"'.@$selected['1988'].'>1988</option><option value="1989"'.@$selected['1989'].'>1989</option><option value="1990"'.@$selected['1990'].'>1990</option><option value="1991"'.@$selected['1991'].'>1991</option><option value="1992"'.@$selected['1992'].'>1992</option><option value="1993"'.@$selected['1993'].'>1993</option><option value="1994"'.@$selected['1994'].'>1994</option><option value="1995"'.@$selected['1995'].'>1995</option><option value="1996"'.@$selected['1996'].'>1996</option><option value="1997"'.@$selected['1997'].'>1997</option><option value="1998"'.@$selected['1998'].'>1998</option><option value="1999"'.@$selected['1999'].'>1999</option><option value="2000"'.@$selected['2000'].'>2000</option></select>';
		
		$generator->apply($this->getConf()->getConfString('ucChangeUserData', 'username'), $formGen->getInput(ucChangeUserData::PARAMETER_USERNAME, $user->getUserName()));
		$generator->apply($this->getConf()->getConfString('ucChangeUserData', 'email'), $formGen->getInput(ucChangeUserData::PARAMETER_EMAIL, $user->getEMail()));
		$generator->apply($this->getConf()->getConfString('ucChangeUserData', 'password'), $formGen->getPasswordInput(ucChangeUserData::PARAMETER_PWD, $user->getPassword()));
		$generator->apply($this->getConf()->getConfString('ucChangeUserData', 'password_rep'), $formGen->getPasswordInput(ucChangeUserData::PARAMETER_PWD_REPEAT, $user->getPassword()));
		$generator->apply($this->getConf()->getConfString('ucChangeUserData', 'gender'), $genderRadio);
		$generator->apply($this->getConf()->getConfString('ucChangeUserData', 'birthday'), $birthdaySelect);
		
		/* Stammdaten Wiso@visor v2 */
		
		$generator->apply($this->getConf()->getConfString('ucChangeUserData', 'matnr'), $formGen->getInput(ucChangeUserData::PARAMETER_MATNR, $user->getMatNr()));
		$studiesSelect = HtmlFormGenerator::getDropDownFromDb($this, "studies", $this->getConf()->getConfString('sql', 'registration', 'dropdown_studies'), "fullname", "majid", $user->getMajId());				
		$generator->apply($this->getConf()->getConfString('ucChangeUserData', 'studies'), $studiesSelect);
		$semStartSelect = HtmlFormGenerator::getDropDownSemester("sem_start", $user->getSemStart());
		$generator->apply($this->getConf()->getConfString('ucChangeUserData', 'sem_start'), $semStartSelect);
		
		/* ende v2 */
		
		$generator->apply($this->getConf()->getConfString('ucChangeUserData', 'reset'), $formGen->getResetButton('resetbutton', 'Zurücksetzen'));
		$generator->apply($this->getConf()->getConfString('ucChangeUserData', 'submit'), $formGen->getSubmitButton('surveyor_next_button', 'Ändern'));
		
		//..und um die Formularelemente kommt noch ein Formular:
		$output = $formGen->getForm('changedataform', $this->getOwnLink(ucChangeUserData::STEP_CHECK), $generator->getHTML());
			
		//HTML in den Output schreiben...
		$this->appendOutput($output);
	}

}
?>