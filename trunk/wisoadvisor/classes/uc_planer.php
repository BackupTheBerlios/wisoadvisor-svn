<?php
class ucPlaner extends UseCase {

//Ausf�hrung: Business-Logik
public function execute()	{

	$ret = false;
  if (!$this->getSess()->isAuthenticated()) {
		header('location:'.$this->getMainLink());
		$this->setOutputType(USECASE_NOTYPE);
		$ret = true;
	
	} else {

	  $this->setOutputType(USECASE_HTML);
		$this->sayHello();

		/*
		switch ($this->getStep()) {
			case 'check':
				//Registrierungsdaten kontrollieren und Registrierung ggf. durchf�hren
				$this->checkRegistration();
				break;
			
			case 'confirm':
				//�berpr�fe die Parameterangaben und schalte den User ggf. frei
				$this->checkConfirmation();
				break;
				
			default:
				// pruefe, ob zus. Stammdaten vorhanden sind
				// falls ja: Pruefungsplan anzeigen
				// falls nein: Infotext anzeigen mit Link zum ucChangeUserData
				$this->showRegistrationForm();
				break;
		}
		*/
		
    $ret = true;
	}
	return $ret;
}
	
private function sayHello() {
  $generator = new HtmlGenerator( $this->getConf()->getConfString('ucPlaner', 'htmltemplate'), 
	                                $this->getConf()->getConfString('template', 'indicator', 'pre'), 
	                                $this->getConf()->getConfString('template', 'indicator', 'after'));
	                                
	$user = User::getForId($this, $this->getSess()->getUid());
	$generator->apply($this->getConf()->getConfString('ucPlaner', 'username'), $user->getUserName());
	$generator->apply($this->getConf()->getConfString('ucPlaner', 'studies'), $user->getStudies());
	$this->appendOutput($generator->getHTML());
	
  $this->setOutputType(USECASE_HTML);
  return true;
}

} // class
?>