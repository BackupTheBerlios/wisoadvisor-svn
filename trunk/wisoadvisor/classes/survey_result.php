<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: survey_result.php
 * $Revision: 1.11 $
 * Erstellt am: 21.06.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
 class SurveyResult {
 	
 	const CUT_MINIMUM = 0.33; // Bei der relativen Darstellung des Balkens als Vergleichswert, wieviele Ergebnisse von den schlechtesten ausklammern
	const CUT_MAXIMUM = 0.33;
	const CHID_BWL = 50; // Für Spezialfall in getCharRanking
 	
 	private $context = null;
	private $survey = null;
	private $uid = null;
	private $attempt = null;
	
	public function __construct(ModelContext $context, Survey $survey, $uid, $attempt) {
		$this->context = $context;
		$this->survey = $survey;
		$this->uid = $uid;
		$this->attempt = $attempt;
	}
 	
	/**
	 * Liefert das Ergebnis des Benutzers in der gesamten Survey.
	 * Dieses wird als Durchschnitt über alle in diesem Test geprüften
	 * Merkmale ermittelt.
	 * 
	 * @return int Durchschnitt über alle Merkmale
	 */
	public function getResult() {
		return round($this->survey->getResultForUser($this->context, $this->uid, $this->attempt));
	}
	
	/**
	 * Liefert eine Einstufung von 0 bis 100, die das relative Testergebnis in Vergleich
	 * zu allen anderen Teilnehmern darstellt. 100 entspricht dabei dem besten Ergebnis
	 * aller Teilnehmer, 0 dem schlechtesten.
	 * 
	 * @return int Eistufung von 0 bis 100
	 */
	public function getRanking() {
		return $this->survey->getUserRank($this->context, $this->uid, $this->attempt);
	}
	
	
	/**
 	 * Liefert den Einleitungsblock für die Auswertung
 	 * 
 	 * @return String
 	 */
 	public function getResultHeader() {
 		// RESULTAT ERMITTELN
		$result = $this->getResult();
		$characteristics = Characteristic::getForSurvey($this->context, $this->survey->getId());
		$charRanking = $this->getCharRanking();
		$ranking = $this->getRanking();
		$rankingText = '';
		if ($ranking >= 66.7) $rankingText = 'oberen';
		else if ($ranking >= 33.3) $rankingText = 'mittleren';
		else $rankingText = 'unteren';
		
		// DARAUS TEXTELEMENT ERMITTELN
		$textelement = $this->survey->getResultTextElement($this->context);
		if ($textelement == null) return null;
		// TODO: Auslagern, evtl. in den HTML-Generator
		$text = str_replace( $this->context->getConf()->getConfString('template', 'indicator', 'pre').'result'.$this->context->getConf()->getConfString('template', 'indicator', 'after'), $result, $textelement->getContent());
		$text = str_replace( $this->context->getConf()->getConfString('template', 'indicator', 'pre').'rankingText'.$this->context->getConf()->getConfString('template', 'indicator', 'after'), $rankingText, $text);

		// Ausfüllen der Felder: FIRST, UPPER, LOWER
		// Mittelwerte über alle Resultate ermitteln
		$average = $this->getAverageResult();
		$first = ''; $upper = null; $lower = null;
		foreach ($charRanking as $char) {
			$result = $this->survey->getResultForUserChar($this->context, $this->uid, $char->getId(), $this->attempt);		
			if ($first == '') $first = $char->getTitle();
			if ($result >= $average and $char->getTitle() != $first)
				$upper[] = $char->getTitle();
			if ($result < $average and $char->getTitle() != $first)
				$lower[] = $char->getTitle();
		}
		if ($lower == null) { // Alle gleich bewertet ->letzter Block leer
			// letzten aus upper in lower verschieben
			$lower[] = array_pop($upper);
		}
		$text = str_replace($this->context->getConf()->getConfString('template', 'indicator', 'pre').'first'.$this->context->getConf()->getConfString('template', 'indicator', 'after'), $first, $text);
		$text = str_replace($this->context->getConf()->getConfString('template', 'indicator', 'pre').'upper'.$this->context->getConf()->getConfString('template', 'indicator', 'after'), self::joinTexts($upper), $text);
		$text = str_replace($this->context->getConf()->getConfString('template', 'indicator', 'pre').'lower'.$this->context->getConf()->getConfString('template', 'indicator', 'after'), self::joinTexts($lower), $text);
		
		// Die Antworttexte einfügen		
		$answerTexts = $this->getSelectedAnswers();
		foreach ($answerTexts as $quid => $answer) {
			$text = str_replace($this->context->getConf()->getConfString('template', 'indicator', 'pre').'question'.$quid.$this->context->getConf()->getConfString('template', 'indicator', 'after'), $answer, $text);
		}
		
		// Die Regel für den Textblock mit gleichen 
		$ifequal = $this->context->getConf()->getConfString('template', 'indicator', 'pre').'ifequal'.$this->context->getConf()->getConfString('template', 'indicator', 'after');
		$else = $this->context->getConf()->getConfString('template', 'indicator', 'pre').'else'.$this->context->getConf()->getConfString('template', 'indicator', 'after');
		$endif = $this->context->getConf()->getConfString('template', 'indicator', 'pre').'endif'.$this->context->getConf()->getConfString('template', 'indicator', 'after');
		if (substr_count($text, $ifequal) == 1 and substr_count($text, $endif) == 1) 
			$text = self::handleIf($text, $ifequal, $else, $endif, $this->isCharsEqual());
		
		return $text;
 	}
 	
 	/**
 	 * Liefert alle CharResult-Objekte für alle der Survey zugeordneten Merkmale
 	 */
 	public function getCharResults() {
 		$result = null;
 		$characteristics = Characteristic::getForSurvey($this->context, $this->survey->getId());
 		foreach ($characteristics as $characteristic) {
 			$charResult = new CharResult($this->context, $this->survey, $characteristic, $this->uid, $this->attempt);
 			$result[] = $charResult;
 		}
 		return $result;
 	}
 	
	
	/**
 	 * Bring die Merkmale in eine Rangfolge, in Abhängigkeit vom Resultat des Users
 	 * Dabei wird bei Gleichwertigkeit BWL bevorzugt
 	 * 
 	 * @return Array Alle Merkmale nach absteigendem Ergebnis geordnet
 	 */
 	public function getCharRanking() {
		$characteristics = Characteristic::getForSurvey($this->context, $this->survey->getId());
		$ranking = null; // Rangfolge der Merkmale
		$topResult = 0; 
		foreach ($characteristics as $characteristic) {
			$result = $this->survey->getResultForUserChar($this->context, $this->uid, $characteristic->getId(), $this->attempt);
			$ranking[$characteristic->getId()] = $result;
			if ($result > $topResult)
				$topResult = $result; 
		}
		arsort($ranking);
		// Sonderfall: Die ersten n sind gleich, dann BWL bevorzugen.
		// Alle besten ermitteln, die ungefähr gleich gut sind.
		$topLevel = array(); 
		foreach ($ranking as $chid => $value) {
			if (round($value) == round($topResult))
				$topLevel[] = $chid;
		}
		if (in_array(self::CHID_BWL, $topLevel)) { // BWL dabei?
			$bwlArray = array(self::CHID_BWL => $topResult);
			// BWL entfernen
			$ranking = array_diff_key($ranking, $bwlArray);
			// und als erstes einfügen
			$ranking = $bwlArray + $ranking;
		}
		// Ende Sonderfall
		$result = null;
		foreach ($ranking as $chid => $value) {
			$result[] = Characteristic::getForId($this->context, $chid);
		}
		return $result;
 	}
 	
 	/**
 	 * Liefert den Durchschnitt über die Ergebnisse für alle Studiengänge.
 	 * Wird auf
 	 */
 	private function getAverageResult() {
 		$sum = 0;
 		$count = 0;
 		$characteristics = Characteristic::getForSurvey($this->context, $this->survey->getId());
 		foreach ($characteristics as $characteristic) {
			$result = round($this->survey->getResultForUserChar($this->context, $this->uid, $characteristic->getId(), $this->attempt));
			$sum += $result;
			$count++;
		}
		if ($count == 0) return 0;
		return $sum / $count;
 	}

	/**
	 * Prüft, ob der Benutzer in allen Merkmalen die gleiche Bewertung erzielt hat.
	 */ 	
 	private function isCharsEqual() {
 		$characteristics = Characteristic::getForSurvey($this->context, $this->survey->getId());
		$sum = 0; $count = 0; $retValue = true;
		foreach ($characteristics as $characteristic) {
			$result = $this->survey->getResultForUserChar($this->context, $this->uid, $characteristic->getId(), $this->attempt);
			//echo 'char='.$characteristic->getId().' => '.$result.'<br>';
			if ($count != null and ($sum/$count != $result)) $retValue = false;
			$sum += $result;
			$count++;
		}
		return $retValue;
 	}
 	
 	/**
 	 * Fügt Texte aneinander, so dass ein Aufzählung entsteht "A, B, C und D"
 	 * @param Array $texts Die Texte
 	 * @return String Zusammengefügte Texte
 	 */
 	private static function joinTexts($texts) {
 		if ($texts == null) return '';
 		$result = '';
 		$lastText = end($texts);
 		foreach ($texts as $text) {
 			if ($result == '') // 1. Element
 				$result = $text;
 			else if ($text == $lastText) // letztes Element
 				$result .= ' und '.$text;
 			else // 2. bis vorletztes Element
 				$result .= ', '.$text;
 		}
 		return $result;
 	}
 	
 	/**
 	 * Parst einen Text nach einem ##if## text1 ##else## text2 ##endif## Block
 	 * und ersetzt ihn in Abhängigkeit von Result durch einen der beiden Texte
 	 * 
 	 * @param String $text Der zu parsende Text
 	 * @param String $ifString Die If-Anweisung 
 	 * @param String $elseString Die else-Anweisung
 	 * @param String $endifString Die else-Anweisung
 	 * @param String $result ob die Bedingung erfüllt ist
 	 * @return String die angepasste Eingabe
 	 */
 	private static function handleIf($text, $ifString, $elseString, $endifString, $result) {
 		$before = substr($text, 0, strpos($text, $ifString)); // String davor ermitteln;
		//echo 'before='.$before.'<br>';
		$after = substr($text, strpos($text, $endifString) + strlen($endifString));
		//echo 'after='.$after.'<br>';
		// String zwischen ifequal und endif ermitteln:
		$rest = substr($text, strpos($text, $ifString) + strlen($ifString)); // Alles vorne weg 
		$rest = substr($rest, 0, strpos($rest, $endifString)); // endif und danach weg.
		//echo 'rest='.$rest.'<br>';
		// Text für wahr
		$true = $rest;
		// Text für falsch:
		$false = '';
		if (substr_count($rest, $elseString) == 1) { // Kommt else vor?
			$true = substr($rest, 0, strpos($rest, $elseString));
			$false = substr($rest, strpos($rest, $elseString) + strlen($elseString));
		}
		//echo 'true='.$true.'<br>';
		//echo 'false='.$false.'<br>';
		// Bedingung prüfen
		
		if ($result) // Bedingung erfüllt
			return $before.$true.$after;
		else
			return $before.$false.$after;
 	}
 	
 	
 	/**
 	 * Liefert ein Array, das für jede Frage, die zum keinem Merkmal gehört, alle von Benutzer
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
 			if ($question->getChId() == null) {
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
}
?>
