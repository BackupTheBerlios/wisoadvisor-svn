<?php
	
	/**
	 * Implementierung der 'LogSource'-Schnittstelle für die Aktion 'survey'
	 */
	class SurveyLogSource extends SimpleLogSource {
		
		public function __construct() {
			
			parent::__construct('step');
		}
		
		public function getLogData(ModelContext $context) {
			
			// Ergebnis der Superklasse wird geholt
			$result = parent::getLogData($context);
			
			// Die aktuelle Umfrage-ID wird ermittelt
			$sid = $context->getParam()->getParameter('sid');
			if ($sid == null || $sid == '')
				throw new MissingParameterException('Parameter sid fehlt.');
				
			// Die Umfrage-ID wird angehaengt
			$result .= "sid=$sid&";
			
			// Handelt es sich um einen Fragenblock? (wenn ja, ist step=survey)
			if (strpos($result, 'survey') != false) {
				
				// Die aktuelle Fragenblock-ID wird ermittelt
				$qbid = $context->getParam()->getParameter('qbid');
				$resultSet = null;
				if ($qbid == null) {
					// Es ist wahrscheinlich der erste Fragenblock => ID des ersten Fragenblocks der Umfrage ermitteln
					$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'logging', 'firstQuestionBlock'), Array($sid));
					if ($resultSet == false)
						throw new ModelException("SurveyLogSource::getLogData: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
					if (($row = $context->getDb()->fetch_array($resultSet)) != false)
						$qbid = $row['qbid'];
				} else {
					// Es ist nicht der erste Fragenblock => Position des naechsten Fragenblocks wird ermittelt
					$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'logging', 'nextQuestionBlockPosition'), Array($sid, $qbid));
					if ($resultSet == false)
						throw new ModelException("SurveyLogSource::getLogData: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);	
					if (($row = $context->getDb()->fetch_array($resultSet)) != false) {
						$position = $row['position'];
						$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'logging', 'nextQuestionBlock'), Array($sid, $position));
						if ($resultSet == false)
							throw new ModelException("SurveyLogSource::getLogData: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);	
						if (($row = $context->getDb()->fetch_array($resultSet)) != false)
							$qbid = $row['qbid'];
					}
				}
				
				// Die Nummer des Fragenblocks wird angehaengt
				if ($qbid) $result .= "qbid=$qbid&";
				
				// Die Nummer des laufenden Versuchs wird abgelesen und angehaengt
				$attempt = $context->getParam()->getParameter('attempt');
				if ($attempt) $result .= "attempt=$attempt&";
			}
			
			return $result;
		}
	}
?>