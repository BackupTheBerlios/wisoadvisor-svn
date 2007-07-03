<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: char_result.php
 * $Revision: 1.6 $
 * Erstellt am: 21.06.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
class CharResult {
	
	const CUT_MINIMUM = 0.1; // Bei der relativen Darstellung des Balkens als Vergleichswert, wieviele Ergebnisse von den schlechtesten ausklammern
	const CUT_MAXIMUM = 0.1;
	
	private $context = null;
	private $survey = null;
	private $characteristic = null;
	private $uid = null;
	private $attempt = null;
	private $resultBarMin = null;
	private $resultBarMax = null;
	
	public function __construct(ModelContext $context, Survey $survey, Characteristic $characteristic, $uid, $attempt) {
		$this->context = $context;
		$this->survey = $survey;
		$this->characteristic = $characteristic;
		$this->uid = $uid;
		$this->attempt = $attempt;
		$this->calculateStatistics();
	}
	
	/**
	 * Liefert das Ergebnis des Benutzers in diesem Merkmal.
	 */
	public function getResult() {
		return round($this->survey->getResultForUserChar($this->context, $this->uid, $this->characteristic->getId(), $this->attempt));
	}
	
	/**
	 * Liefert die Einstufung basierend auf dem Ergebnis
	 * 
	 * @return Rating Objekt
	 */
	public function getRating() {
		$result = $this->getResult(); 
		$average = $this->survey->getAverageResultForChar($this->context, $this->characteristic->getId(), 1); // immer mit den Erstversuchen vergleichen
		return Rating::getForResult($this->context, $this->characteristic->getId(), 1, $result, $average);
	}
	
	public function getTitle() {
		return $this->characteristic->getTitle();
	}
	
	
 	/**
 	 * Liefert den Text für das Merkmalsergebnis
 	 */
 	public function getResultText() {
 		$result = $this->getResult(); 
 		$avgResult = round($this->survey->getAverageResultForChar($this->context, $this->characteristic->getId(), 1));
		$rating = $this->getRating();
		if ($rating == null)  // Textelement konnte nicht ermittelt werden
			throw new ModelException('Fehler: Textelement konnte nicht ermittelt werden. Rating für '.$this->characteristic->getId().' nicht vorhanden.');
		
		$textelement = $rating->getTextElement($this->context);
		
		$charHtml = $textelement->getContent();
		// TODO: Auslagern, evtl. in den HTML-Generator
		$charHtml = str_replace($this->context->getConf()->getConfString('template', 'indicator', 'pre').'result'.$this->context->getConf()->getConfString('template', 'indicator', 'after'), $result, $charHtml);
		$charHtml = str_replace($this->context->getConf()->getConfString('template', 'indicator', 'pre').'average'.$this->context->getConf()->getConfString('template', 'indicator', 'after'), $avgResult, $charHtml);
		// Gewählte Antworten in die Ausgabe einfügen
		$answerTexts = $this->getSelectedAnswers();
		foreach ($answerTexts as $quid => $text) {
			$charHtml = str_replace($this->context->getConf()->getConfString('template', 'indicator', 'pre').'question'.$quid.$this->context->getConf()->getConfString('template', 'indicator', 'after'), $text, $charHtml);
		}
		return $charHtml;
 	}
 	
 	
 	/**
 	 * Liefert das Smiley-Aussehen für das Merkmalsergebnis
 	 */
 	public function getSmileyAspect() {
 		$rating = $this->getRating(); 
 		$level = Rating::LEVEL_UNKNOWN;
		if ($rating != null) $level = $rating->getLevel();
		switch ($level) {
 			case Rating::LEVEL_POOR: 
 				return ImageCreator::SMILEY_POOR;
 			case Rating::LEVEL_AVERAGE: 
 				return ImageCreator::SMILEY_AVERAGE;
 			case Rating::LEVEL_GOOD: 
				return ImageCreator::SMILEY_GOOD; 
 			case Rating::LEVEL_UNKNOWN: 
				return ImageCreator::SMILEY_UNKNOWN; 
 		}
 	}
 	
 	
 	/**
 	 * Liefert das Minimum des Ergebnisbalkens für das Merkmal
 	 */
 	public function getResultBarMinimum() {
		return $this->resultBarMin;
 	}
 	
