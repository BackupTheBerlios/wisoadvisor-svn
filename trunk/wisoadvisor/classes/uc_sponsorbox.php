<?php
/***********************************************************************************
 * WiSo@visor - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de
 * 
 * ucSponsorBox ist dafür zuständig, auf jeder Contentseite einen
 * Link zu einem zufällig ausgewählten Sponsor anzuzeigen
 *
 * Datei: uc_sponsorbox.php
 * Erstellt am: 06.05.2006
 * Erstellt von: Florian Strecker
 ***********************************************************************************/

class ucSponsorBox extends UseCase
{
	//Ausführung: Business-Logik
	public function execute()
	{
		//zuerst wird geprüft, ob der angezeigte "Hauptusecase" vielleicht eine Ausnahme ist, die
		//KEINE Sponsorbox enthält
		if (in_array($this->getDispatcherParam(), $this->getConf()->getConfArray('ucSponsorBox', 'exceptionsWithoutBox')))
		{
			$this->setOutput(''); //einfach leerer Output
		}
		else
		{
			//ein Sponsor wird ZUFÄLLIG ausgewählt
			//Achtung: das zugehörige SQL-Statement ist wahrscheinlich proprietär; bei Benutzung einer 
			//anderen Datenbank muss es geändert werden (in der configuration.php, Schlüssel siehe nächste Zeile)
			$sponsor = $this->getDb()->fetchRecord( $this->getConf()->getConfString('sql', 'sponsors', 'randomSponsor') );
			$usercount = $this->getDb()->fetchRecord( $this->getConf()->getConfString('sql', 'sponsors', 'usercount') );
			
			$generator = new HtmlGenerator( $this->getConf()->getConfString('ucSponsorBox', 'sponsorbox_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
			$generator->apply($this->getConf()->getConfString('ucSponsorBox', 'href'), $sponsor['href']);
			$generator->apply($this->getConf()->getConfString('ucSponsorBox', 'sponsorname'), $sponsor['sponsorname']);
			$generator->apply($this->getConf()->getConfString('ucSponsorBox', 'logo'), 
											$this->getConf()->getConfString('ucSponsorBox', 'imagePath').$sponsor['logo']);
			
			$generator->apply($this->getConf()->getConfString('ucSponsorBox', 'usercount'), $usercount['zahl']);
			$this->appendOutput($generator->getHTML());
		}

		$this->setOutputType(USECASE_HTML);
		return true;
	}
}
?>