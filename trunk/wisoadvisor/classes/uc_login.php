<?php
//führt den Login durch

class ucLogin extends UseCase
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
			//abhängig vom Step...
			switch ($this->getStep())
			{
				case 'check':
					//Login kontrollieren und ggf. durchführen
					
					//zuerst die übergebenen Parameter einlesen:
					$username = $this->getParam()->getParameter('email');
					$password = $this->getParam()->getParameter('passwd');
					
					//User authentifizieren:
					if ($this->getSess()->authenticate($username, $password))
					{
						//User ist authentifiziert - 
						//wenn target angegeben ist, dann dahin weiterleiten,
						//sonst auf den "afterLogin"-UseCase
						if ($this->getParam()->getParameter('target'))
							header('location:'.urldecode($this->getParam()->getParameter('target')));
						else header('location:'.$this->getUsecaseLink($this->getConf()->getConfString('usecaseAfterLogin')));
						$this->setOutputType(USECASE_NOTYPE);
					}
					else
					{
						//User ist NICHT authentifiziert: Login-Formular mit Fehlermeldung anzeigen
						$this->appendOutput($this->getLoginForm('loginform_tpl', $this->getConf()->getConfString('ucLogin', 'error', 'noAuthentication')));
						$this->setOutputType(USECASE_HTML);
					}
					break;
				
				default:
					//zeige das Login-Formular
					$this->appendOutput($this->getLoginForm('loginform_tpl'));
					$this->setOutputType(USECASE_HTML);
					break;
			}

			return true;
		}
	}

	/**
	 * getLoginForm() zeigt das Login-Formular, ggf. mit Fehlermeldungen an
	 * @param $template das zu benutzende Login-Template
	 * @param $errorMessage eine Fehlermeldung als String
	 * @return das entsprechende HTML-Fragment als String
	 */
	private function getLoginForm($template, $error = '')
	{
		//zur Anzeige des Formulars wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucLogin', $template), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucLogin', 'loginaction'), $this->getUsecaseLink('login', 'check'));
		$generator->apply($this->getConf()->getConfString('ucLogin', 'registeraction'), $this->getUsecaseLink('registration', '', Array('target='.urlencode($this->getParam()->getParameter('target')))));
		$generator->apply($this->getConf()->getConfString('ucLogin', 'datenschutzlink'), $this->getUsecaseLink('static', 'datenschutz'));
		$generator->apply($this->getConf()->getConfString('ucLogin', 'passwortlink'), $this->getUsecaseLink('forgotpassword'));
		$generator->apply($this->getConf()->getConfString('ucLogin', 'showerror'), $error);
		$generator->apply($this->getConf()->getConfString('ucLogin', 'targetlink'), $this->getParam()->getParameter('target'));
	
		return $generator->getHTML();
	}

	/**
	 * getLoginBox() liefert ein "abgespecktes" Login-Formular, das von anderen UseCases eingebunden werden kann
	 * @return das HTML-Fragment als String
	 */
	public function getLoginBox()
	{
		return $this->getLoginForm('loginbox_tpl');
	}
}
?>