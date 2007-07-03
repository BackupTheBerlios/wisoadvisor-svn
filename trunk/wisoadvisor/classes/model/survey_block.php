<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: survey_block.php
 * $Revision: 1.9 $
 * Erstellt am: 15.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
/**
 * Die Klasse SurveyBlock repräsentiert einen Block von Surveys, der auf der Übersichtsseite dargestellt wird.
 * Für einen Block kann wiederum ein kumuliertes Ergebnis bestimmt werden.
 * 
 * @author Michael Gottfried
 */
class SurveyBlock extends ModelHelper {
	
	const TYPE_BARS = 'bars';
	const TYPE_ICONS = 'icons';
	
	private $position = null;
	private $title = null;
	private $type = null;
	private $infotemplate = null;
	private $infotitle = null;
	
	private function __construct($blid) {
		parent::__construct($blid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enthält.
	 * @return SurveyBlock Das neu erzeugte SurveyBlock-Objekt.
	 */
	private function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen	
			$result = new SurveyBlock($row['blid']);
			$result->position = $row['position'];
			$result->title = $row['title'];
			$result->type = $row['type'];
			$result->infotemplate = $row['infotemplate'];
			$result->infotitle = $row['infotitle'];
		}
		// Objekt zurückliefern
		return $result;
	}
	
	/**
	 * Liefert ein SurveyBlock-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $blid Block-ID, die das gewünschte Objekt identifiert.
	 * @return SurveyBlock Das gewünschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForId(ModelContext $context, $blid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey_block', 'getForId'), Array($blid));
		if ($resultSet == false) 
			throw new ModelException("SurveyBlock::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert alle SurveyBlock-Objekte aus der Datenbank gelesen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return Array von SurveyBlock-Objekten
	 */
	public static function getAll(ModelContext $context) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey_block', 'getAll'), Array());
		if ($resultSet == false) 
			throw new ModelException("SurveyBlock::getAll: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getPosition() {
		return $this->position;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function getInfoTemplate() {
		return $this->infotemplate;
	}
	
	public function getInfoTitle() {
		return $this->infotitle;
	}
	
	public function isCompleted(ModelContext $context, $uid) {
		$surveys = Survey::getForBlock($context, $this->id);
		foreach ($surveys as $survey) {
			if (!$survey->isCompleted($context, $uid))
				return false;
		}
		return true;
	}
//	
//	/**
//	 * Liefert das Durchschnittsergebnis eines Benutzers in diesem SurveyBlock zurück.
//	 * 
//	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
//	 * @param int $uid Die UserId des Users, dessen Ergebnisse gewünscht werden
//	 * @return int Das Durchschnittsergebnis oder null, falls keine Ergebnisse vorliegen
//	 */
//	public function getMeanResultForUser(ModelContext $context, $uid) {
//		$surveys = Survey::getForBlock($context, $this->id);
//		$sumResult = 0;
//		$count = 0;
//		foreach ($surveys as $survey) {
//			$result = $survey->getMeanResultForUser($context, $uid);
//			if ($result != null) {
//				$sumResult += $result;
//				$count++;
//			}
//		}
//		if ($count == 0) return null;
//		return $sumResult/$count;
//	}
//	
//	/**
//	 * Liefert das Ergebnis eines Benutzers in diesem SurveyBlock zurück.
//	 * Dabei wird das Distanzmaß angewendet.
//	 * 
//	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
//	 * @param int $uid Die UserId des Users, dessen Ergebnisse gewünscht werden
//	 * @return int Das Durchschnittsergebnis oder null, falls keine Ergebnisse vorliegen
//	 */
//	public function getResultForUser(ModelContext $context, $uid) {
//		$surveys = Survey::getForBlock($context, $this->id);
//		foreach ($surveys as $survey) {
//			if (!$survey->isCompleted($context, $uid)) return null;
//		}
//		$characteristics = Characteristic::getForBlock($context, $this->getId());
//		if ($characteristics == null) return null;
//		$sum = 0;
//		$maxSum = 0;
//		foreach ($characteristics as $char) {
//			$result = $this->getResultForUserChar($context, $uid, $char->getId());
//			// Abweichung von erwarteten Minimun berechnen				
//			$abweichung = $char->getLowerTarget() - $result;
//			if ($abweichung < 0) $abweichung = 0; // Übererfüllung ignorieren
//			$sum += $abweichung * $abweichung; // Quadrate der Abweichung aufsummieren
//			// Bestimmung der maximalen Abweichnung für spätere Normierung
//			$maxAbweichung = $char->getlowerTarget() - 0;
//			$maxSum += $maxAbweichung * $maxAbweichung;
//		}
//		$distanzmass = sqrt($sum);
//		$maxDistanzmass = sqrt($maxSum);
//		$normDistanzmass = $distanzmass / $maxDistanzmass;
//		return 100 - ($normDistanzmass * 100);
//	}
//	
//	
//	/**
//	 * Liefert das Resultat, das ein Benutzers in diesem Umfrageblock erziehlt hat.
//	 * Falls die Umfrage noch nicht ausgefüllt wurde, wird eine ModelException geworfen.
//	 * 
//	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
//	 * @param int $uid ID des Benutzers, dessen Ergebnis gewünscht wird.
//	 * @param int $chid ID des Merkmals, dessen Ergebnis gewünscht wird.
//	 * @return int Rating von 0 bis 100
//	 */
//	public function getResultForUserChar(ModelContext $context, $uid, $chid) {
//		$surveys = Survey::getForBlock($context, $this->id);		
//		$result = 0;
//		$count = 0;
//		// Mittelwert des Resultats aus allen beteiligten Umfragen ermittelb
//		foreach ($surveys as $survey) {
//			$attempt = $survey->getAttemptForUser($context, $uid);
//			//echo 'SurveyBlock::getResultForUserChar: sid='.$survey->getId().', uid='.$uid.', chid='.$chid.', attempt='.$attempt;
//			$result += $survey->getResultForUserChar($context, $uid, $chid, $attempt);
//			//echo 'result='.$result.'<br>';
//			$count++;
//		}
//		if ($count == 0) 
//			return null;
//		return $result = $result / $count; 
//	}
	
	
	/**
	 * Liefert den nächsten SurveyBlock in der Bearbeitungsreihenfolge
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param SurveyBlock nächster SurveyBlock oder null, falls letzter
	 */
	public function getSuccessor(ModelContext $context) {
		$next = null;
		// Zuerst innerhalb des aktuellen SurveyBlock Nachfolger suchen:
		$surveyBlocks = SurveyBlock::getAll($context);
		foreach ($surveyBlocks as $surveyBlock) {
			if ($surveyBlock->getPosition() > $this->getPosition() and // gef. SurveyBlock kommt nach aktuellem
				($next == null or $next->getPosition() > $surveyBlock->getPosition())) // noch kein Erg. oder neuer Erg. besser
				$next = $surveyBlock;
		}
		return $next; 
	}
	
}
?>
