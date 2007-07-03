<?php
//ucSession zeigt die "Session-Box" mit dem Login/Logout-Link an

class ucSession extends UseCase
{
	//Ausführung: Business-Logik
	public function execute()
	{
		/*
		 * Fallunterscheidung: Ist ein Nutzer angemeldet, erscheinen in der "Sessionbox" seine Nutzerdaten
		 * und ein "abmelden"-Link
		 * Ist er nicht angemeldet, erscheint eine Login-Box
		 */
		
		if ($this->getSess()->isAuthenticated())
		{
			$generator = new HtmlGenerator( $this->getConf()->getConfString('ucSession', 'authenticated_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
			$generator->apply($this->getConf()->getConfString('ucSession', 'username'), $this->getSess()->getUserData('username'));
			$generator->apply($this->getConf()->getConfString('ucSession', 'useremail'), $this->getSess()->getUserData('email'));
			$generator->apply($this->getConf()->getConfString('ucSession', 'changedata'), $this->getUsecaseLink('changeuserdata'));
			$generator->apply($this->getConf()->getConfString('ucSession', 'logout'), $this->getUsecaseLink('logout'));
			
			$this->appendOutput($generator->getHTML());
		}
		else
		{
			//in diesem Fall wird der Login-Usecase verwendet - mit speziellem Template und target
			//AUSNAHME: Der useCase im Content-Bereich ist schon der Login-Usecase - schließlich wollen wir das nicht doppelt gemoppelt,
			//um die armen User nicht zu verwirren...
			if ($this->getParam()->getParameter($this->getConf()->getConfString('dispatchparamname')) != 'login')
			{
				//target ist die Seite selbst, die gerade angezeigt wird!
				$this->getParam()->setParameter('target', urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']));
				
				$ucLogin = new ucLogin();
				$ucLogin->initialize( $this->getConf(), $this->getDb(), $this->getParam(), $this->getSess());
	
				$this->appendOutput( $ucLogin->getLoginBox() );
			}
			else
			{
				//im anderen Fall ist der Output einfach:
				$this->appendOutput( 'nicht angemeldet' );	
			}
		}
		
		$this->setOutputType(USECASE_HTML);
		return true;
	}
}
?>