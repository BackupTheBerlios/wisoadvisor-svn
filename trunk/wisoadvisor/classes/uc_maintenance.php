<?php
//baut das Menü auf
//zur Zeit ohne Verwendung

class ucMaintenance extends UseCase
{
	//Ausführung: Business-Logik
	public function execute()
	{
		//zur Anzeige wird der static-Usecase benutzt
		$this->getParam()->setParameter('step', 'maintenance');
		$staticUc = new ucStatic();
		$staticUc->initialize($this->getConf(), $this->getDb(), $this->getParam(), $this->getSess());
		$staticUc->execute();
		
		$this->setOutput($staticUc->getOutput());
		$this->setOutputType(USECASE_HTML);
		return true;
	}
}
?>