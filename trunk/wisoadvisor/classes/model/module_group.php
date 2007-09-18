<?php
/***********************************************************************************
 * WiSo@visor v2 - Studienmanagementsystem (Pruefungsplan)
 * (c) 2007 Lehrstuhl fuer Wirtschaftsinformatik 3, Uni Erlangen-Nuernberg
 *
 * Datei: module_group.php
 * $Revision: 1.6 $
 * Erstellt am: 30.08.2007
 * Erstellt von: Florian Mattes
 ***********************************************************************************/

/** 
 * Die Klasse ModuleGroup enthaelt einen Bereich des Studiums
 * 
 * @author Florian MAttes
 */ 
class ModuleGroup extends ModelHelper {
	
  private $mgrpid = null;
  private $name = null;
  
  private function __construct($modid) {
		parent::__construct($modid);
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
      $result = new ModuleGroup($row['mgrpid']);
      $result->name = $row['name'];
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
		return new ModuleGroup(Module::ID_NEW);
	}
	

	/**
	 * Liefert ein Modul anhand seiner ID
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param $modid Module ID
	 * @return Array von Module-Objekten
	 */
	public static function getForId(ModelContext $context, $mgrpid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'module_group', 'getForId'), Array($mgrpid));
		if ($resultSet == false) 
			throw new ModelException("Module::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result = self::getForDBRow($row);
		}
		return $result;
	}
	
  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
  }

  }
?>
