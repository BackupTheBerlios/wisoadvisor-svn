<?php
/***********************************************************************************
 * WiSo@visor - Online Studienberatung der WiSo-Fakult�t
 * (c) 2006 Lehrstuhl f�r Wirtschaftsinformatik 3, Uni Erlangen-N�rnberg
 * R�ckfragen zu dieser Software: kompetenzmanagement@floooooo.de
 *
 * Datei: advisorPdf.php
 * Erstellt am: 18.06.2006
 * Erstellt von: flo
 ***********************************************************************************/
 
class AdvisorPdf extends FPDF {

	//einige Feststehende Konfigurationen
	//aus Gr�nden der Einfachheit stehen diese hier und nicht in der config
	//bei Arrays gilt: die Reihenfolge der Parameter entspricht der FPDF-Funktion, f�r die die Werte benutzt werden
	
	//Allgemeines:
	private $pdfCreator = 'WiSo@visor with FPDF';
	private $pdfAuthor = 'WiSo@visor, Wirtschafts- und Sozialwissenschaftliche Fakult�t, FAU Erlangen-N�rnberg';
	private $pdfSubject = 'pers�nliche Testergebnisse f�r ';
	private $pdfKeywords = '';
	private $pdfTitle = 'WiSo@visor - Der Online-Berater zur Studienwahl';
	
	private $pageMargins = Array(10, 10, 45, 25); //Links, rechts, oben, unten (in mm)
	
	private $font = 'Helvetica';
	private $fontStyle = '';
	private $fontSize = 12;
	private $fontCellHeight = 6;
	private $fontColor = 'black';
	private $fontAlign = 'L';
	private $fontTextAlign = 'J';
	
	//�berschriften:
	private $fontStyleHeadline = 'B';
	private $fontSizeHeadline = 18;
	private $fontCellHeightHeadline = 10;
	private $fontColorHeadline = 'darkblue';
	private $fontAlignHeadline = 'L';
	
	//�berschriften 2ter Ordnung:
	private $fontStyleSubline = 'B';
	private $fontSizeSubline = 15;
	private $fontCellHeightSubline = 10;
	private $fontColorSubline = 'darkblue';
	private $fontAlignSubline = 'L';
	
	//Spezial�berschriften f�r Blocks:
	private $fontStyleBlockline = 'B';
	private $fontSizeBlockline = 12;
	private $fontCellHeightBlockline = 6;
	private $fontColorBlockline = 'darkblue';
	private $fontAlignBlockline = 'L';

	private $color = Array( 'black'=> Array(0, 0, 0), 
							'darkblue'=> Array(0, 51, 102),
							'lightyellow'=> Array(255, 255, 51) );
	
	
	//Startseite:
	private $documentTitle = 'WiSo@visor';
	private $documentSubTitle = 'Der WiSo-Berater zur Studienwahl';
	private $startPageTitle = 'Pers�nliche Testergebnisse f�r ';
	private $startPageIntroText = '';
	private $documentTitleFontSize = 60;
	private $documentSubTitleFontSize = 24;
	private $documentTitleFontStyle = 'B';
	private $documentSubTitleFontStyle = 'B';
	private $titleHeight = 26;
	private $subTitleHeight = 15;
	private $titleTextPadding = 50;
	private $startPageTextPadding = 40; //Abstand f�r den Text
	
	//Letzte Seite:
	private $lastPageText = "(c) 2006\r\nLehrstuhl f�r Betriebswirtschaftslehre, insbes. Wirtschaftsinformatik III\r\nProf. Dr. Michael Amberg\r\n\r\nFriedrich-Alexander-Universit�t Erlangen-N�rnberg\r\nWirtschafts- und Sozialwissenschaftliche Fakult�t\r\nLange Gasse 20\r\n90403 N�rnberg\r\n\r\nFon 0911-5302-801\r\nFax 0911-5302-860\r\nwi3@wiso.uni-erlangen.de\r\n\r\n\r\nAnsprechpartner: Sonja Fischer, Manuela Schr�der";
	private $lastPageFontSize = 8;
	private $lastPageFontStyle = '';
	private $lastPageTextPadding = 130; //Abstand f�r den Text
	
