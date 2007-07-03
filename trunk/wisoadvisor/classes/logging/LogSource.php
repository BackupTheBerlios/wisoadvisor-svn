<?php

	/**
	 * Schnittstelle f�r Log-Quellen im WiSo-Advisor
	 */
	interface LogSource {
	
		/*
		 * Gibt die zu speichernde Log-Nachricht zur�ck.
		 * ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
		 */	
		public function getLogData(ModelContext $context);
	}

?>