<?php

	/**
	 * 'Dummy'-Implementierung der 'LogSource'-Schnittstelle
	 */
	class EmptyLogSource implements LogSource {
		
		public function getLogData(ModelContext $context) {
			
			$result = '';
			return $result;
		}
		
		public function __toString() {
			
			return 'empty log source';
		}
	}

?>