	//Hinweise-Seite:
	private $infoPageText = "Im Folgenden erh�ltst Du eine Zusammenfassung der Ergebnisse von allen Tests, die Du durchgef�hrt hast. Das Ergebnis enth�lt sowohl absolute Aussagen dar�ber, wie gut Du die einzelnen Tests absolviert hast bzw. in welchem Umfang Du die im Studium gestellten Anforderungen (z. B. Lern- und Einsatzbereitschaft oder Organisationsf�higkeit) erf�llst, als auch am Anfang jedes Testergebnisses eine relative Aussage dar�ber, wie gut Du im Vergleich zu den anderen Testteilnehmern (unteres, mittleres oder oberes Drittel) stehst. Diese relative Aussage kann sich je nach Anzahl und Antwortverhalten der Testteilnehmer ver�ndern.\r\n\r\nZur besseren �bersicht werden Deine Einzelergebnisse grafisch veranschaulicht.\r\nDer jeweils am Anfang einer Testauswertung abgebildete Balken zeigt Dich im Vergleich zu den anderen Testteilnehmern. Der wei�e Strich markiert Dein eigenes Testergebnis, w�hrend die R�nder des Balkens jeweils das schlechteste bzw. beste Ergebnis der anderen Nutzer (reduziert um die Extremwerte) symbolisieren.\r\nAn den Grafiken im Bereich \"Deine Ergebnisse im Einzelnen\" erkennst Du Deinen Stand im Vergleich zu den Ergebnissen der anderen Testteilnehmer. Der wei�e Strich markiert dann Dein eigenes Testergebnis, w�hrend der dunkelblaue Bereich die Bandbreite (reduziert um die Extremwerte) der Ergebnisse der anderen Benutzer zeigt.\r\nZus�tzlich setzen wir die bekannten \"Smileys\" ein, um Dir ein Ergebnis auf einen Blick zu veranschaulichen:\r\n\"traurig\" bedeutet: Du hast leider nur bis zu ein Drittel der gestellten Fragen richtig beantworten k�nnen. Lies Dir die Infoseiten noch einmal gewissenhaft durch und Du wirst den Erfolg sehen, wenn Du den jeweiligen Test wiederholst.\r\nDer \"unentschlossene Smiley\" sagt aus, dass Du zwischen 33,3% und 66,6% aller Fragen richtig beantwortet hast und somit im guten Mittelfeld liegst. Die restlichen Informationen bekommst Du �ber die Informationsseiten und die Homepage der WiSo-Fakult�t.\r\nEinen Gl�ckwunsch sprechen wir Dir mit dem \"lachenden Smiley\" aus: Du hast zwischen 66,6% und 100% der Fragen richtig beantwortet, in diesem Bereich bist Du top informiert!\r\n\r\nDeine Daten werden nat�rlich vertraulich behandelt und nicht an Dritte weitergegeben. Sie flie�en nicht in das Auswahl- und Zulassungsverfahren der Hochschule ein. Dieses Testergebnis dient lediglich Dir bei der Studienfach- und Hochschulwahl.\r\n\r\nWir w�nschen Dir viel Spa� beim Lesen Deiner Ergebnisse!";
	private $infoPageTitle = 'Hinweise';
	
	//Fazit-Seite:
	private $fazitPageText = "Die Testergebnisse sollen Dir vor allem helfen, Dich mit Deinen St�rken und Schw�chen, mit dem Hochschulort Erlangen-N�rnberg sowie deinen Interessen, Berufsw�nschen und Vorwissen bezogen auf die hier angebotenen Studieng�nge auseinander zu setzen.\r\nDie Fragen dieses Tests wurden so gew�hlt, dass Du einen Einblick in die Besonderheiten der Hochschule und der einzelnen Studieng�nge gewinnst.\r\n\r\nFalls Dich das Testergebnis von dieser Hochschule und einem der angebotenen Studieng�nge �berzeugt hat, w�rden wir uns �ber Deine Bewerbung sehr freuen!";
	private $fazitPageTitle = 'Fazit';
	private $fazitPageSubheadline = 'Weitere Informationen';
	private $fazitPageSubtext = "Weitere Informationen zu unseren Bachelorstudieng�ngen findest Du unter www.bachelor.wiso.uni-erlangen.de ,\r\nInfos zu unserer Fakult�t gibt es unter www.wiso.uni-erlangen.de";
	
	//Overview-Seite:
	private $overviewPageTitle = 'Ergebnis�bersicht';
	
	//ResultSeiten:
	private $resultPageTextPadding = 0;
	private $resultSingleResultsText = 'Deine Ergebnisse im Einzelnen';
	
	private $surveyIconsTexts = Array('BWL', 'VWL', 'WiP�d', 'WI', 'IBS', 'Soz�k');
	private $surveyIconsWidth = 30;
	
	//Header:
	private $headerLogoCoordinates = Array('grafik/pdf/advisorlogo.png', 10, 5, 190, 29); 
	
	//Footer:
	private $footerFontSize = 8;
	private $footerPosition = -15; //mm vom unteren Rand ab
	private $footerHeight = 10;
	
	//Weitere:
	private $tocTitle = 'Inhalt';
	private $tocLastPage = 'Kontakt und Copyright';
	
	private $testResult = '       Dein Ergebnis: Du hast ';
	private $testResult2 = ' % erreicht';
	private $smileySize = Array('height'=>6, 'width'=>6);
	private $barSize = Array('height'=>6, 'width'=>60);
	private $rankingBarPadding = 75;
	
	/**
	 * Member, die nicht vordefniert werden, sondern �ber setter-Methoden mit Werten belegt werden k�nnen
	 */
	private $creationDate = '';
	private $userName = '';
	private $pdfResults = Array();
	private $impressumLink;
	private $infoPageLink;
	private $fazitPageLink;
	private $overviewPageLink;
	private $tocLink;
	private $pageIdentifier = 105; //wir starten bei 103, um "verwechslungen" auszuschlie�en
	//100 + 101 + 102 + 103 + 104 f�r TOC und Impressum, Fazit, Overview und Infoseite
	
	/**
	 * inkrementiert den PageIdentifier
	 */
	private function nextPageIdentifier()
	{
		$this->pageIdentifier++;
		return $this->pageIdentifier++;
	}

