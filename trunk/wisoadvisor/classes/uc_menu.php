<?php
//baut das Men� auf
//zur Zeit ohne Verwendung

class ucMenu extends UseCase
{
	//Ausf�hrung: Business-Logik
	public function execute()
	{
		$this->setOutput('');
		$this->setOutputType(USECASE_HTML);
		return true;
	}
}
?>