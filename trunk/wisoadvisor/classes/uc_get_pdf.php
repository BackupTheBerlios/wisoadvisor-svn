<?php

class ucGetPdf extends UseCase
{
	/**
	 * Member
	 */
	private $fileIdentifier;
	private $fileIncrementor = 0;
	private $imgFileNames;
	private $imgCreator;
	private $fileLocation;
	private $tocNumbers = Array( 	Array('void', 'A', 'B', 'C', 'D', 'E'), 
									Array('void', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'), 
									Array('void', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10')
								); //etwas unsauber: der "Zähler" für die Inhaltsnummerierung

	/**
	 * gibt den nächsten Inhaltsverzeichniseintrag zurück
	 * Einträge und Ebenen sind in $this->tocNumbers definiert
	 * @param $level Level des Eintrages - 0 = oberste Ebene
	 * @return String mit dem Eintrag
	 */	
	private function getNextTocNumber($level = 0)
	{
		//wenn der Level = 0 ist, dann müssen die untergeordneten Zeiger zurückgesetzt werden:
		if ($level==0)
		{
			reset($this->tocNumbers[1]);
			reset($this->tocNumbers[2]);
			return next($this->tocNumbers[0]);
		}
		elseif ($level==1)
		{
			reset($this->tocNumbers[2]);
			return current($this->tocNumbers[0]).next($this->tocNumbers[1]);
		}
		elseif ($level==2)
		{
			return current($this->tocNumbers[0]).current($this->tocNumbers[1]).'.'.next($this->tocNumbers[2]);
		}
		else return false;
	}

	/**
	 * setzt den Identifier für die Bilddateinamen
	 * @param String $value der Identifier
	 */
	private function setFileId($value)
	{
		$this->fileIdentifier = $value;
	}
	
	/**
	 * gibt den eindeutigen FileIdentifier zurück
	 */
	private function getFileId()
	{
		$this->fileIncrementor++;
		return $this->fileLocation.$this->fileIdentifier.$this->fileIncrementor;
	}
	
	/**
	 * Hilfsmethode: entfernt HTML-Tags aus einem String und ersetzt jeden neuen Absatz und den
	 * HTML-Umbruch <br/> durch ein \r\n
	 * @param String $text
	 * @return String der modifizierte String
	 */
	private function convertFromHtml($text)
	{
		//zuerst ersetzen wir <br>, <br />, <br/>, <p>: (wir machen das "von Hand" um das schnellere str_replace verwenden zu können)
		$text = str_replace ( '<br>', "\r\n", $text );
		$text = str_replace ( '<br/>', "\r\n", $text );
		$text = str_replace ( '<br />', "\r\n", $text );
		//bei <p> ein Trick: wir nehmen nur das Absatzende!
		$text = str_replace ( '</p>', "\r\n", $text );
		
		//jetzt noch verbleibende HTML-Tags weg:
		$text = strip_tags($text);
		
		return $text;
	}
	
	/**
	 * Hilfsmethode zur Smiley-Anzeige
	 * 
	 * @param $filename die zu erzeugende Datei
	 * @param $result das %-Ergebnis
	 * @param $yellowBackground true wenn der gelbe Hintergrund benötigt wird
	 */
	private function createSmiley($filename, $result = null, $yellowBackground = false) {
		// TODO : Result-Auswertung in DB!!
		if ($result <= 33.3) $smiley = ImageCreator::SMILEY_POOR;
		if ($result > 33.3 and $result <= 66.7) $smiley = ImageCreator::SMILEY_AVERAGE;
		if ($result > 66.7) $smiley = ImageCreator::SMILEY_GOOD;
		if ($result == null) $smiley = ImageCreator::SMILEY_UNKNOWN;
		if ($yellowBackground)
			$this->imgCreator->showSmiley($smiley, ImageCreator::BACKGROUND_YELLOW, $filename);
		else
			$this->imgCreator->showSmiley($smiley, ImageCreator::BACKGROUND_WHITE, $filename);
	} 
	
	/**
	 * Liefert ein PDFresult für einen Block
	 * 
	 * @param SurveyBlock $block Der darzustellende SurveyBlock
	 * @return PdfResult ein PdfResult
	 */
	private function getResultBlock(SurveyBlock $block) {
		
		$blockResult = new PdfResult();
		$blockResult->setTitle($block->getTitle());
		$blockResult->setIsBlock(true);
		$blockResult->setTocNumber($this->getNextTocNumber(0));
		
		//jetzt im Baum weiter absteigen auf Survey-Ebene:
		$surveys = Survey::getForBlock($this, $block->getId());
		foreach ($surveys as $survey) 
		{
			if ($block->getType()==SurveyBlock::TYPE_BARS)
			{
				$blockResult->setResultType(PdfResult::TYPE_BAR);
				$blockResult->addPdfResult($this->getResultSurveyBars($survey, $block->getTitle()));
			}
			else
			{
				$blockResult->setResultType(PdfResult::TYPE_SMILEYS);
				$blockResult->addPdfResult($this->getResultSurveyIcons($survey, $block->getTitle()));
			}
		}
		
		return $blockResult;
	}

	/**
	 * Liefert ein PDFresult für eine Survey, die ihr Ergebnis als Statusbalken darstellt
	 * 
	 * @param Survey $survey Die darzustellende Survey
	 * @param String $title Titel des Übergeordneten Blocks
	 */
	private function getResultSurveyBars(Survey $survey, $title) 
	{
		$attempt = $survey->getAttemptForUser($this, $this->getSess()->getUid());
		$completed = $attempt > 0;
		$result = null;
		$surveyResult = null;
		$text = 'Für diesen Test liegt uns noch kein Ergebnis für Dich vor.';

		$pdfResult = new PdfResult();
		$pdfResult->setResultType(PdfResult::TYPE_BAR);
		$pdfResult->setTitle($survey->getTitle());
		$pdfResult->setParentTitle($title);
		$pdfResult->setTocNumber($this->getNextTocNumber(1));
		
		//Balkenbild: dem PdfResult Datei zuweisen und im FileSystem erzeugen!
		$filename = $this->getFileId().'.png';
		$this->imgFileNames[] = $filename;
		
		if ($completed) {
			$surveyResult = new SurveyResult($this, $survey, $this->getSess()->getUid(), $attempt); 
			$result = $surveyResult->getResult();
			$text = $surveyResult->getResultHeader();
			
			//Bilddatei erzeugen:
			$this->imgCreator->showRankingBar($result, true, $filename);
		}
		else $this->imgCreator->showEmptyResultBar($filename);
		
		$pdfResult->setBar($filename);
		$pdfResult->setPercentage(($result) ? round($result) : 0); //entweder die gerundete Zahl (0-100) oder 0 falls null
		
		$pdfResult->setText( $this->convertFromHtml( $text ) );
		
		//Smiley bauen: dem PdfResult Datei zuweisen und im FileSystem erzeugen!
		$filename = $this->getFileId().'.png';
		$this->imgFileNames[] = $filename;
		$this->createSmiley($filename, $result);
		$pdfResult->setSmiley($filename);

		//jetzt im Baum weiter absteigen auf Characteristic-Ebene: - aber nur, wenn der Test schon gemacht wurde...
		if ($surveyResult)
		{
			$charResults = $surveyResult->getCharResults();
			if ($charResults) $pdfResult->setSecondText($this->getConf()->getConfString('messages', 'charheader'));
			
			foreach ($surveyResult->getCharResults() as $charResult) $pdfResult->addPdfResult($this->getResultCharBars($charResult));
		}
		
		return $pdfResult;
	}
	
	/**
	 * Liefert ein PDFresult für eine Survey, die ihr Ergebnis als Rangfolge darstellt
	 * 
	 * @param Survey $survey Die darzustellende Survey
	 * @param String $title Titel des Übergeordneten Blocks
	 */
	private function getResultSurveyIcons(Survey $survey, $title) 
	{
		$attempt = $survey->getAttemptForUser($this, $this->getSess()->getUid());
		$completed = $attempt > 0;

		$pdfResult = new PdfResult();
		$pdfResult->setResultType(PdfResult::TYPE_SMILEYS);
		$pdfResult->setTitle($survey->getTitle());
		$pdfResult->setParentTitle($title);
		$pdfResult->setTocNumber($this->getNextTocNumber(1));
		
		$text = 'Für diesen Test liegt uns noch kein Ergebnis für Dich vor.';
		if ($completed) {
			$surveyResult = new SurveyResult($this, $survey, $this->getSess()->getUid(), $attempt); 
			$text = $surveyResult->getResultHeader();
			
			$text .= "\r\n\r\nIn diesem Test haben wir für Dich die folgende Rangfolge ermittelt:\r\n\r\n"; //Leerzeilen fürs PDF einfügen
			
			//jetzt lassen wir uns alle Merkmale in er ermittelten Rangfolge geben und teilen diese dem Nutzer mit:
			$characteristics = $surveyResult->getCharRanking();
			
			$i = 0; //Zähler
			foreach ($characteristics as $char) 
			{
				$i++;
				$text .= '('.$i.') '.$char->getTitle()."\r\n";
			}
			
			//zusätzlich: für die Übersicht Smiley-Darstellung:
			$characteristics = Characteristic::getForSurvey($this, $survey->getId());

			if ($characteristics != null) foreach ($characteristics as $char) 
			{
				$childResult = new PdfResult();
				$childResult->setIsCharacteristic(true);
				$childResult->setTitle($char->getTitle());
				
				$result = null;
				$attempt = $survey->getAttemptForUser($this, $this->getSess()->getUid());
				if ($survey->isCompleted($this, $this->getSess()->getUid()))
					$result = $survey->getResultForUserChar($this, $this->getSess()->getUid(), $char->getId(), $attempt);
				//grafik erzeugen:
				$filename = $this->getFileId().'.png';
				$this->imgFileNames[] = $filename;
				$this->createSmiley($filename, $result);
				$childResult->setSmiley($filename);
				
				//und das Ganze der entsprechenden Survey anhängen:
				$pdfResult->addPdfResult($childResult);
			}
			
		}
		
		$pdfResult->setText( $this->convertFromHtml($text) );		
		
		return $pdfResult;
	}
	
	/**
	 * Liefert ein PDFresult für ein Characteristic
	 * 
	 * @param CharResult $charResult das darzustellende CharResult
	 */
	private function getResultCharBars(CharResult $charResult) 
	{
		if (!$charResult->isVisible()) return new PdfResult();

		$pdfResult = new PdfResult();
		$pdfResult->setResultType(PdfResult::TYPE_BAR);
		$pdfResult->setTitle($charResult->getTitle());
		$pdfResult->setIsCharacteristic(true);
		$pdfResult->setTocNumber($this->getNextTocNumber(2));

		$result = $charResult->getResult();
		$pdfResult->setPercentage(($result) ? $result : 0); //entweder die gerundete Zahl (0-100) oder 0 falls null
		
		$pdfResult->setText( $this->convertFromHtml( $charResult->getResultText() ) );
		
		//Balkenbild: dem PdfResult Datei zuweisen und im FileSystem erzeugen!
		$filename = $this->getFileId().'.png';
		$this->imgFileNames[] = $filename;
		$this->imgCreator->showResultBar($charResult->getResultBarMinimum(), $charResult->getResultBarMaximum(), $result, true, $filename);
		$pdfResult->setBar($filename);
		
		//Smiley:
		$filename = $this->getFileId().'.png';
		$this->imgFileNames[] = $filename;
		$this->imgCreator->showSmiley($charResult->getSmileyAspect(), ImageCreator::BACKGROUND_WHITE, $filename);
		$pdfResult->setSmiley($filename);		

		return $pdfResult;
	}

	/**
	 * Ausführung: Business-Logik
	 */
	public function execute()
	{

	try {
		
		$this->imgCreator = new ImageCreator($this->getConf());
		$this->fileLocation = $this->getConf()->getConfString('ucGetPdf', 'filename', 'location');
		
		$pdf = new AdvisorPdf();
		
		//Werte setzen:
		$pdf->setUserName($this->getSess()->getUserData('username'));
		
		//für später: "einmalige" Grafik-Dateikennung setzen
		//einfach aus UID und aktueller Zeit bauen
		$this->setFileId($this->getSess()->getUid().'_'.time());

		//Unterblöcke / Surveys laden
		$blocks = SurveyBlock::getAll($this);
		$pdfResults = Array();

		foreach ($blocks as $block) $pdf->addPdfResult($this->getResultBlock($block));

		$pdfFileName = $this->fileLocation.
						$this->getConf()->getConfString('ucGetPdf', 'filename', 'prefix').
						$this->getSess()->getUserData('username').'_'.
						date('Y-m-d_H-i-s', time()).
						$this->getConf()->getConfString('ucGetPdf', 'filename', 'postfix');
		
		//PDF erzeugen:
		if (!$pdf->createPdf($pdfFileName)) throw new Exception('AdvisorPdf: Error creating PDF');

		//das Ganze als Email versenden:
		$mail = new htmlMimeMail5();
		$mail->setFrom($this->getConf()->getConfString('ucGetPdf', 'email', 'sender'));
		$mail->setSubject($this->getConf()->getConfString('ucGetPdf', 'email', 'subject'));
		$mail->addAttachment(new fileAttachment($pdfFileName));
		
		//Mailtext:
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucGetPdf', 'email', 'template'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucGetPdf', 'email', 'username'), $this->getSess()->getUserData('username'));
		
		$mail->setText($generator->getHTML());
		
		if (!$mail->send(array($this->getSess()->getUserData('username').' <'.$this->getSess()->getUserData('email').'>'))) throw new Exception('AdvisorPdf: Error sending eMail');
		
		//...und alle Dateien wieder löschen:
		foreach($this->imgFileNames as $imgFile) unlink($imgFile);
		unlink($pdfFileName);

	
		//HTML in den Output schreiben...
		$generator = new HtmlGenerator( $this->getConf()->getConfString('ucGetPdf', 'template', 'tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit den zu ersetzenden Anteilen...
		$generator->apply($this->getConf()->getConfString('ucGetPdf', 'template', 'email'), $this->getSess()->getUserData('email'));
		$generator->apply($this->getConf()->getConfString('ucGetPdf', 'template', 'backlink'), $this->getUsecaseLink('overview'));
		$this->appendOutput($generator->getHTML());
		$this->setOutputType(USECASE_HTML);
		
		return true;

		} catch (Exception $e) {
			$this->setError('ucGetPdf: Bei der Verarbeitung ist ein Fehler aufgetreten.<br/>'.$e->getMessage());
 			return false;
		}
	}
	
}
?>