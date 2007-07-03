<?php
//führt die Registrierung durch

class ucRegistration extends UseCase
{
	//Ausführung: Business-Logik
	public function execute()
	{
		//unterschiedliches Verhalten, je nachdem, ob Nutzer angemeldet oder nicht; 
		//wenn der Nutzer bereits angemeldet ist, wird direkt auf den Standard-Usecase weitergeleitet
		if ($this->getSess()->isAuthenticated())
		{
			//leite auf den Standardusecase um	
			header('location:'.$this->getMainLink());
			$this->setOutputType(USECASE_NOTYPE);
			return true;
		}
		else
		{
			$this->setOutputType(USECASE_HTML);
			//abhängig vom Step...
			switch ($this->getStep())
			{
				case 'check':
					//Registrierungsdaten kontrollieren und Registrierung ggf. durchführen
					$this->checkRegistration();
					break;
				
				case 'confirm':
					//überprüfe die Parameterangaben und schalte den User ggf. frei
					$this->checkConfirmation();
					break;
					
				default:
					//zeige das Registrierungs-Formular
					$this->showRegistrationForm();
					break;
			}
			
			return true;
		}
	}

	/**
	 * checkRegistration() führt die Registrierung durch
	 */
	private function checkRegistration()
	{
		//zuerst die übergebenen Parameter einlesen:
		$email = $this->getParam()->getParameter('email');
		$username = $this->getParam()->getParameter('username');
		$password = $this->getParam()->getParameter('passwd');
		$passwordRepeat = $this->getParam()->getParameter('passwd_repeat');
		$gender = $this->getParam()->getParameter('gender');
		$birthday = $this->getParam()->getParameter('birthday');
		$datenschutz = $this->getParam()->getParameter('datenschutz');
		
		//in $ error werden evtl. Fehlermeldungen gesammelt.
		$error = '';
		
		//aus den übergebenen Daten wird ein neues UserObjekt gebaut:
		$user = User::getNew($this);
		$user->setEMail($email);
		$user->setUserName($username);
		$user->setPassword($password);
		$user->setGender($gender);
		$user->setBirthday($birthday);
		
		//zuerst checken: ist die eMailadresse gültig?
  		if (!preg_match($this->getConf()->getConfString('ucRegistration', 'regex', 'email'), $email)) $error .= $this->getConf()->getConfString('ucRegistration', 'error', 'email').'<br/>';
		//ist der Username ok?
  		if (!preg_match($this->getConf()->getConfString('ucRegistration', 'regex', 'username'), $username)) $error .= $this->getConf()->getConfString('ucRegistration', 'error', 'username').'<br/>';
		//ist das Geschlecht ok?
  		if (!preg_match($this->getConf()->getConfString('ucRegistration', 'regex', 'gender'), $gender)) $error .= $this->getConf()->getConfString('ucRegistration', 'error', 'gender').'<br/>';
		//ist der Username ok?
  		if (!preg_match($this->getConf()->getConfString('ucRegistration', 'regex', 'birthday'), $birthday)) $error .= $this->getConf()->getConfString('ucRegistration', 'error', 'birthday').'<br/>';
		//ist das Passwort ok?
  		if (!preg_match($this->getConf()->getConfString('ucRegistration', 'regex', 'password'), $password)) $error .= $this->getConf()->getConfString('ucRegistration', 'error', 'password').'<br/>';
		//stimmen Passwort und -Wiederholung überein?
		if ($password != $passwordRepeat) $error .= $this->getConf()->getConfString('ucRegistration', 'error', 'password_repeat').'<br/>';
		//wurde der Datenschutzhaken gesetzt?
		if (($datenschutz == null) || ($datenschutz == '')) $error .= $this->getConf()->getConfString('ucRegistration', 'error', 'datenschutz_akzeptieren').'<br/>';
		
		//wenn $error nicht leer ist, werden die Fehler angezeigt
		if ($error != '')
		{
			$this->showRegistrationForm($user, $error);
		}
		else
		{
			//ansonsten: Registrieren...	
			//zuerst: Verifikationscode generieren
			$authCode = $this->generateVerificationString();
			//Zielgruppe: derzeit immer 1!
			$targetgroup = 1;
//			//confirmed: zu diesem Zeitpunkt immer false (WICHTIG: als String!)
//			$confirmed = 'false';
//			IM MOMENT WIRD JEDER USER 'SOFORT' FREIGESCHALTET, DAMIT EINE FREIE REGISTRIERUNG OHNE EMAILBESTÄTIGUNG MÖGLICH IST!
			$confirmed = true;
			
			//UserObjekt weiter befüllen:
			$user->setConfirmed($confirmed);
			$user->setTgId($targetgroup);
			$user->setAuthCode($authCode);
			
			//Daten in Datenbank ablegen...
			try
			{
				$user->storeInDb($this);

				//Alles ok:	
				//1. eMail verschicken
				//$this->generateConfirmationMail($email, $username, $authCode);
				
//				//2. Bestätigungsseite anzeigen
//				$this->showConfirmation($email);
//				IM MOMENT - SIEHE OBEN - WIRD KEINE BESTÄTIGUNG ANGEZEIGT, SONDERN DIREKT WIEDER DER LOGIN!
//				header('location:'.$this->getUseCaseLink('login', '', Array('target='.urlencode($this->getParam()->getParameter('target')))));
//				$this->setOutputType(USECASE_NOTYPE);
				
				//jetzt setzen wir einfach noch die benötigten Parameter und loggen den User automatisch ein:
				$this->getParam()->setParameter('email', $user->getEMail());
				$this->getParam()->setParameter('passwd', $user->getPassword());
				$this->getParam()->setParameter('step', 'check');
				
				$ucLogin = new ucLogin();
				$ucLogin->initialize($this->getConf(), $this->getDb(), $this->getParam(), $this->getSess());
				
				$ucLogin->execute();
				
				$this->setOutputType($ucLogin->getOutputType());
				$this->setOutput($ucLogin->getOutput());

			}
			catch (ModelException $e)
			{
				//vereinfachte Annahme: ModelException wurde geworfen, weil die eMailadresse schon vorhanden war...
				$this->showRegistrationForm($user, $this->getConf()->getConfString('ucRegistration', 'error', 'double_email').'<br/>');
			}
		}
	}

