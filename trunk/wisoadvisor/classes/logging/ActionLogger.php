<?php
	
	/**
	 * Einfache Implementierung der 'Logger'-Schnittstelle.
	 * Es wird ein Array mit registrierten Log-Quellen verwaltet.
	 */
	class ActionLogger implements Logger {
		
		private $sources;	// Array für Log-Quellen
		
		public function __construct() {}
		
		/**
		 * Die eigentliche Log-Funktion. Sie ermittelt mit Hilfe des ParameterHandlers
		 * die vom Benutzer betaetigte Aktion und speichert die Log-Daten von der
		 * entsprechenden Log-Quelle in einer Datenbanktabelle.
		 */
		public function log(ModelContext $context) {
			
			$action = $context->getParam()->getParameter($context->getConf()->getConfString('dispatchparamname'));
			if (!$action) $action = 'start';
			if (array_key_exists($action, $this->sources)) {
				
				// Die Session-Id wird geholt
				$session_id = session_id();
				
				// Die Benutzeridentifikation wird geholt
				$uid = $context->getSess()->isAuthenticated() ? $context->getSess()->getUid() : '0';
				
				$resultSet = $context->getDb()->fetchPreparedRecord($context->getConf()->getConfString('sql', 'logging', 'actionId'), 
					Array($action));
				if ($resultSet != false) {
					
					// Die Aktionsidentifikation wird gelesen
					$aid = $resultSet['aid'];
				
					// Die Log-Daten werden generiert
					$data = $this->sources[$action]->getLogData($context);
					
					// Die Log-Daten werden in die Datenbank eingespeichert
					$context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'logging', 'click'), 
						array($uid, $session_id, $aid, $data));
				}
			}
		}
		
		/**
		 * Diese Funktion legt in der Liste mit den Log-Quellen 
		 * die Log-Quelle fuer eine bestimmte Aktion fest.
		 * @param $action - Zeichenkette, die die gewuenschte Aktion spezifizert
		 * @param $logSource - Ein Objekt, das die Schnittstelle 'LogSource' implementiert
		 */
		public function addLogSource($action, LogSource $logSource) {
		
			if ($action && $logSource)
				$this->sources[$action] = $logSource;
		}
		
		/**
		 * Diese Funktion loescht die Log-Quelle fuer eine bestimmte Aktion
		 * aus der Liste mit den Log-Quellen.
		 * @param $action - Zeichenkette, die die gewuenschte Aktion spezifizert
		 */
		public function removeLogSource($action) {
			
			if ($action && array_key_exists($action, $this->sources))
				unset($this->sources[$action]);
		}
	}
?>