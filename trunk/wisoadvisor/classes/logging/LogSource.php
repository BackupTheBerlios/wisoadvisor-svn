<?php

	/**
	 * Schnittstelle für Log-Quellen im WiSo-Advisor
	 */
	interface LogSource {
	
		/*
		 * Gibt die zu speichernde Log-Nachricht zurück.
		 * ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
		 */	
		public function getLogData(ModelContext $context);
	}

?>