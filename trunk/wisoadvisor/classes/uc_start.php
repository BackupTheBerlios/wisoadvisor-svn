<?php

class ucStart extends UseCase
{
	//Ausführung: Business-Logik
	public function execute()
	{
		//der Start-UseCase zeigt den "Studienweg" an.
		//dazu muss von ucGraphics das entsprechende (mit Haken für den angemeldeten User befüllte) Bild angefordert werden
		//ausserdem wird die Image-Map mit den entsprechenden Links generiert

		//wenn der User angemeldet ist, dann wird als Parameter die Uid mit übergeben:
		$param = null;
		if ($this->getSess()->isAuthenticated()) $param = 'uid='.$this->getSess()->getUid();

		//Zur Anzeige des Formulars wird der HTML-Generator benutzt
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucStart', 'start_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucStart', 'uni'), $this->getUsecaseLink('info', ucInfo::STEP_BLOCK, Array(ucInfo::PARAMETER_BLID.'='.$this->getConf()->getConfString('ucStart', 'blid', 'uni')) ));
		$generator->apply($this->getConf()->getConfString('ucStart', 'kompetenz'), $this->getUsecaseLink('info', ucInfo::STEP_BLOCK, Array(ucInfo::PARAMETER_BLID.'='.$this->getConf()->getConfString('ucStart', 'blid', 'kompetenz')) ));
		$generator->apply($this->getConf()->getConfString('ucStart', 'interessen'), $this->getUsecaseLink('info', ucInfo::STEP_BLOCK, Array(ucInfo::PARAMETER_BLID.'='.$this->getConf()->getConfString('ucStart', 'blid', 'interessen')) ));
		$generator->apply($this->getConf()->getConfString('ucStart', 'ergebnis'), $this->getUsecaseLink('overview'));
		$generator->apply($this->getConf()->getConfString('ucStart', 'sponsors'), $this->getSponsorList());
	
		//HTML in den Output schreiben...
		$this->appendOutput($generator->getHTML());
		$this->setOutputType(USECASE_HTML);
		return true;
	}
	
	/**
	 * getSponsorList() erzeugt die Sponsorenliste
	 * @return die Liste der Sponsoren, als String (HTML)
	 */
	 private function getSponsorList()
	 {
		$resultString = '';
		
		$sponsors = $this->getDb()->query( $this->getConf()->getConfString('sql', 'sponsors', 'allSponsors') );

		while ($sponsor = $this->getDb()->fetch_array($sponsors))
		{
			$generator = new HtmlGenerator( $this->getConf()->getConfString('ucStart', 'sponsor_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
			//befülle den Generator mit den zu ersetzenden Anteilen...
			$generator->apply($this->getConf()->getConfString('ucStart', 'href'), $sponsor['href']);
			$generator->apply($this->getConf()->getConfString('ucStart', 'logo'), $this->getConf()->getConfString('ucStart', 'imagePath').$sponsor['logo']);
			$generator->apply($this->getConf()->getConfString('ucStart', 'spname'), $sponsor['sponsorname']);
	 	
	 		$resultString .= $generator->getHTML();
		}
		
		return $resultString;
	 }
}
?>