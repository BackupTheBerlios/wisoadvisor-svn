<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakult�t
 * (c) 2006 Lehrstuhl f�r Wirtschaftsinformatik 3, Uni Erlangen-N�rnberg
 * R�ckfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: survey.php
 * $Revision: 1.29 $
 * Erstellt am: 15.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
/**
 * Die Klasse Survey repr�sentiert alle Informationen f�r eine Befragung/Test
 * 
 * @author Michael Gottfried
 */
class Survey extends ModelHelper {
	
	private $title = null;
	private $blid = null;
	private $position = null;
	private $gid = null;
	private	$startpage = null;
	private	$endpage = null;
	private $whypage = null;
	private $resultTeId = null;
	
	private function __construct($sid) {
		parent::__construct($sid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enth�lt.
	 * @return Survey Das neu erzeugte Survey-Objekt.
	 */
	private static function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen	
			$result = new Survey($row['sid']);
			$result->title = $row['title'];
			$result->blid = $row['blid'];
			$result->position = $row['position'];
			$result->gid = $row['gid'];
			$result->startpage = $row['startpage'];
			$result->endpage = $row['endpage'];
			$result->whypage = $row['helptext'];
			$result->resultTeId = $row['teid'];
		}
		// Objekt zur�ckliefern
		return $result;
	}
	