	/**
	 * zeichnet auf jeder Seite den PageHeader; geerbt von FPDF
	 */
	public function Header()
	{
	    //Logo
	    $this->Image($this->headerLogoCoordinates[0], 
	    				$this->headerLogoCoordinates[1], 
	    				$this->headerLogoCoordinates[2], 
	    				$this->headerLogoCoordinates[3], 
	    				$this->headerLogoCoordinates[4]);
	}
	
	/**
	 * zeichnet auf jeder Seite den PageFooter; geerbt von FPDF
	 */
	function Footer()
	{
	    //Position at 1.5 cm from bottom
	    $this->SetY($this->footerPosition);
	    $this->SetFont($this->font, $this->fontStyle, $this->footerFontSize);
		$this->SetTextColor($this->color[$this->fontColor][0], $this->color[$this->fontColor][1], $this->color[$this->fontColor][2]);
	    //Page number
	    $this->Cell(0, $this->footerHeight, $this->startPageTitle.$this->userName.', '.$this->creationDate, 0, 0,'L');
	    $this->Cell(0, $this->footerHeight, 'Seite '.$this->PageNo().'/{nb}', 0, 0, 'R');
	}

	/**
	 * formatiert eine Seitenzahl - einstellige werden zu zweistelligen
	 * @return String die Seitenzahl
	 */
	private function getFormattedPageNumber()
	{
		$pageNumber = ($this->PageNo()<10) ? '  '.$this->PageNo() : $this->PageNo();
		return $pageNumber;
	}

	/**
	 * setzt das Erstellungdatum
	 */
	private function setCreationDate()
	{
		$this->creationDate = 'erstellt am '.date('d.m.Y, H:i', time()).' Uhr';
	}

	/**
	 * setzt den Usernamen
	 * @param $value der zu setzende Wert
	 */
	public function setUserName($value)
	{
		$this->userName = $value;
	}

 	/**
 	 * f�gt ein PdfResult-Objekt hinzu 
 	 * @param $pdfResult ein PdfResult-Objekt
 	 * @throws ModelException
 	 */
 	public function addPdfResult(PdfResult $pdfResult)
 	{
 		try {
 			$this->pdfResults[] = $pdfResult;
 		}
 		catch (Exception $e)
 		{
 			throw new ModelException('AdvisorPdf->addPdfResult: cant add child result');
 		}
 	}

	/**
	 * erzeugt die "Startseite"
	 */
	private function getFirstPage()
	{
		$this->AddPage();
		
		//Cursor setzen:
		$this->SetY($this->pageMargins[2]+$this->titleTextPadding);
		
		//blaue Schrift:
		$this->SetTextColor($this->color[$this->fontColorHeadline][0], $this->color[$this->fontColorHeadline][1], $this->color[$this->fontColorHeadline][2]);
		//Titel und Untertitel:
		$this->SetFont($this->font, $this->documentTitleFontStyle, $this->documentTitleFontSize);
		$this->Cell(0, $this->titleHeight, $this->documentTitle, 0, 1, 'C');
		
		$this->SetFont($this->font, $this->documentSubTitleFontStyle, $this->documentSubTitleFontSize);
		$this->Cell(0, $this->subTitleHeight, $this->documentSubTitle, 0, 1, 'C');
		
		//2ter Untertitel:
		$this->SetFont($this->font, $this->fontStyle, $this->fontSize);
		$this->Cell(0, 10, $this->startPageTitle.$this->userName, 0, 2, 'C');
		
		//Begr��ungstext:
		$this->SetY($this->GetY()+$this->startPageTextPadding);
		
		$this->SetTextColor($this->color[$this->fontColor][0], $this->color[$this->fontColor][1], $this->color[$this->fontColor][2]);
		$this->SetFont($this->font, $this->fontStyle, $this->fontSize);
		$this->MultiCell(0, $this->fontCellHeight, $this->startPageIntroText, 0, 'J');
		
	}
	
	/**
	 * erzeugt die letzte Dokumentseite
	 */	
	private function getLastPage()
	{
		$this->AddPage();
		$this->SetLink($this->impressumLink);
		//Seitenzahl f�r Inhaltsverzeichnis:
		$this->AddReplacement('100', $this->getFormattedPageNumber());
		
		//Cursor setzen:
		$this->SetY($this->pageMargins[2]+$this->lastPageTextPadding);
		
		$this->SetFont($this->font, $this->lastPageFontStyle, $this->lastPageFontSize);
		$this->SetTextColor($this->color[$this->fontColor][0], $this->color[$this->fontColor][1], $this->color[$this->fontColor][2]);
		//Text:
		$this->MultiCell(0, $this->fontCellHeight, $this->lastPageText, 0, 'L');

	}
	
