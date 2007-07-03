<?php

	/**
	 * Einfache Implementierung der 'LogSource'-Schnittstelle.
	 * Es wird den Wert eines benutzerdefinierten Paramters 
	 * gespeichert.
	 */
	class SimpleLogSource implements LogSource {
		
		private $param = '';
		
		/**
		 * Standardkonstruktor
		 *
		 * @param parameter Name des zu speichernden Parameters
		 */
		public function __construct($parameter) {
			
			if ($parameter && strcmp($parameter, ''))
				$this->param = $parameter;
		}
		
		public function getLogData(ModelContext $context) {
			
			$result = "$this->param=" . $context->getParam()->getParameter($this->param) . "&";
			return $result;
		}
		
		public function __toString() {
			
			return 'simple log source';
		}
	}

?>