<?php
/***********************************************************************************
 * WiSo@visor - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de
 * 
 * PdfResult repräsentiert ein Testergebnis mit allen benötigten Attributen
 * durch die rekursive Verschachtelung kann ein Ergebnisbaum aufgebaut werden
 * ein PdfResult wird zur Generierung des Ergebnis-PDFs benutzt
 *
 * Datei: advisor_pdf_result.php
 * Erstellt am: 19.06.2006
 * Erstellt von: flo
 ***********************************************************************************/
 
 class PdfResult {
 	
 	const TYPE_BAR = 1;
 	const TYPE_SMILEYS = 2;
 	
 	private $resultType;
 	private $isCharacteristic = false;
 	private $isBlock = false;
 	private $title;
 	private $percentage;
 	private $text;
 	private $secondText;
 	private $pictureSmiley;
 	private $picturePercentageBar;
 	private $smileyArray = Array();
 	private $pdfResultArray = Array();
 	private $pdfInternalLink;
 	private $pdfPageIdentifier;
 	private $parentTitle;
 	private $tocNumber;
 	
 	/**
 	 * setzt den Typ des Results - akzeptiert einen der oben als Konstanten definierten Typen
 	 * @param $value der entsprechende Wert
 	 * @throws ModelException
 	 */
 	public function setResultType($value)
 	{
 		if (($value==PdfResult::TYPE_BAR) || ($value==PdfResult::TYPE_SMILEYS)) $this->resultType = $value;
 		else throw new ModelException('PdfResult->setResultType: unknown type');	
 	}
 	
 	/**
 	 * setzt den Characteristic-Typ des Results - definiert, ob das Result schon die unterste Ebene (characteristic) ist
 	 * @param $value boolean true oder false
 	 * @throws ModelException
 	 */
 	public function setIsCharacteristic($value)
 	{
 		if ($value) $this->isCharacteristic = true;
 		else $this->isCharacteristic = false;
 	}
 	
 	/**
 	 * setzt den Block-Typ des Results - definiert, ob das Result die oberste Ebene (block) ist
 	 * @param $value boolean true oder false
 	 * @throws ModelException
 	 */
 	public function setIsBlock($value)
 	{
 		if ($value) $this->isBlock = true;
 		else $this->isBlock = false;
 	}
 	
 	/**
 	 * setzt den Titel des Results
 	 * @param $value der entsprechende Wert
 	 * @throws ModelException
 	 */
 	public function setTitle($value)
 	{
 		if (((string) $value) || ($value == null)) $this->title = (string) $value;
 		else throw new ModelException('PdfResult->setTitle: string necessary');
 	}
	
 	/**
 	 * setzt den ParentTitel des Results
 	 * @param $value der entsprechende Wert
 	 * @throws ModelException
 	 */
 	public function setParentTitle($value)
 	{
 		if (((string) $value) || ($value == null)) $this->parentTitle = (string) $value;
 		else throw new ModelException('PdfResult->setParentTitle: string necessary');
 	}
	
 	/**
 	 * setzt die Inhaltsverzeichnis-Nummer des Results
 	 * @param $value der entsprechende Wert
 	 * @throws ModelException
 	 */
 	public function setTocNumber($value)
 	{
 		if (((string) $value) || ($value == null)) $this->tocNumber = (string) $value;
 		else throw new ModelException('PdfResult->setTocNumber: string necessary');
 	}
	
 	/**
 	 * setzt die erreichte Prozentzahl, ganzzahlig auf einer Skala von 0-100
 	 * @param $value der entsprechende Wert
 	 * @throws ModelException
 	 */
 	public function setPercentage($value)
 	{
 		if (((float) $value) || ($value==0)) $this->percentage = (float) $value;
 		else throw new ModelException('PdfResult->setPercentage: float necessary - but got: '.$value);
 	}
	
 	/**
 	 * setzt einen internen LinkIdentifier zur PDF-ERzeugung
 	 * @param $value der entsprechende Wert
 	 */
 	public function setPdfInternalLink($value)
 	{
 		$this->pdfInternalLink = $value;
 	}
	
 	/**
 	 * setzt einen internen Identifier für die Seitenzahl zur PDF-ERzeugung
 	 * @param $value der entsprechende Wert
 	 */
 	public function setPdfPageIdentifier($value)
 	{
 		$this->pdfPageIdentifier = $value;
 	}
	
 	/**
 	 * setzt den Ergebnistext
 	 * @param $value der entsprechende Wert
 	 * @throws ModelException
 	 */
 	public function setText($value)
 	{
 		if (((string) $value) || ($value == null)) $this->text = (string) $value;
 		else throw new ModelException('PdfResult->setText: string necessary');
 	}
	
 	/**
 	 * setzt den Ergebnistext2
 	 * @param $value der entsprechende Wert
 	 * @throws ModelException
 	 */
 	public function setSecondText($value)
 	{
 		if (((string) $value) || ($value == null)) $this->secondText = (string) $value;
 		else throw new ModelException('PdfResult->setSecondText: string necessary');
 	}
	
 	/**
 	 * setzt den Link auf die hinterlegte Smiley-Datei
 	 * @param $value der entsprechende Wert
 	 * @throws ModelException
 	 */
 	public function setSmiley($value)
 	{
 		if ((string) $value) $this->pictureSmiley = (string) $value;
 		else throw new ModelException('PdfResult->setSmiley: string necessary');
 	}
	
 	/**
 	 * setzt den Link auf die hinterlegte Balken-Datei
 	 * @param $value der entsprechende Wert
 	 * @throws ModelException
 	 */
 	public function setBar($value)
 	{
 		if ((string) $value) $this->picturePercentageBar = (string) $value;
 		else throw new ModelException('PdfResult->setBar: string necessary');
 	}
	
 	/**
 	 * fügt für den Typ 'smileys' einen Smiley unter verwendung eines Schlüssels hinzu - 
 	 * der Schlüssel ist gleichzeitig der "Spaltenname" der Ergebnisrangfolge
 	 * @param $key der Schlüssel
 	 * @param $value der entsprechende Wert
 	 * @throws ModelException
 	 */
 	public function addResultSmiley($key, $value)
 	{
 		if (((string) $value) && ((string) $key)) $this->smileyArray[(string) $key] = $value;
 		else throw new ModelException('PdfResult->addResultSmiley: strings necessary');
 	}

 	/**
 	 * fügt ein Result-Kindobjekt hinzu 
 	 * @param $pdfResult ein PdfResult-Objekt
 	 * @throws ModelException
 	 */
 	public function addPdfResult(PdfResult $pdfResult)
 	{
 		try {
 			$this->pdfResultArray[] = $pdfResult;
 		} 
 		catch (Exception $e)
 		{
 			throw new ModelException('PdfResult->addPdfResult: cant add child result');
 		}
 	}

	/**
	 * Getter-Methoden
	 */
	public function getResultType()
	{
		return $this->resultType;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function getParentTitle()
	{
		return $this->parentTitle;
	}
	
	public function getPercentage()
	{
		return $this->percentage;
	}
	
	public function getText()
	{
		return $this->text;
	}
	
	public function getSecondText()
	{
		return $this->secondText;
	}
	
	public function getSmiley()
	{
		return $this->pictureSmiley;
	}
	
	public function getBar()
	{
		return $this->picturePercentageBar;
	}
	
	public function getAllResultSmileys()
	{
		return $this->smileyArray;
	}
	
	public function getAllPdfResults()
	{
		return $this->pdfResultArray;
	}
	
	public function isCharacteristic()
	{
		return $this->isCharacteristic;
	}	
	
	public function isBlock()
	{
		return $this->isBlock;
	}	
	
	public function getPdfInternalLink()
	{
		return $this->pdfInternalLink;	
	}

	public function getTocNumber()
	{
		return $this->tocNumber;	
	}

	public function getPdfPageIdentifier()
	{
		return $this->pdfPageIdentifier;	
	}
}
?>