	/**
	 * erzeugt die "Fazit"-Seite
	 */
	private function getFazitPage()
	{
		//eine Seite erzeugen
		$this->AddPage();
		//Link setzen: 
		$this->SetLink($this->fazitPageLink);
		
		$this->AddReplacement('103', $this->getFormattedPageNumber());
		
		//Cursor setzen:
		$this->SetY($this->pageMargins[2]+$this->resultPageTextPadding);
		
		//�berschrift:
		$this->SetFont($this->font, $this->fontStyleHeadline, $this->fontSizeHeadline);
		$this->SetTextColor($this->color[$this->fontColorHeadline][0], $this->color[$this->fontColorHeadline][1], $this->color[$this->fontColorHeadline][2]);
			//die �berschrift ist im Dokument verlinkt - die Links wurden bereits vorher von der getToc()-Funktion gesetzt
		$this->Cell(0, $this->fontCellHeightHeadline, $this->fazitPageTitle, 0, 1, $this->fontAlignHeadline);
		
		//Text schreiben:
		$this->SetFont($this->font, $this->fontStyle, $this->fontSize);
		$this->SetTextColor($this->color[$this->fontColor][0], $this->color[$this->fontColor][1], $this->color[$this->fontColor][2]);
		$this->Ln();
		$this->MultiCell(0, $this->fontCellHeight, $this->fazitPageText, 0, $this->fontTextAlign);

		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		//weitere Infos:
		//�berschrift:
		$this->SetFont($this->font, $this->fontStyleSubline, $this->fontSizeSubline);
		$this->SetTextColor($this->color[$this->fontColorSubline][0], $this->color[$this->fontColorSubline][1], $this->color[$this->fontColorSubline][2]);
		$this->MultiCell(0, $this->fontCellHeightSubline, $this->fazitPageSubheadline, 0, $this->fontAlignSubline);
		
		//Text schreiben:
		$this->SetFont($this->font, $this->fontStyle, $this->fontSize);
		$this->SetTextColor($this->color[$this->fontColor][0], $this->color[$this->fontColor][1], $this->color[$this->fontColor][2]);
		$this->Ln();
		$this->MultiCell(0, $this->fontCellHeight, $this->fazitPageSubtext, 0, $this->fontTextAlign);
	}

	/**
	 * erzeugt die "Ergebnis�bersicht"-Seite
	 */
	private function getOverviewPage()
	{
		//eine Seite erzeugen
		$this->AddPage();
		//Link setzen: 
		$this->SetLink($this->overviewPageLink);
		
		$this->AddReplacement('104', $this->getFormattedPageNumber());
		
		//Cursor setzen:
		$this->SetY($this->pageMargins[2]+$this->resultPageTextPadding);
		
		//�berschrift:
		$this->SetFont($this->font, $this->fontStyleHeadline, $this->fontSizeHeadline);
		$this->SetTextColor($this->color[$this->fontColorHeadline][0], $this->color[$this->fontColorHeadline][1], $this->color[$this->fontColorHeadline][2]);
			//die �berschrift ist im Dokument verlinkt - die Links wurden bereits vorher von der getToc()-Funktion gesetzt
		$this->Cell(0, $this->fontCellHeightHeadline, $this->overviewPageTitle, 0, 1, $this->fontAlignHeadline);
		

		//zuerst: Bl�cke durchgehen...
		$results = $this->pdfResults;

		foreach ($results as $result) 
		{
			//direkt: Block�berschrift schreiben
			$this->SetFont($this->font, $this->fontStyleSubline, $this->fontSizeSubline);
			$this->SetTextColor($this->color[$this->fontColorSubline][0], $this->color[$this->fontColorSubline][1], $this->color[$this->fontColorSubline][2]);
			$this->MultiCell(0, $this->fontCellHeightSubline, $result->getTocNumber().' '.$result->getTitle(), 0, $this->fontTextAlign);
			
			//dann: die darunterh�ngenden Surveys schreiben, abh�ngig vom Blocktyp:
			if ($result->getResultType()==PdfResult::TYPE_BAR)
			{
				$childResults = $result->getAllPdfResults();
				foreach ($childResults as $childResult) $this->getOverviewSurveyBar($childResult);
			}
			else
			{
				$childResults = $result->getAllPdfResults();
				foreach ($childResults as $childResult) $this->getOverviewSurveyIcons($childResult);
			}
		}
	}

