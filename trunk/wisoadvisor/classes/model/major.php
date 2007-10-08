<?php
/***********************************************************************************
 * WiSo@visor v2 - Studienmanagementsystem (Pruefungsplan)
 * (c) 2007 Lehrstuhl fuer Wirtschaftsinformatik 3, Uni Erlangen-Nuernberg
 *
 * Datei: studies.php
 * $Revision: 1.6 $
 * Erstellt am: 30.08.2007
 * Erstellt von: Florian Mattes
 ***********************************************************************************/

/** 
 * Die Klasse Studies enthaelt einen Studiengang
 * 
 * @author Florian MAttes
 */ 
class Major extends ModelHelper {
	
  private $stid = null;
  private $name = null;
  private $veryshortname = null;
  private $shortname = null;
  private $fullname = null;
  
  private function __construct($majid) {
		parent::__construct($majid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enthaelt.
	 * @return Module Das neu erzeugte User-Objekt.
	 */
	private function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen	
      $result = new Major($row['majid']);
      $result->stid = $row['stid'];
      $result->veryshortname = $row['veryshortname'];
      $result->shortname = $row['shortname'];
      $result->fullname =  $row['fullname'];
		}
		// Objekt zurueckliefern
		return $result;
	}
	
	/**
	 * Liefert ein neues, leeres User-Objekt mit id = 'new'
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return User Das Objekt
	 */
	public static function getNew(ModelContext $context) {
		return new Major(Major::ID_NEW);
	}
	

	/**
	 * Liefert ein Modul anhand seiner ID
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param $modid Module ID
	 * @return Array von Module-Objekten
	 */
	public static function getForId(ModelContext $context, $stid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'studies', 'getForId'), Array($stid));
		if ($resultSet == false) 
			throw new ModelException("Major::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result = self::getForDBRow($row);
		}
		return $result;
	}
	
  public function getVeryShortName() {
    return $this->veryshortname;
  }

  public function getFullName() {
    return $this->fullname;
  }
  
  public function getShortName() {
    return $this->veryname;
  }
  
  public function getStId() {
    return $this->stid;
  }
  
  }
?>