	/**
	 * checkConfirmation() überprüft, ob die Registrierung freigeschaltet werden kann
	 * @return eine entsprechende HTML-Seite (direkt im Output)
	 * @deprecated derzeit nicht verwendet
	 */
	private function checkConfirmation()
	{
		//zuerst alle wichtigen Parameter lesen
		$email = $this->getParam()->getParameter('email');
		$authCode = $this->getParam()->getParameter('authentication');

		//checken, ob eMail und Verifizierungscode zusammenpassen
		$record = $this->getDb()->fetchPreparedRecord($this->getConf()->getConfString('sql', 'registration', 'verificate'), Array($email, $authCode));		
		//wenn der Record jetzt "leer" ist (keine UID), hat die Verifizierung NICHT geklappt
		if (($record['uid']=='') || (!$record['uid']))
		{
			//Fehlerseite anzeigen
			$this->showVerificationError($email);
		}
		else
		{
			//Verifizierung hat geklappt: Also: confirmed auf true setzen
			$result = $this->getDb()->preparedQuery($this->getConf()->getConfString('sql', 'registration', 'verificate_update'), Array($email, $authCode));		
			//je nachdem ob erfolgreich oder nicht, Fehlerseite anzeigen oder nicht
			if ($result) $this->showVerificationConfirmation($email);
			else $this->showVerificationError($email);
		}
	}