	/**
	 * schreibt f�r die Ergebnis�bersicht ein einzelnes Testergebnis (Icons)
	 * @param $pdfResult ein PdfResult (Survey)
	 */
	private function getOverviewSurveyIcons(PdfResult $pdfResult)
	{
		//Testbezeichnung als normaler Text:
		$this->SetFont($this->font, $this->fontStyle, $this->fontSize);
		$this->SetTextColor($this->color[$this->fontColor][0], $this->color[$this->fontColor][1], $this->color[$this->fontColor][2]);
		$this->MultiCell(0, $this->fontCellHeight, $pdfResult->getTocNumber().' '.$pdfResult->getTitle(), 0, $this->fontTextAlign);
		$this->Ln();
		
		//TODO: auslagern und generieren oder so!
		//wir machen hier alles von Hand
		//zuerst alle 6 Studieng�nge;
		$cellX = $this->GetX() + ($this->surveyIconsWidth / 2);
		$cellY = $this->GetY();
		$i = null;
		for ($i=0;$i<=5;$i++)
		{
			$this->Text($cellX, $cellY, $this->surveyIconsTexts[$i]);
			$cellX += $this->surveyIconsWidth;
		}
		
		//Studieng�nge jeweils mit Smiley: = alle Kinder dieses Results:
		$childResults = $pdfResult->getAllPdfResults();
		$standardSmiley = 'grafik/grau2.png';
		$smileys = array('bwl'=>$standardSmiley, 'vwl'=>$standardSmiley, 'wipaed'=>$standardSmiley, 'wi'=>$standardSmiley, 'ibs'=>$standardSmiley, 'sowi'=>$standardSmiley);
		foreach ($childResults as $childResult) 
		{
			$smiley = $childResult->getSmiley();
			switch ($childResult->getTitle())
			{
				case 'Betriebswirtschaftslehre': $smileys['bwl'] = $smiley; break;
				case 'Volkswirtschaftslehre': $smileys['vwl'] = $smiley; break;
				case 'Wirtschaftsp�dagogik': $smileys['wipaed'] = $smiley; break;
				case 'Wirtschaftsinformatik': $smileys['wi'] = $smiley; break;
				case 'International Business Studies': $smileys['ibs'] = $smiley; break;
				case 'Sozial�konomik': $smileys['sowi'] = $smiley; break;
			}
		}

		//richtig sortiertes Array:
		$smileyArr = Array($smileys['bwl'], $smileys['vwl'], $smileys['wipaed'], $smileys['wi'], $smileys['ibs'], $smileys['sowi']);
		//Smilies malen:
		$imgX = $this->pageMargins[0] + ($this->surveyIconsWidth / 2) - $this->smileySize['width'] - 2;
		$imgY = $this->GetY()-$this->fontCellHeight+2;
		$i = null;
		for ($i=0;$i<=5;$i++)
		{
		    $this->Image($smileyArr[$i], $imgX, $imgY, $this->smileySize['width'], $this->smileySize['height'], 'PNG');
			$imgX += $this->surveyIconsWidth;
		}
		$this->Ln();

	}

	/**
	 * schreibt f�r die Ergebnis�bersicht ein einzelnes Testergebnis (Balken)
	 * @param $pdfResult ein PdfResult (Survey)
	 */
	private function getOverviewSurveyBar(PdfResult $pdfResult)
	{
		//Testbezeichnung als normaler Text:
		$this->SetFont($this->font, $this->fontStyle, $this->fontSize);
		$this->SetTextColor($this->color[$this->fontColor][0], $this->color[$this->fontColor][1], $this->color[$this->fontColor][2]);
		$this->MultiCell(0, $this->fontCellHeight, $pdfResult->getTocNumber().' '.$pdfResult->getTitle(), 0, $this->fontTextAlign);
		
		//Smiley & Balken:
		$this->Ln();
		
		$imgX = $this->pageMargins[0];
		$imgY = $this->GetY() - $this->fontCellHeight;
	    if ($pdfResult->getSmiley())
	    		$this->Image($pdfResult->getSmiley(), $imgX, $imgY, $this->smileySize['width'], $this->smileySize['height']);
	    
	    $imgX = $this->rankingBarPadding;
		$imgY = $imgY; 
		if ($pdfResult->getBar())
				$this->Image($pdfResult->getBar(), $imgX, $imgY, $this->barSize['width'], $this->barSize['height']);
				
		$this->Ln();
	}

	/**
	 * erzeugt die "Hinweise"-Seite
	 */
	private function getInfoPage()
	{
		//eine Seite erzeugen
		$this->AddPage();
		//Link setzen: 
		$this->SetLink($this->infoPageLink);
		
		$this->AddReplacement('102', $this->getFormattedPageNumber());
		
		//Cursor setzen:
		$this->SetY($this->pageMargins[2]+$this->resultPageTextPadding);
		
		//�berschrift:
		$this->SetFont($this->font, $this->fontStyleHeadline, $this->fontSizeHeadline);
		$this->SetTextColor($this->color[$this->fontColorHeadline][0], $this->color[$this->fontColorHeadline][1], $this->color[$this->fontColorHeadline][2]);
			//die �berschrift ist im Dokument verlinkt - die Links wurden bereits vorher von der getToc()-Funktion gesetzt
		$this->Cell(0, $this->fontCellHeightHeadline, $this->infoPageTitle, 0, 1, $this->fontAlignHeadline);
		
		//Text schreiben:
		$this->SetFont($this->font, $this->fontStyle, $this->fontSize);
		$this->SetTextColor($this->color[$this->fontColor][0], $this->color[$this->fontColor][1], $this->color[$this->fontColor][2]);
		$this->Ln();
		$this->MultiCell(0, $this->fontCellHeight, $this->infoPageText, 0, $this->fontTextAlign);
	}

	/**
	 * erzeugt die Seiten f�r alle gespeicherten Results
	 */
	private function getAllResultPages()
	{
		$results = $this->pdfResults;
		
		//f�r jedes Result muss eine Seite (oder Teile davon...) erzeugt werden:
		foreach ($results as $result) $this->getResultPage($result); //ggf. werden hier auch rekursiv ChildResults abgearbeitet
		
	}
	
