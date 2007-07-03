<?php
/***********************************************************************************
 * WiSo@visor - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: model.php
 * $Revision: 1.2 $
 * Erstellt am: 05.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/


/**
 * Helper-Klasse, die die Basisfunktionen für alle Modelklassen
 * bereitstellt.
 * 
 * @author Michael Gottfried
 */
abstract class ModelHelper {
	
	/**
	 * Jedes Objekt wird durch mindestens ein Feld identifiziert, die Id.
	 */
	protected $id = null;

	/**
	 * ...und diese ID = 'new', wenn es ein neues Objekt - noch ohne ID - ist
	 */
	const ID_NEW = 'new';

	/**
	 * Privater Standardkonstruktor, der über die statischen getFor...-Methoden der
	 * abgeleiteten Klassen aufgerufen wird.
	 * @param int $id Die identifizierende ID
	 */
	private function __construct($id) {
		$this->id = $id;		
	}
	
	/**
	 * Getter-Methode für die Id.
	 * @return int $id Die identifizierende ID.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Setter-Methode für die Id.
	 * @param int $id Die identifizierende ID.
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
}

/**
 * Interface zum Zugriff auf Datenbank und Programmkonfiguration
 * Viele Modell-Klassen benötigen ein ModelContext-Objekt, um Daten aus der Datenbank
 * zu lesen oder Konfigurationsvariablen abzurufen.
 * 
 * @author Michael Gottfried
 */
interface ModelContext {
	
	/**
	 * Liefert den Konfigurationscontainer zum Zugriff auf die Programmkonfiguration.
	 * 
	 * @return Configuration-Instanz
	 */
	public function getConf();
	
	/**
	 * Liefert den Datenbank-Container zur Datenbankbenutzung.
	 * 
	 * @return Database-Instanz
	 */
	public function getDb();
}

?>
