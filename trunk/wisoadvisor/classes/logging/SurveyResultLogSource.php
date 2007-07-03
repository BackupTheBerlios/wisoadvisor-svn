<?php
	
	/**
	 * Implementierung der 'LogSource'-Schnittstelle für die Aktion 'survey_result'
	 */
	class SurveyResultLogSource extends SimpleLogSource {
		
		public function __construct() {
			
			parent::__construct('sid');
		}
		
		public function getLogData(ModelContext $context) {
			
			// Ergebnis der Superklasse wird geholt
			$result = parent::getLogData($context);
			
			// Die Nummer des laufenden Versuchs wird abgelesen und angehaengt
			$attempt = $context->getParam()->getParameter('attempt');
			if ($attempt) $result .= "attempt=$attempt&";
			
			return $result;
		}
	}
?>