	/**
	 * Liefert ein Survey-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $sid Survey-ID, die das gew�nschte Objekt identifiert.
	 * @return Survey Das gew�nschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForId(ModelContext $context, $sid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getForId'), Array($sid));
		if ($resultSet == false) 
			throw new ModelException("Survey::getForId: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert alle Survey-Objekte aus der Datenbank.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return Array von Survey-Objekten
	 */
	public static function getAll(ModelContext $context) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getAll'), Array());
		if ($resultSet == false) 
			throw new ModelException("Survey::getAll: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	/**
	 * Liefert alle Survey-Objekte aus der Datenbank, die zu einem SurveyBlock geh�ren.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $blid Die ID des Blockes, zu dem die Surveys geh�ren m�ssen.
	 * @return Array von Survey-Objekten
	 */
	public static function getForBlock(ModelContext $context, $blid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getForBlock'), Array($blid));
		if ($resultSet == false) 
			throw new ModelException("Survey::getForBlock: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	/**
	 * Liefert alle Survey-Objekte aus der Datenbank, die zu einer Group geh�ren.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $gid Die ID der Group, zu der die Surveys geh�ren m�ssen.
	 * @return Array von Survey-Objekten
	 */
	public static function getForGroup(ModelContext $context, $gid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getForGroup'), Array($gid));
		if ($resultSet == false) 
			throw new ModelException("Survey::getForGroup: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	
	/**
	 * Liefert due ERSTE Umfrage in einem Umfrage-Block.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $blid Die ID des Umfrageblocks, dessen Umfragen gew�nscht werden.
	 * @return Survey Erste Survey im Block
	 */
	public static function getFirstForSurveyBlock(ModelContext $context, $blid) {
		$result = null;		
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getFirstForSurveyBlock'), Array($blid));
		if ($resultSet == false) 
			throw new ModelException("Survey::getFirstForSurveyBlock: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		if (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result = self::getForDBRow($row);
		}
		return $result;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getBlId() {
		return $this->blid;
	}
	
	public function getBlock(ModelContext $context) {
		return SurveyBlock::getForId($context, $this->blid);
	}
	
	public function getPosition() {
		return $this->position;
	}
	
	public function getGId() {
		return $this->gid;
	}
	
	public function getGroup(ModelContext $context) {
		return Group::getForId($context, $this->gid);
	}
	
	public function getStartpage() {
		return $this->startpage;
	}
	
	public function getEndpage() {
		return $this->endpage;
	}
	
	public function getWhypage() {
		return $this->whypage;
	}
	
	public function getResultTeId() {
		return $this->resultTeId;
	}
	
	public function getResultTextElement(ModelContext $context) {
		if ($this->resultTeId == 'null' or $this->resultTeId == '') return null;
		return TextElement::getForId($context, $this->resultTeId);
	}
	
	/**
	 * Pr�ft, ob eine Umfrage von einem Benutzer bereits ausgef�llt wurde.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid ID des Benutzers, dessen Ergebnis gew�nscht wird.
	 * @param int $attempt Der Ausf�llversuch (optional)
	 * @return boolean True, falls die Umfrage bereits ausgef�llt wurde, sonst false
	 */
	public function isCompleted(ModelContext $context, $uid, $attempt = null) {
		//wenn keine uid angegeben wurde, dann muss nat�rlich false zur�ckgegeben werden - gilt f�r nicht angemeldete User
		if ($uid == null) return false;
		$maxAttempt = $this->getAttemptForUser($context, $uid);
		if ($attempt == null) return $maxAttempt > 0;
		return $maxAttempt >= $attempt;
	}


	/**
	 * Liefert das Resultat, das ein Benutzers in dieser Umfrage erziehlt hat.
	 * Falls die Umfrage noch nicht ausgef�llt wurde, wird eine ModelException geworfen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid ID des Benutzers, dessen Ergebnis gew�nscht wird.
	 * @param int $chid ID des Merkmals, dessen Ergebnis gew�nscht wird.
	 * @param int $attempt Der Ausf�llversuch
	 * @return int Result von 0 bis 100
	 */
	public function getResultForUserChar(ModelContext $context, $uid, $chid, $attempt) {
		// Shortcut: Wenn Ergebnis schon da, direkt aus DB lesen.
		$dbResult = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getResultForUserChar'), Array($uid, $this->id, $chid, $attempt));
		if ($dbResult == false) 
			throw new ModelException("Survey::getResultForUserChar: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		if (($row = $context->getDb()->fetch_array($dbResult)) != false) 
			return $row['result'];
		// Kein Eintrag, Ergebnis neu berechnen (langsam):
		return $this->calculateResultForUserChar($context, $uid, $chid, $attempt);
	}
	
	
	/**
	 * Liefert das Resultat, das ein Benutzers in dieser Umfrage erziehlt hat.
	 * Falls die Umfrage noch nicht ausgef�llt wurde, wird eine ModelException geworfen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid ID des Benutzers, dessen Ergebnis gew�nscht wird.
	 * @param int $chid ID des Merkmals, dessen Ergebnis gew�nscht wird.
	 * @param int $attempt Der Ausf�llversuch
	 * @return int Result von 0 bis 100
	 */
	private function calculateResultForUserChar(ModelContext $context, $uid, $chid, $attempt) {
		$result = 0;
		$count = 0;
		//Alle Fragen f�r diese Umfrage ermitteln
		$questions = Question::getForSurvey($context, $this->getId());
		foreach ($questions as $question) {
			// Ist Frage bewertungsrelevant und geh�rt sie zur gesuchten Characteristic?
			if ($question->isRated($context) and $question->getChId()==$chid) {
				$result += $question->getResultForUser($context, $uid, $attempt);
				$count++;
			}
		}
		if ($count == 0) return null; // keine Werte f�r dieses Merkmal gefunden
		return $result/$count;
	}


	/**
	 * Liefert das Durchschnittsergebnis aller Benutzer in diesem Merkmal
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $chid ID des Merkmals, dessen Ergebnis gew�nscht wird.
	 * @param int $attempt Der Ausf�llversuch
	 * @return int Result von 0 bis 100
	 */
	public function getAverageResultForChar(ModelContext $context, $chid, $attempt) {
		$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getAverageResultForChar'), Array($this->id, $chid, $attempt));
		if ($result == false) 
			throw new ModelException("Survey::getAverageResultForChar: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		if (($row = $context->getDb()->fetch_array($result)) != false) 
			return $row['averagerating'];
		throw new ModelException("Survey::getAverageResultForChar: Es konnte kein Ergebnis ermittelt werden f�r chid=$chid und attempt=$attempt", 0);
	}
	
//	
//	/**
//	 * Liefert das Durchschnittsergebnis aller Benutzer in dieser Survey
//	 * 
//	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
//	 * @param int $attempt Der Ausf�llversuch
//	 * @return int Result von 0 bis 100
//	 */
//	public function getAverageResult(ModelContext $context, $attempt) {
//		$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getAverageResult'), Array($this->id, $attempt));
//		if ($result == false) 
//			throw new ModelException("Survey::getAverageResult: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
//		if (($row = $context->getDb()->fetch_array($result)) != false) 
//			return $row['averageresult'];
//		else
//			return null;
//	}
	

//	public function getAverageResultForChar(ModelContext $context, $chid, $attempt) {
//		$result = 0;
//		$count = 0;
//		//Alle Fragen f�r diese Umfrage ermitteln
//		$questions = Question::getForSurvey($context, $this->getId());
//		foreach ($questions as $question) {
//			// Ist Frage bewertungsrelevant und geh�rt sie zur gesuchten Characteristic?
//			if ($question->isRated($context) and $question->getChId()==$chid) {
//				$result += $question->getAverageResult($context, $attempt);
//				$count++;
//			}
//		}
//		return $result/$count;
//	}
	
	/**
	 * Liefert alle Ergebnisse eines Benutzers in dieser Survey zur�ck.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die UserId des Users, dessen Ergebnisse gew�nscht werden
	 * @param int $attempt Der Ausf�llversucht
	 * @return Array Ein Array mit allen Ergebnissen in der Form (chId->Ergebnis)
	 */
	public function getResultsForUser(ModelContext $context, $uid, $attempt) {
		$results = null;
		$characteristics = Characteristic::getForSurvey($context, $this->id);
		if ($characteristics == null) return null;
		foreach ($characteristics as $characteristic) {
			$result = $this->getResultForUserChar($context, $uid, $characteristic->getId(), $attempt);
			$results[$characteristic->getId()] = $result;
		}
		return $results;
	}
	
	

	/**
	 * Liefert alle Ergebnisse f�r ein Merkmal in dieser Survey zur�ck.
	 * Nutzt dabei die vorberechten und gespeicherten Ergebnisse in der Datenbank.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $chid ID des Merkmals, dessen Ergebnis gew�nscht wird.
	 * @param int $attempt Der Ausf�llversucht
	 * @return Array Ein Array mit allen Ergebnissen
	 */
	public function getResultsForChar(ModelContext $context, $chid, $attempt) {
		$returnValue = null;
		$dBResult = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getResultsForChar'), Array($this->id, $chid, $attempt));
		if ($dBResult == false) 
			throw new ModelException("Survey::getResultsForChar: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($dBResult)) != false) {
			$result = $row['result'];
			$uid = $row['uid'];
			$returnValue[$uid] = $result;
		}
		return $returnValue;
	}

//	/**
//	 * Liefert das Durchschnittsergebnis eines Benutzers in dieser Survey zur�ck.
//	 * 
//	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
//	 * @param int $uid Die UserId des Users, dessen Ergebnisse gew�nscht werden
//	 * @return int Das Durchschnittsergebnis oder null, falls keine Ergebnisse vorliegen
//	 */
//	public function getMeanResultForUser(ModelContext $context, $uid) {
//		if (!$this->isCompleted($context, $uid)) 
//			return null;
//		$results = $this->getResultsForUser($context, $uid);
//		if ($results == null)
//			return null;
//		$sumResult = 0;
//		$count = 0;
//		foreach ($results as $result) {
//			$sumResult += $result;
//			$count++;
//		}
//		return $sumResult/$count;
//	}
	
	/**
	 * Liefert das Ergebnis eines Benutzers in dieser Survey zur�ck.
	 * Dabei wird der Durchschnitt angewendet.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die UserId des Users, dessen Ergebnisse gew�nscht werden
	 * @param int $attempt Der Ausf�llversuch
	 * @return int Das Durchschnittsergebnis oder null, falls keine Ergebnisse vorliegen
	 */
	public function getResultForUser(ModelContext $context, $uid, $attempt) {
		$dBResult = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getResultForUser'), Array($uid, $this->id, $attempt));
		if ($dBResult == false) 
			throw new ModelException("Survey::getResultForUser: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		if (($row = $context->getDb()->fetch_array($dBResult)) != false) 
			return $row['result'];
		// Nichts gespeichert, berechnen:
		return $this->calculateResultForUser($context, $uid, $attempt);
	}
	
	private function  calculateResultForUser(ModelContext $context, $uid, $attempt) {
		$results = $this->getResultsForUser($context, $uid, $attempt);$results = null;
		$characteristics = Characteristic::getForSurvey($context, $this->id);
		if ($characteristics == null) return null;
		foreach ($characteristics as $characteristic) {
			$result = $this->getResultForUserChar($context, $uid, $characteristic->getId(), $attempt);
			$results[$characteristic->getId()] = $result;
		}
		return array_sum($results) / count($results);
	}
	
	/**
	 * Liefert den prozentualen Rang des Benutzers verglichen mit allen anderen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die UserId des Users, dessen Ergebnisse gew�nscht werden
	 * @param int $attempt Der Ausf�llversuch
	 * @return int der Rang von 0 bin 100 in Prozent
	 */
	public function getUserRank(ModelContext $context, $uid, $attempt) {
		$result = $this->getResultForUser($context, $uid, $attempt);
		$dBResult = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getUserRank'), Array($this->id, $result));
		if ($dBResult == false) 
			throw new ModelException("Survey::getUserRank: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		if (($row = $context->getDb()->fetch_array($dBResult)) != false) 
			return (int) $row['rank'];
		throw new ModelException("Survey::getUserRank: Konnte kein Ergebnis ermitteln, es fehlen Daten in der Datenbank", 0);
	}
	
//	/**
//	 * Liefert das Ergebnis eines Benutzers in dieser Survey zur�ck.
//	 * Dabei wird das Distanzma� angewendet.
//	 * 
//	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
//	 * @param int $uid Die UserId des Users, dessen Ergebnisse gew�nscht werden
//	 * @param int $attempt Der Ausf�llversuch
//	 * @return int Das Durchschnittsergebnis oder null, falls keine Ergebnisse vorliegen
//	 */
//	public function getDistanceResultForUser(ModelContext $context, $uid, $attempt) {
//		$results = $this->getResultsForUser($context, $uid, $attempt);
//		if ($results == null)
//			return null;
//		$sum = 0; 
//		$maxSum = 0;
//		foreach ($results as $chid => $result) {
//			$char = Characteristic::getForId($context, $chid);
//			// Abweichung von erwarteten Minimun berechnen				
//			$abweichung = $char->getlowerTarget() - $result;
//			if ($abweichung < 0) $abweichung = 0; // �bererf�llung ignorieren
//			$sum += $abweichung * $abweichung; // Quadrate der Abweichung aufsummieren
//			// Bestimmung der maximalen Abweichnung f�r sp�tere Normierung
//			$maxAbweichung = $char->getlowerTarget() - 0;
//			$maxSum += $maxAbweichung * $maxAbweichung;
//		}
//		$distanzmass = sqrt($sum);
//		$maxDistanzmass = sqrt($maxSum);
//		if ($maxDistanzmass == 0) // keine Werte -> kein Ergebnis 
//			return null;
//		$normDistanzmass = $distanzmass / $maxDistanzmass;
//		return 100 - ($normDistanzmass * 100);
//	}
	
	/**
	 * Liefert die Gesamtergebnisse aller Benutzer f�r diese Umfrage.
	 * Diese werden nicht berechnet, sondern direkt aus der Datenbank gelesen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $attempt Der Ausf�llversuch
	 * @return Array mit allen Ergebnissen: uid => result
	 */
	public function getAllResults(ModelContext $context, $attempt) {
		$returnValue = null;
		$dbResult = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getAllResults'), Array($this->id, $attempt));
		if ($dbResult == false) 
			throw new ModelException("Survey::getAllResults: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($dbResult)) != false) {
			$uid = $row['uid'];
			$result = $row['result'];
			$returnValue[$uid]=$result;
		}
		return $returnValue;
	}
	
//	private function calculateAllResults(ModelContext $context, $attempt) {
//		$returnValue = null;
//		$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getAllResults'), Array($this->id, $attempt));
//		if ($result == false) 
//			throw new ModelException("Survey::getAllResults: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
//		$sum = null;
//		$maxSum = null;
//		while (($row = $context->getDb()->fetch_array($result)) != false) {
//			$rating = $row['rating'];
//			$uid = $row['uid'];
//			$chid = $row['chid'];
//			$lowerTarget = $row['lower_target'];
//			$abweichung = $lowerTarget - $rating;
//			if ($abweichung < 0) $abweichung = 0;
//			if ($sum == null or !array_key_exists($uid, $sum)) $sum[$uid] = 0;
//			$sum[$uid] += $abweichung * $abweichung;
//			if ($maxSum == null or !array_key_exists($uid, $maxSum)) $maxSum[$uid] = 0;
//			$maxSum[$uid] += $lowerTarget * $lowerTarget;
//		}
//		$count = 0; $distantmassSumme = 0;
//		foreach ($sum as $uid => $userSum) {
//			$maxDistanzmass = sqrt($maxSum[$uid]);
//			$distanzmass = sqrt($userSum);
//			if ($maxDistanzmass != 0) { // keine Werte -> kein Ergebnis
//				 $normDistanzmass = $distanzmass / $maxDistanzmass;
//				 $returnValue[$uid] = 100 - ($normDistanzmass * 100);
//			}
//		}
//		return $returnValue;
//	}
	
	/**
	 * L�scht alle Antworten eines Benutzers und setzt somit den Fragebogen zur�ck.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die UserId des Users, dessen Ergebnisse gel�scht werden sollen
	 * @param int $attempt Der Ausf�llversucht
	 */
	public function clearAnswers(ModelContext $context, $uid, $attempt) {
		//Alle gew�hlten Antworten in allen Fragen dieser Survey f�r diesen User l�schen
		$success = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'clearAnswers'), Array($uid, $attempt, $this->id));		
		if (!$success)
			throw new ModelException("Survey::clearAnswers: Fehler beim L�schen der Antworten:<br>".$context->getDb()->getError(), 0);
		$success = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'clearCompleted'), Array($this->id, $uid, $attempt));		
		if (!$success)
			throw new ModelException("Survey::clearAnswers: Fehler beim L�schen des Ausgef�llt-Eintrags:<br>".$context->getDb()->getError(), 0);
	}
	
	/**
	 * Speichert das vollst�ndige Ausf�llen eines Fragebogens.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die UserId des Users, dessen Ergebnisse gespeichert werden sollen
	 * @param int $attempt Der Ausf�llversucht
	 */
	public function storeComplete(ModelContext $context, $uid, $attempt) {
		// Merkmalsergebnisse speichern
		$results = $this->getResultsForUser($context, $uid, $attempt);
		foreach ($results as $chid => $result) {
			$success = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'storeUserResults'), Array($uid, $this->id, $chid, $attempt, $result));		
			if (!$success)
				throw new ModelException("Survey::storeComplete: Fehler beim Einf�gen der Merkmalsergebnisse:<br>".$context->getDb()->getError(), 0);
		}
		// Gesamtergebnis mit Ausf�lldatum speichern
		$result = $this->calculateResultForUser($context, $uid, $attempt);
		$success = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'storeComplete'), Array($this->id, $uid, time(), $attempt, $result));		
		if (!$success)
			throw new ModelException("Survey::storeComplete: Fehler beim Einf�gen des Fragebogenergebnisses:<br>".$context->getDb()->getError(), 0);
	}
	
	/**
	 * Liefert die Nummer des h�chstens Versuchs, den Fragebogen auszuf�llen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $uid Die UserId des Users, dessen Ergebnisse gew�nscht werden
	 * @return int Die Nummer des letzten Versuchs, mit dem der Fragebogen vollst�ndig beantwortet wurde. 0, falls noch nie beantwortet
	 */
	public function getAttemptForUser(ModelContext $context, $uid) {
		$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getAttemptForUser'), Array($this->id, $uid));
		if ($result == false) 
			throw new ModelException("Survey::getAttemptForUser: Fehler beim Lesen von der Datenbank:<br>".$context->getDb()->getError(), 0);
		if (($row = $context->getDb()->fetch_array($result)) != false) 
			return $row['maxattempt'];
		else
			return 0;
	}
	
// Momentan unn�tig
//	/**
//	 * Liefert die Nummer des h�chstens Versuchs, in dem der User den Fragebogen ausgef�llt hat.
//	 * Pr�ft die Ergebnisse, nicht die Abgeschlossen-Tabelle und liefert den h�chsten Attempt.
//	 * 
//	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
//	 * @param int $uid Die UserId des Users, dessen Ergebnisse gew�nscht werden
//	 * @return int Die Nummer des letzten Versuchs, mit dem der Fragebogen beantwortet wurde. 0, falls noch nie beantwortet
//	 */
//	public function getCurrentAttemptForUser(ModelContext $context, $uid) {
//		$result = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'survey', 'getCurrentAttemptForUser'), Array($this->id, $uid));
//		if ($result == false) 
//			throw new ModelException("Survey::getCurrentAttemptForUser: Fehler beim Lesen von der Datenbank", 0);
//		if (($row = $context->getDb()->fetch_array($result)) != false) 
//			return $row['maxattempt'];
//		else
//			return 0;
//	}

	/**
	 * Liefert den n�chsten Survey in der Bearbeitungsreihenfolge, der zum aktuellen Block geh�rt
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param Survey n�chster Survey oder null, falls letzter
	 */
	public function getSuccessor(ModelContext $context) {
		$next = null;
		// Zuerst innerhalb des aktuellen SurveyBlock Nachfolger suchen:
		$surveys = Survey::getForBlock($context, $this->getBlid());
		foreach ($surveys as $survey) {
			if ($survey->getPosition() > $this->getPosition() and // gef. Survey kommt nach aktuellem
				($next == null or $next->getPosition() > $survey->getPosition())) // noch kein Erg. oder neuer Erg. besser
				$next = $survey;
		}
		return $next; 
	}
}
?>
