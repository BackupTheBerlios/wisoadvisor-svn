<?php
//baut das Menü auf
//zur Zeit ohne Verwendung

class ucMenu extends UseCase
{
	//Ausführung: Business-Logik
	public function execute()
	{
		$this->setOutput('');
		$this->setOutputType(USECASE_HTML);
		return true;
	}
}
?>