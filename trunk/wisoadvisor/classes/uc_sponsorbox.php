<?php
/***********************************************************************************
 * WiSo@visor - Online Studienberatung der WiSo-Fakult�t
 * (c) 2006 Lehrstuhl f�r Wirtschaftsinformatik 3, Uni Erlangen-N�rnberg
 * R�ckfragen zu dieser Software: kompetenzmanagement@floooooo.de
 * 
 * ucSponsorBox ist daf�r zust�ndig, auf jeder Contentseite einen
 * Link zu einem zuf�llig ausgew�hlten Sponsor anzuzeigen
 *
 * Datei: uc_sponsorbox.php
 * Erstellt am: 06.05.2006
 * Erstellt von: Florian Strecker
 ***********************************************************************************/

class ucSponsorBox extends UseCase
{
	//Ausf�hrung: Business-Logik
	public function execute()
	{
		//zuerst wird gepr�ft, ob der angezeigte "Hauptusecase" vielleicht eine Ausnahme ist, die
		//KEINE Sponsorbox enth�lt
		if (in_array($this->getDispatcherParam(), $this->getConf()->getConfArray('ucSponsorBox', 'exceptionsWithoutBox')))
		{
			$this->setOutput(''); //einfach leerer Output
		}
		else
		{
			//ein Sponsor wird ZUF�LLIG ausgew�hlt
			//Achtung: das zugeh�rige SQL-Statement ist wahrscheinlich propriet�r; bei Benutzung einer 
			//anderen Datenbank muss es ge�ndert werden (in der configuration.php, Schl�ssel siehe n�chste Zeile)
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