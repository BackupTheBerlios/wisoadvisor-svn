<?php
//Mainmenu zeigt das Hauptmen� an

class ucMainmenu extends UseCase
{
	//Ausf�hrung: Business-Logik
	public function execute()
	{
		$this->appendOutput('Im Moment nix mit Hauptmen�');

		$this->setOutputType(USECASE_HTML);
		return true;
	}
}
?>