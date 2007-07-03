<?php
	
	/**
	 * Implementierung der 'LogSource'-Schnittstelle für die Aktion 'surveyhelp'
	 */
	class SurveyHelpLogSource extends SimpleLogSource {
		
		public function __construct() {
			
			parent::__construct('step');
		}
		
		public function getLogData(ModelContext $context) {
			
			$result = parent::getLogData($context);
			if (strpos($result, 'survey') != false) {
				
				// Es wurde die Hilfeseite einer Umfrage angeclickt
				// => Id der Umfrage ermitteln und anhaengen
				$sid = $context->getParam()->getParameter('sid');
				$result .= "sid=$sid&";
				
			} elseif (strpos($result, 'questionblock') != false) {
			
				// Es wurde die Hilfeseite eines Frageblocks angeclickt
				// => Id des Frageblocks ermitteln und anhaengen
				$qbid = $context->getParam()->getParameter('qbid');
				$result .= "qbid=$qbid&";
			}
			
			return $result;
		}
	}

?>