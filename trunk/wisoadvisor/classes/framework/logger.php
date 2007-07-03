<?php

	/*
	 * Schnittstelle für Logging-Komponenten im WiSo-Advisor.
	 */
	interface Logger {
	
		/*
		 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
		 */	
		public function log(ModelContext $context);
	}

?>