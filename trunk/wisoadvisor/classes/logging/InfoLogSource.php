<?php
	
	/**
	 * Implementierung der 'LogSource'-Schnittstelle für die Aktion 'info'
	 */
	class InfoLogSource extends SimpleLogSource {
		
		public function __construct() {
			
			parent::__construct('step');
		}
		
		public function getLogData(ModelContext $context) {
			
			$result = parent::getLogData($context);
			if (strpos($result, 'page') != false) {
				
				// Es wurde die Infoseite einer Umfrage angeclickt
				// => Id der Umfrage ermitteln und anhaengen
				$sid = $context->getParam()->getParameter('sid');
				$result .= "sid=$sid&";
				
			} elseif (strpos($result, 'block') != false) {
			
				// Es wurde die Uebersichtsseite eines Umfrageblocks angeclickt
				// => Id des Umfrageblocks ermitteln und anhaengen
				$blid = $context->getParam()->getParameter('blid');
				$result .= "blid=$blid&";
			}
			
			return $result;
		}
	}

?>