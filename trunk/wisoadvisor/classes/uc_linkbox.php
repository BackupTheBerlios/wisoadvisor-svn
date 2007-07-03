<?php
//linkbox zeigt die allegeine "Linkbox" auf jeder Seite an

class ucLinkbox extends UseCase
{
	//Ausführung: Business-Logik
	public function execute()
	{
		//hier muss nur das Template befüllt werden
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucLinkbox', 'tpl_file'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));

		$generator->apply('STARTSEITE', $this->getMainLink());
		$generator->apply('ERGEBNISUEBERSICHT', $this->getUsecaseLink('overview'));
		$generator->apply('DATENSCHUTZ', $this->getUsecaseLink('static', 'datenschutz'));
		$generator->apply('IMPRESSUM', $this->getUsecaseLink('static', 'impressum'));
		$generator->apply('FEEDBACK', $this->getUsecaseLink('feedback', '', Array('reference='.urlencode('Position:'.$_SERVER['QUERY_STRING']))));

		$this->appendOutput($generator->getHTML());
		$this->setOutputType(USECASE_HTML);
		return true;
	}
}
?>