<?php
//static zeigt "statische" HTMl-Seiten an - dazu wird das Haupttemplate benutzt und im Content-Bereich 
//der Inhalt des entsprechenden "statischen" Templates eingeblendet

class ucStatic extends UseCase
{
	//Ausf�hrung: Business-Logik
	public function execute()
	{
		//Pfad und Suffix zu den TemplateDateien:
		//Der Pfad h�ngt davon ab, ob der Nutzer eingeloggt ist oder nicht; f�r eingeloggte Nutzer stehen beide Suchpfade
		//zur Verf�gung, f�r nicht eingeloggte nur der "�ffentliche"
		$publicPath = $this->getConf()->getConfString('ucStatic', 'templatePath', 'all');
		$securePath = '';
		if ($this->getSess()->isAuthenticated()) $securePath = $this->getConf()->getConfString('ucStatic', 'templatePath', 'authenticated');

		//Suffix der Template-Files
		$suffix = $this->getConf()->getConfString('ucStatic', 'suffix');
		
		//welches Template soll gezogen werden? - Der Dateinmae entspricht immer dem "Step"-Parameter
		$templateFile = $this->getStep().$suffix;
		
		//versuchen, das Template zu lesen; zuerst im "securePath", ansonsten im �ffentlichen Pfad, ansonsten Fehler...
		if (file_exists($securePath.$templateFile))	$this->setOutput(file_get_contents($securePath.$templateFile));
		else if (file_exists($publicPath.$templateFile))	$this->setOutput(file_get_contents($publicPath.$templateFile));
		else { 
			$this->setOutput($this->getConf()->getConfString('messages', 'pagenotfound').'<br/>');
			$this->appendOutput('Seite: '.$this->getStep());
		}

		$this->setOutputType(USECASE_HTML);
		return true;
	}
}
?>