	/**
	 * erzeugt ein bis mehrere "Resultseiten"
	 * @param $pdfResult ein PDF-Resultobjekt
	 * @param $link: optional: PDFinterner Linkpointer
	 * @param $counter: optional: Objektz�hler
	 * @param $blockIdent: optional: BlockPageIdentifier
	 */	
	private function getResultPage($pdfResult, $link = null, $counter = null, $blockIdent = null)
	{
		//ist das Result ein Charakteristikum? (und hat deshalb keine Kinder)
		if ($pdfResult->isCharacteristic())
		{
			$this->getCharacteristicBlock($pdfResult);
		}
		elseif ($pdfResult->isBlock())
		{
			//oder ist es ein Block?
			
			$counter = 0;
			//dann: rekursiver Aufruf f�r alle Childresults:
			foreach ($pdfResult->getAllPdfResults() as $childResult) 
			{
				$this->getResultPage($childResult, $pdfResult->getPdfInternalLink(), $counter, $pdfResult->getPdfPageIdentifier());
				$counter++;
			}
		}
		else
		{
			$this->getSurveyPage($pdfResult, $link, $counter, $blockIdent);
			
			//dann: rekursiver Aufruf f�r alle Childresults: (ausser wenn die Survey eine Icon-Survey ist!!!)
			if (!($pdfResult->getResultType()==PdfResult::TYPE_SMILEYS)) 
				foreach ($pdfResult->getAllPdfResults() as $childResult) $this->getResultPage($childResult);
		}
		
	}
	
	/**
	 * liefert einen "Surveyblock" f�r ein Result, das eine Umfrage repr�sentiert
	 * @param $pdfResult ein PdfResult
	 * @param $link: optional: PDFinterner Linkpointer
	 * @param $counter: optional: Objektz�hler
	 * @param $blockPage: optional: PageIdentifier eines Blocks
	 */
	private function getSurveyPage($pdfResult, $link = null, $counter = null, $blockPage = null)
	{
		//eine Survey hat im Prinzip einen Characteristic-Block (nur auf "ihrer" Ebene), bestehend
		//ggf. aus Balken, Smiley und Text; darunter kommt sowieso das Handling der Einzelmerkmale
		//prinzipiell f�ngt aber jede Survey auf einer neuen Seite an:

		//eine Seite erzeugen
		$this->AddPage();
		//Link setzen: Block und Survey, Block nur, wenn es der "NULLTE" ist
		if ($counter==0) $this->SetLink($link);
		$this->SetLink($pdfResult->getPdfInternalLink());
		
		//Seitenzahl f�r Inhaltsverzeichnis: - wenn Nullter Block, dann auch f�r den:
		$this->AddReplacement($pdfResult->getPdfPageIdentifier(), $this->getFormattedPageNumber());
		if ($counter==0) $this->AddReplacement($blockPage, $this->getFormattedPageNumber());
		
		//Cursor setzen:
		$this->SetY($this->pageMargins[2]+$this->resultPageTextPadding);
		
		//�berschrift: 2 Zeilen, eine mit dem BlockTitel und eine mit dem SurveyTitel
		$this->SetFont($this->font, $this->fontStyleBlockline, $this->fontSizeBlockline);
		$this->SetTextColor($this->color[$this->fontColorBlockline][0], $this->color[$this->fontColorBlockline][1], $this->color[$this->fontColorBlockline][2]);
			//die �berschrift ist im Dokument verlinkt - die Links wurden bereits vorher von der getToc()-Funktion gesetzt
		$this->MultiCell(0, $this->fontCellHeightBlockline, $pdfResult->getParentTitle(), 0, $this->fontAlignBlockline);

		$this->SetFont($this->font, $this->fontStyleHeadline, $this->fontSizeHeadline);
		$this->SetTextColor($this->color[$this->fontColorHeadline][0], $this->color[$this->fontColorHeadline][1], $this->color[$this->fontColorHeadline][2]);
			//die �berschrift ist im Dokument verlinkt - die Links wurden bereits vorher von der getToc()-Funktion gesetzt
		$this->Cell(0, $this->fontCellHeightHeadline, $pdfResult->getTocNumber().' '.$pdfResult->getTitle(), 0, 1, $this->fontAlignHeadline);
		
		//Resulttext etc. schreiben:
		$this->SetFont($this->font, $this->fontStyle, $this->fontSize);
		$this->SetTextColor($this->color[$this->fontColor][0], $this->color[$this->fontColor][1], $this->color[$this->fontColor][2]);
		$this->Ln();

		$this->MultiCell(0, $this->fontCellHeight, $pdfResult->getText(), 0, $this->fontTextAlign);
		$this->Ln();
		
		//Grafiken und zweiter Text nur, wenn Typ=BAR
		if ($pdfResult->getResultType()==PdfResult::TYPE_BAR)
		{
			//Smiley & Balken:
			$this->Ln();
			
			$imgX = $this->pageMargins[0];
			$imgY = $this->GetY() - $this->fontCellHeight;
		    if ($pdfResult->getSmiley())
		    		$this->Image($pdfResult->getSmiley(), $imgX, $imgY, $this->smileySize['width'], $this->smileySize['height']);
		    
		    $imgX = $this->rankingBarPadding;
			$imgY = $imgY; 
			if ($pdfResult->getBar())
					$this->Image($pdfResult->getBar(), $imgX, $imgY, $this->barSize['width'], $this->barSize['height']);
					
			$this->Ln();

			$this->SetFont($this->font, $this->fontStyleSubline, $this->fontSizeSubline);
			$this->SetTextColor($this->color[$this->fontColorSubline][0], $this->color[$this->fontColorSubline][1], $this->color[$this->fontColorSubline][2]);
			$this->MultiCell(0, $this->fontCellHeightSubline, $this->resultSingleResultsText, 0, $this->fontTextAlign);

			$this->SetFont($this->font, $this->fontStyle, $this->fontSize);
			$this->SetTextColor($this->color[$this->fontColor][0], $this->color[$this->fontColor][1], $this->color[$this->fontColor][2]);
			$this->MultiCell(0, $this->fontCellHeight, $pdfResult->getSecondText(), 0, $this->fontTextAlign);
			$this->Ln();

		}
		
	}
	