 	/**
 	 * Liefert das Maximum des Ergebnisbalkens für das Merkmal
 	 */
 	public function getResultBarMaximum() {
		return $this->resultBarMax;
 	}
 	
 	/**
 	 * Prüft, ob das Merkmal überhaupt dargestellt werden soll.
 	 */
 	public function isVisible() {
 		switch ($this->characteristic->getShowResult()) {
			case Characteristic::SHOW_RESULT_NO:
				return false;
				
			case Characteristic::SHOW_RESULT_ABSOLUTE:
			case Characteristic::SHOW_RESULT_RELATIVE:
			case Characteristic::SHOW_RESULT_TEXT_ONLY:
				return true;
		}
 	}
 	
 	/**
 	 * Prüft, ob der Ergebnisbalken angezeigt werden soll.
 	 */
 	public function isResultBarVisible() {
 		switch ($this->characteristic->getShowResult()) {
			case Characteristic::SHOW_RESULT_NO:
			case Characteristic::SHOW_RESULT_TEXT_ONLY:
				return false;
				
			case Characteristic::SHOW_RESULT_ABSOLUTE:
			case Characteristic::SHOW_RESULT_RELATIVE:
				return true;
		}
 	}
 	
 	
 	/**
 	 * Liefert ein Array, das für jede Frage, die zum gewählten Merkmal gehört, alle von Benutzer
 	 * gewählten Antworten als konkatenierten, mit Kommas getrennten String enthält.
 	 * 
 	 * @param Survey $survey
 	 * @param Characteristic $characteristic
 	 * @param int $attempt
 	 * @return Array Form: quid -> antworten
 	 */
 	private function getSelectedAnswers() {
		$result = null; 		
 		$questions = Question::getForSurvey($this->context, $this->survey->getId());
 		foreach ($questions as $question) {
 			if ($question->getChId() == $this->characteristic->getId()) {
 				$answers = Answer::getForQuestionUser($this->context, $question->getId(), $this->uid, $this->attempt);
 				$answerText = '';
 				if ($answers != null) {
 					 foreach ($answers as $answer) {
 					 	if ($answerText != '')
 					 		$answerText .= ', '.$answer->getAnswer();
 					 	else 
 					 		$answerText = $answer->getAnswer();
 					 }
 				}
 				$result[$question->getId()] = $answerText;
 			}
 		}
 		return $result;
 	}
 	
 	private function calculateStatistics() {
 		switch ($this->characteristic->getShowResult()) {
			case Characteristic::SHOW_RESULT_NO:
			case Characteristic::SHOW_RESULT_TEXT_ONLY:
				break;
				
			case Characteristic::SHOW_RESULT_ABSOLUTE:
				$this->resultBarMin = $this->characteristic->getLowerTarget();
				$this->resultBarMax = $this->characteristic->getUpperTarget();
				break;
				
			case Characteristic::SHOW_RESULT_RELATIVE:
				$ratings = $this->survey->getResultsForChar($this->context, $this->characteristic->getId(), 1); // immer mit den Erstversuchen vergleichen
				//asort($ratings); // Aufsteigend sortieren
				// 10% ermitteln
				$cut = round(self::CUT_MINIMUM * count($ratings));
				// die ersten 10% überspringen
				reset($ratings);
				for ($i=1; $i<=$cut; $i++) next($ratings);
				$this->resultBarMin = current($ratings);
				// die letzten 10% überspringen
				end($ratings);
				for ($i=1; $i<=$cut; $i++) prev($ratings);
				$this->resultBarMax = current($ratings);
				break;
		}
 	}
}
?>