	/**
	 * showVerificationError() zeigt die "Verifizierungsfehlerseite" an
	 * @deprecated derzeit nicht verwendet
	 */	
	private function showVerificationError($email)
	{
			//zur Anzeige der Seite wird der HTML-Generator benutzt
			$generator = new HtmlGenerator( $this->getConf()->getConfString('ucRegistration', 'error_verification_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
			//befülle den Generator mit den zu ersetzenden Anteilen...
			$generator->apply($this->getConf()->getConfString('ucRegistration', 'email'), $email);
			//HTML in den Output schreiben...
			$this->appendOutput($generator->getHTML());
	}
	
	/**
	 * showVerificationConfirmation() zeigt die "Verifizierungsbestätigungsseite" an
	 * @deprecated derzeit nicht verwendet
	 */	
	private function showVerificationConfirmation($email)
	{
			//zur Anzeige der Seite wird der HTML-Generator benutzt
			$generator = new HtmlGenerator( $this->getConf()->getConfString('ucRegistration', 'confirm_verification_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
			//befülle den Generator mit den zu ersetzenden Anteilen...
			$generator->apply($this->getConf()->getConfString('ucRegistration', 'email'), $email);
			//HTML in den Output schreiben...
			$this->appendOutput($generator->getHTML());
	}
	
	/**
	 * showRegistrationForm() zeigt das Registrierungs-Formular, ggf. mit Fehlermeldungen an
	 * @param $user ein (ggf. leeres) Userobjekt
	 * @param $errorMessage eine Fehlermeldung als String
	 * @return das entsprechende HTML-Fragment (direkt im UseCase-Output)
	 */
	private function showRegistrationForm($user = null, $error = '')
	{
		if (!$user) $user = User::getNew($this);
		
		//zur Anzeige des Formulars wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucRegistration', 'registrationform_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucRegistration', 'registeraction'), $this->getOwnLink('check'));
		$generator->apply($this->getConf()->getConfString('ucRegistration', 'datenschutzlink'), $this->getUsecaseLink('static', 'datenschutz'));
		$generator->apply($this->getConf()->getConfString('ucRegistration', 'showerror'), $error);
		$generator->apply($this->getConf()->getConfString('ucRegistration', 'email'), $user->getEMail());
		$generator->apply($this->getConf()->getConfString('ucRegistration', 'username'), $user->getUserName());
		$generator->apply($this->getConf()->getConfString('ucRegistration', 'password'), $user->getPassword());

		//die gender-Radio-Boxen müssen extra bereitgestellt werden:
		//TODO: dazu den Form-Generator verwenden!!!
		$check['u'] = ''; $check['m'] = ''; $check['w'] = '';
		if ((!$user->getGender()) || ($user->getGender()=='')) $user->setGender('u');
		$check[$user->getGender()] = 'checked="checked"';
		$genderRadio = '<input type="radio" name="gender" id="gender_u" tabindex="5" '.$check['u'].' value="u"/><label for="gender_u"> keine Angabe</label><br/><input type="radio" tabindex="6" name="gender" id="gender_m" '.$check['m'].' value="m"/><label for="gender_m"> männlich</label><br/><input type="radio" tabindex="7" name="gender" id="gender_w" '.$check['w'].' value="w"/><label for="gender_w"> weiblich</label>';
		
		//das Birthday-Select muss auch gebaut werden 
		//TODO: auch hier den FormGenerator verwenden
		$selected = Array();
		if ((!$user->getBirthday()) || ($user->getBirthday()=='')) $user->setBirthday('0');
		$selected[(string) $user->getBirthday()] = ' selected="selected"';
		$birthdaySelect = '<select height="1" name="birthday" tabindex="8"><option value="0"'.@$selected['0'].'>keine Angabe</option><option value="1900"'.@$selected['1900'].'>vor 1970</option><option value="1970"'.@$selected['1970'].'>1970</option><option value="1971"'.@$selected['1971'].'>1971</option><option value="1972"'.@$selected['1972'].'>1972</option><option value="1973"'.@$selected['1973'].'>1973</option><option value="1974"'.@$selected['1974'].'>1974</option><option value="1975"'.@$selected['1975'].'>1975</option><option value="1976"'.@$selected['1976'].'>1976</option><option value="1977"'.@$selected['1977'].'>1977</option><option value="1978"'.@$selected['1978'].'>1978</option><option value="1979"'.@$selected['1979'].'>1979</option><option value="1980"'.@$selected['1980'].'>1980</option><option value="1981"'.@$selected['1981'].'>1981</option><option value="1982"'.@$selected['1982'].'>1982</option><option value="1983"'.@$selected['1983'].'>1983</option><option value="1984"'.@$selected['1984'].'>1984</option><option value="1985"'.@$selected['1985'].'>1985</option><option value="1986"'.@$selected['1986'].'>1986</option><option value="1987"'.@$selected['1987'].'>1987</option><option value="1988"'.@$selected['1988'].'>1988</option><option value="1989"'.@$selected['1989'].'>1989</option><option value="1990"'.@$selected['1990'].'>1990</option><option value="1991"'.@$selected['1991'].'>1991</option><option value="1992"'.@$selected['1992'].'>1992</option><option value="1993"'.@$selected['1993'].'>1993</option><option value="1994"'.@$selected['1994'].'>1994</option><option value="1995"'.@$selected['1995'].'>1995</option><option value="1996"'.@$selected['1996'].'>1996</option><option value="1997"'.@$selected['1997'].'>1997</option><option value="1998"'.@$selected['1998'].'>1998</option><option value="1999"'.@$selected['1999'].'>1999</option><option value="2000"'.@$selected['2000'].'>2000</option></select>';

		$generator->apply($this->getConf()->getConfString('ucRegistration', 'gender'), $genderRadio);
		$generator->apply($this->getConf()->getConfString('ucRegistration', 'birthday'), $birthdaySelect);

		//ggf. wird auch das Link-Target mit durchgereicht
		$generator->apply($this->getConf()->getConfString('ucRegistration', 'targetlink'), $this->getParam()->getParameter('target'));
	
		//HTML in den Output schreiben...
		$this->appendOutput($generator->getHTML());
	}

	/**
	 * showConfirmation() zeigt die Bestätigungsseite nach erfolgreicher Registrierung an
	 * @param $email eMailadresse
	 * @return das entsprechende HTML-Fragment (direkt im UseCase-Output)
	 * @deprecated derzeit nicht verwendet
	 */
	private function showConfirmation($email = '')
	{
		//zur Anzeige der Seite wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucRegistration', 'confirmation_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucRegistration', 'email'), $email);
		//HTML in den Output schreiben...
		$this->appendOutput($generator->getHTML());
	}

	/**
	 * generateConfirmationMail() generiert eine eMail mit dem Bestätigungslink zur Freischaltung der Registrierung
	 * @param $email eMailadresse des Empfängers
	 * @param $username Name des Empfängers
	 * @param $authCode Verifizierungscode
	 * @return true, wenn alles geklappt hat, sonst false
	 */
	private function generateConfirmationMail($email, $username, $authCode)
	{
		//Der Link verlinkt auf Registration, mit Step=confirm
		//es muss der komplette Servername angegeben werden...
//		$link = 'http://'.$_SERVER['SERVER_NAME'].$this->getOwnLink('confirm').'&email='.$email.'&authentication='.$authCode;
		$link = 'http://'.$_SERVER['SERVER_NAME'].$this->getMainLink(); //Link auf die Startseite
		//zum Erstellen des Mailbodys kann wieder der "HTML-Generator" benutzt werden (ist eigentlich ein "Template-Generator"...)
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucRegistration', 'email_confirmation', 'template'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucRegistration', 'email_confirmation', 'email'), $email);
		$generator->apply($this->getConf()->getConfString('ucRegistration', 'email_confirmation', 'username'), $username);
		$generator->apply($this->getConf()->getConfString('ucRegistration', 'email_confirmation', 'link'), $link);
		//damit können alle Teile der eMail erzeugt werden:
		$body = $generator->getHTML();
		$subject = $this->getConf()->getConfString('ucRegistration', 'email_confirmation', 'subject');
		$header = 'From: '.$this->getConf()->getConfString('ucRegistration', 'email_confirmation', 'sender')."\nReply-To: ".$this->getConf()->getConfString('ucRegistration', 'email_confirmation', 'replyto')."\n";
		
		//eMail verschicken:
		return mail($email, $subject, $body, $header);
	}

	/**
	 * generateVerificationString() erzeugt einen zufälligen String zur Verification der eMailadresse
	 * @return ein String mit dem Zufallscode
	 */
	 private function generateVerificationString()
	 {
	 	//einige "feste" Definitionen:
	 	$rndPossibilities['low'] = 48; //untere Grenze für den Ascii-Wert
	 	$rndPossibilities['up'] = 122; //obere Grenze für den Ascii-Wert
	 	$rndPossibilities['interval_numbers']['low'] = 48; //untere Grenze für Ziffern
	 	$rndPossibilities['interval_numbers']['up'] = 57; //obere Grenze für Ziffern
	 	$rndPossibilities['interval_chars_big']['low'] = 65; //untere Grenze für Großbuchstaben
	 	$rndPossibilities['interval_chars_big']['up'] = 90; //obere Grenze für Großbuchstaben
	 	$rndPossibilities['interval_chars_small']['low'] = 97; //untere Grenze für Kleinbuchstaben
	 	$rndPossibilities['interval_chars_small']['up'] = 122; //obere Grenze für Kleinbuchstaben
	 	
	 	//Länge des Verfifikationsstrings:
	 	$length = $this->getConf()->getConfInt('ucRegistration', 'authenticationLength');

	 	//in $code wird der String erstellt:
	 	$code = '';

		for ($i = 0; $i <= ($length - 1); $i++)
		{
	 		do 
	 		{
	 			$rndNumber = (int) rand($rndPossibilities['low'], $rndPossibilities['up']); //erzeuge Zufallszahl
	 		} while (
		 				(($rndNumber < $rndPossibilities['interval_numbers']['low']) || ($rndNumber > $rndPossibilities['interval_numbers']['up']))
	 					&& (($rndNumber < $rndPossibilities['interval_chars_big']['low']) || ($rndNumber > $rndPossibilities['interval_chars_big']['up']))
	 					&& (($rndNumber < $rndPossibilities['interval_chars_small']['low']) || ($rndNumber > $rndPossibilities['interval_chars_small']['up']))
	 				); //wiederhole, falls $rndNumber NICHT in einem der 3 Intervalle liegt
	 				
	 		//hänge das ermittelte Ascii-Zeichen an den String an
	 		$code .= chr($rndNumber);
		}
		
		return $code;
	 }

}
?>