	/**
	 * liefert f�r ein PDFResult das Ergebnis im PDF
	 * @param $pdfResult das Result
	 */
	private function getResultInPdf(PdfResult $pdfResult)
	{
		//Result-Text und Grafiken:
		$this->SetFont($this->font, $this->fontStyle, $this->fontSize);
		$this->SetTextColor($this->color[$this->fontColor][0], $this->color[$this->fontColor][1], $this->color[$this->fontColor][2]);
		$this->Ln();
		
		//Grafiken nur, wenn Typ=BAR
		if ($pdfResult->getResultType()==PdfResult::TYPE_BAR)
		{
			//Smiley & Balken:
			$this->Cell(0, $this->fontCellHeight, $this->testResult.$pdfResult->getPercentage().$this->testResult2, 0, 1, $this->fontAlign);
			
			$imgX = $this->pageMargins[0];
			$imgY = $this->GetY() - $this->fontCellHeight;
		    if ($pdfResult->getSmiley())
		    		$this->Image($pdfResult->getSmiley(), $imgX, $imgY, $this->smileySize['width'], $this->smileySize['height']);
		    
		    $imgX = 210 - ($this->pageMargins[1] + $this->barSize['width']);
			$imgY = $imgY; 
			if ($pdfResult->getBar())
					$this->Image($pdfResult->getBar(), $imgX, $imgY, $this->barSize['width'], $this->barSize['height']);
					
			$this->Ln();
		}
		
		$this->MultiCell(0, $this->fontCellHeight, $pdfResult->getText(), 0, $this->fontTextAlign);
		$this->Ln();
		
	}

	/**
	 * liefert einen "Characteristicblock" f�r ein Result
	 * @param $pdfResult ein PdfResult
	 */
	private function getCharacteristicBlock($pdfResult)
	{
		//am Anfang muss gepr�ft werden, ob eine neue Seite eingef�gt werden muss.
		//dies ist dann der Fall, wenn auf die Aktuelle Seite nur noch die �berschrift und das Ergebnis von der H�he
		//her passen w�rden. Deshalb:
		//�berschrifth�he + Ergebnish�he + Leerzeilenh�he + Leerzeilenh�he dazu mind. 1 Zeile Text
		//und nat�rlich der untere Seitenrand
		$neededMinimumSpace = $this->fontCellHeightSubline + (3 * $this->fontCellHeight) + $this->fontCellHeight + $this->pageMargins[3];
		//aktuelle Cursorposition muss weiter oben sein; wenn nicht: Seitenumbruch
		if ($this->GetY() > (297 - $neededMinimumSpace)) $this->AddPage(); //297=Seitenl�nge A4!
		
		//Seitenzahl f�r Inhaltsverzeichnis:
		$this->AddReplacement($pdfResult->getPdfPageIdentifier(), $this->getFormattedPageNumber());
		//Linkziel:
		$this->SetLink($pdfResult->getPdfInternalLink());

		//�berschrift
		$this->SetFont($this->font, $this->fontStyleSubline, $this->fontSizeSubline);
		$this->SetTextColor($this->color[$this->fontColorSubline][0], $this->color[$this->fontColorSubline][1], $this->color[$this->fontColorSubline][2]);
			//die �berschrift ist im Dokument verlinkt - die Links wurden bereits vorher von der getToc()-Funktion gesetzt
		$this->Cell(0, $this->fontCellHeightSubline, $pdfResult->getTocNumber().' '.$pdfResult->getTitle(), 0, 1, $this->fontAlignSubline);
		
		//Resulttext etc. schreiben:
		$this->getResultInPdf($pdfResult);
	}
	
