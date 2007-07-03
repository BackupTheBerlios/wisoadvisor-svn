<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: uc_char_result.php
 * $Revision: 1.6 $
 * Erstellt am: 09.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
 /**
  * Der CharResult-UseCase stellt das Ergebnis in einem einzelnen Merkmal dar.
  * @author Michael Gottfried
  */
 class ucCharResult extends UseCase {
 	
 	/**
 	 * Die ChId, auf die sich die Auswertung bezieht.
 	 */
 	private $chid = null; 
 	/**
 	 * Die SId, auf die sich die Auswertung bezieht.
 	 */
 	private $sid = null;
 	
 	public function setChId($chId) {
 		$this->chid = $chId;
 	}
 	
 	public function setSId($sId) {
 		$this->sid = $sId;
 	}
 	
 	public function execute() {
 		try {
 			$this->readParameters();
 			// MODELOBJEKTE LADEN
 			$characteristic = Characteristic::getForId($this, $this->chid);
 			if ($characteristic == null) { // Merkmal konnte nicht aus DB gelesen werden -> Fehler
 				$this->setError('Fehler: Merkmal mit der ID='.$this->chid.' wurde nicht gefunden.');
 				return false;
 			}
 			$survey = Survey::getForId($this, $this->sid);
 			if ($survey == null) { // Survey konnte nicht aus DB gelesen werden -> Fehler
 				$this->setError('Fehler: Survey mit der ID='.$this->sid.' wurde nicht gefunden.');
 				return false;
 			}
 			// RESULTAT ERMITTELN
 			$result = $survey->getResultForUserChar($this, $this->getSess()->getUid(), $characteristic->getId());
 			// TEXTELEMENT ERMITTELN
 			$rating = Rating::getForResult($this, $characteristic->getId(), 1, $result);
 			if ($rating == null)
 				$level = Rating::LEVEL_UNKNOWN;
 			else
 				$level = $rating->getLevel();
 			switch ($level) {
 				case Rating::LEVEL_POOR: 
 					$icon=ImageCreator::SMILEY_POOR;
 					break;
 				case Rating::LEVEL_AVERAGE: 
 					$icon=ImageCreator::SMILEY_AVERAGE;
 					break;
 				case Rating::LEVEL_GOOD: 
					$icon=ImageCreator::SMILEY_GOOD; 
 					break;
 				case Rating::LEVEL_UNKNOWN: 
					$icon=ImageCreator::SMILEY_UNKNOWN; 
 					break;
 			}
 			$smileyImage = $this->getUsecaseLink('graphics',ucGraphics::STEP_SMILEY, Array(ucGraphics::PAR_SMILEY_ASPECT.'='.$icon, ucGraphics::PAR_BACKGROUND.'='.ucGraphics::BACKGROUND_YELLOW));
 			
 			$min = $characteristic->getLowerTarget();
			$max = $characteristic->getUpperTarget();
			
			$ratingImage = $this->getUsecaseLink('graphics',ucGraphics::STEP_RESULTBAR, Array('minimum='.$min, 'maximum='.$max, 'result='.$result));
		
 			if ($rating == null) { // Textelement konnte nicht ermittelt werden
 				$this->setError('Fehler: Textelement konnte nicht ermittelt werden.');
 				return false;
 			}
 			$textelement = $rating->getTextElement($this);
 			$charHtml = $textelement->getContent();
 			// AUSGABE GENERIEREN
 			//zur Anzeige des Formulars wird der HTML-Generator benutzt
			$generator = new HtmlGenerator( $this->getConf()->getConfString('ucCharResult', 'char_result_tpl'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
			//befülle den Generator mit den zu ersetzenden Anteilen...
			$generator->apply('char_title', $characteristic->getTitle());
			//Gespeicherten Output aller Subusecases einfügen
			$generator->apply('char_result', $charHtml);
			$generator->apply('rating_image', $ratingImage);
			$generator->apply('char_result_value',  round($result));
			$generator->apply('char_icon', $smileyImage);
		
			//HTML in den Output schreiben...
			$this->appendOutput($generator->getHTML());
			$this->setOutputType(USECASE_HTML);
			return true;
 			
 		} catch (ModelException $e) {
 			$this->setError('Exception: '.$e->getMessage());
 			return false;
 		} catch (MissingParameterException $e) {
 			$this->setError($e->getMessage);
 			return false;
 		}
 	}
 	
 	/**
 	 * Liest die Parameter aus dem HTTP-Request ein, falls erforderlich.
 	 * Fehlen am Ende Parameter, wird eine Exception geworfen
 	 */
 	private function readParameters() {
 		// Falls die chid nicht gesetzt wurde, prüfen, ob sie als Parameter übergeben wurde.
 		if ($this->chid == null) 
 			$this->chid = $this->getParam()->getParameter('chid');

 		// Immer noch keine Chid -> Fehler
 		if ($this->chid == null) 
 			throw new MissingParameterException('Es wurde keine Characteristic-ID übergeben.');

 		// Falls die sid nicht gesetzt wurde, prüfen, ob sie als Parameter übergeben wurde.
 		if ($this->sid == null) 
 			$this->sid = $this->getParam()->getParameter('sid');

 		// Immer noch keine Sid -> Fehler
 		if ($this->sid == null) 
 			throw new MissingParameterException('Es wurde keine Survey-ID übergeben.');
 	}
 	
 }
?>
