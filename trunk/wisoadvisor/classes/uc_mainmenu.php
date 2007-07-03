<?php
//Mainmenu zeigt das Hauptmenü an

class ucMainmenu extends UseCase
{
	//Ausführung: Business-Logik
	public function execute()
	{
		$this->appendOutput('Im Moment nix mit Hauptmenü');

		$this->setOutputType(USECASE_HTML);
		return true;
	}
}
?>