	/**
	 * erzeugt das Inhaltsverzeichnis, setzt dazu dokumentinterne Links
	 */
	private function getToc()
	{
		//eine Seite erzeugen
		$this->AddPage();
		
		//"feste" Links erzeugen:
		$this->tocLink = $this->AddLink();
		$this->impressumLink = $this->AddLink();
		$this->infoPageLink = $this->AddLink();
		$this->fazitPageLink = $this->AddLink();
		$this->overviewPageLink = $this->AddLink();
		
		//Cursor setzen:
		$this->SetY($this->pageMargins[2]+$this->resultPageTextPadding);
		
		//�berschrift
		$this->SetFont($this->font, $this->fontStyleHeadline, $this->fontSizeHeadline);
		$this->SetTextColor($this->color[$this->fontColorHeadline][0], $this->color[$this->fontColorHeadline][1], $this->color[$this->fontColorHeadline][2]);
		$this->Cell(0, $this->fontCellHeightHeadline, $this->tocTitle, 0, 1, $this->fontAlignHeadline);
		
		//Leerzeile:
		$this->Ln();
		
		//Schrift f�r Eintr�ge:
		$this->SetFont($this->font, $this->fontStyle, $this->fontSize);
		$this->SetTextColor($this->color[$this->fontColor][0], $this->color[$this->fontColor][1], $this->color[$this->fontColor][2]);
		
		//Zeile f�r Inhalt:
		$this->AddReplacement('101', $this->getFormattedPageNumber());
		$this->writeTocLine($this->tocTitle, $this->tocLink, 0, '101');
		$this->Ln();
		
		//Zeile f�r Infoseite:
		$this->writeTocLine($this->infoPageTitle, $this->infoPageLink, 0, '102');
		$this->Ln();
		
		//Zeile f�r �bersicht:
		$this->writeTocLine($this->overviewPageTitle, $this->overviewPageLink, 0, '104');
		$this->Ln();
		
		//Zeilen f�r Resultbl�cke;
		$this->writeTocResults($this->pdfResults);
		
		//Zeile f�r Fazit-Seite:
		$this->Ln();
		$this->writeTocLine($this->fazitPageTitle, $this->fazitPageLink, 0, '103');
		
		//Zeile f�r letzte Seite:
		$this->Ln();
		$this->writeTocLine($this->tocLastPage, $this->impressumLink, 0, '100');
		
	}
	
	/**
	 * liefert rekursiv Verzeichniseintr�ge f�r Results
	 * @param $resultArray Array mit Results
	 * @param $level Level des Eintrags; 0 ist oberste Ebene
	 */
	private function writeTocResults($resultArray, $level = 0)
	{
		foreach ($resultArray as $result) 
		{
				$link = $this->AddLink();
				$result->setPdfInternalLink($link);
				$result->setPdfPageIdentifier($this->nextPageIdentifier());
					
				//aber nur, wenn es nicht schon die unterste Ebene (=Characteristic) ist:
				if (!$result->isCharacteristic())
					$this->writeTocLine($result->getTocNumber().' '.$result->getTitle(), $link, $level, $result->getPdfPageIdentifier());
					
				//dasselbe f�r die Kinder:
				$this->writeTocResults($result->getAllPdfResults(), $level + 1);
		}
	}
	
	/**
	 * erzeugt eine Zeile f�rs Inhaltsverzeichnis
	 * @param $line Text der Zeile
	 * @param $link Linkziel
	 * @param $level Level der �berschrift - 0 ist oberste Ebene
	 * @param $page Seitenidentifier
	 */
	private function writeTocLine($line, $link, $level = 0, $page = 0)
	{
		$levelString = '';
		$i = null;
		for ($i == 0; $i<=$level; $i++) $levelString .= '     ';
		
		//Vorgehen: Zeile mit "Inhalt" schreiben (linksb�ndig)...
		$tocLine = $levelString.$line;
		$this->Cell(0, $this->fontCellHeight, $tocLine, 0, 1, 'L', 0, $link);
		//dann: Cursor um Texth�he heraufsetzen...
		$this->SetY($this->GetY()-$this->fontCellHeight);

		//Stringbreite von eben ausrechnen:
		$width= $this->GetStringWidth($tocLine);
		//Seitenzahlstring solang verl�ngern, bis die Punkte fast an den Text reichen:
		$tocPageNumber = '{rp}'.$page;
		$tocPageNumberWidth = 210 - ($this->pageMargins[0] + $this->pageMargins[1] + $width + 3); //Seitenbreite minus R�nder und Stringbreite von eben und "Sicherheit"
		while ( $this->GetStringWidth($tocPageNumber)<$tocPageNumberWidth ) $tocPageNumber = '.'.$tocPageNumber;
		
		//...und rechtsb�ndig Zelle mit Seitenzahl schreiben:
		$this->Cell(0, $this->fontCellHeight, $tocPageNumber, 0, 1, 'R', 0, $link);
		
	}
	
	/**
	 * erzeugt das PDF
	 * @param $fileName Dateiname
	 * @return true bei Erfolg
	 */
	public function createPdf($fileName)
	{
		//Dokument einrichten:
		$this->setCreationDate();
		$this->AliasNbPages();
		$this->AliasReplacementOnClose();
		$this->SetAutoPageBreak(true, $this->pageMargins[3]);
		$this->SetLeftMargin($this->pageMargins[0]);
		$this->SetRightMargin($this->pageMargins[1]);
		$this->SetTopMargin($this->pageMargins[2]);

		//Eigenschaften setzen:
		$this->SetAuthor($this->pdfAuthor);
		$this->SetTitle($this->pdfTitle);
		$this->SetKeywords($this->pdfKeywords);
		$this->SetSubject($this->pdfSubject.$this->userName);
		$this->SetCreator($this->pdfCreator);

		//Startseite schreiben:
		$this->getFirstPage();
		
		//Inhaltsverzeichnis schreiben:
		$this->getToc();
		
		//Hinweiseseite:
		$this->getInfoPage();
		
		//�bersicht:
		$this->getOverviewPage();
		
		//alle "Results" abarbeiten
		$this->getAllResultPages();
		
		//Fazit-Seite:
		$this->getFazitPage();

		//Letzte Seite:
		$this->getLastPage();
		
		//PDF-Datei erzeugen:
		$this->Output($fileName, 'F');
		return true;
	}
}
?>
