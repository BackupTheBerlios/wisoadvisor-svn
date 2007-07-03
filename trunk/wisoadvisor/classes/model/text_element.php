<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: text_element.php
 * $Revision: 1.2 $
 * Erstellt am: 15.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
/**
 * Die Klasse TextElement repräsentiert einen in der Datenbank hinterlegten HTML-Block,
 * der dem Anwender als Ergebnis seiner Umfragen angezeigt wird.
 * 
 * @author Michael Gottfried
 */
class TextElement extends ModelHelper {
	
	private $ttId = null;
	private $name = null;
	private $content = null;

	
	private function __construct($teid) {
		parent::__construct($teid);
	}
	
	/**
	 * Ermittelt ausgehend von der aus der Datenbank gelesenen Datenzeile
	 * alle notwendigen Informationen zur Erstellung eines neuen Objekts.
	 * 
	 * @param array $row Die Datenbankzeile, die Infos zum erstellenden Objekt enthält.
	 * @return TextElement Das neu erzeugte User-Objekt.
	 */
	private function getForDBRow($row) {
		$result = null;
		if ($row != false) { // id existiert 
			// Objekt existiert in DB -> erzeugen	
			$result = new TextElement($row['teid']);
			$result->ttid = $row['ttid'];
			$result->name = $row['name'];
			$result->content = $row['content'];
		}
		// Objekt zurückliefern
		return $result;
	}
	
	/**
	 * Liefert ein TextElement-Objekt, dessen Inhalte aus der Datenbank gelesen werden.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @param int $teid Textelement-ID, die das gewünschte Objekt identifiert.
	 * @return TextElememt Das gewünschte Objekt oder null, falls kein Objekt mit diser ID existiert.
	 */
	public static function getForId(ModelContext $context, $uid) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'text_element', 'getForId'), Array($uid));
		if ($resultSet == false) 
			throw new ModelException("TextElement::getForId: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		$row = $context->getDb()->fetch_array($resultSet);
		$result = self::getForDBRow($row);
		return $result;
	}
	
	/**
	 * Liefert alle TextElement-Objekte aus der Datenbank gelesen.
	 * 
	 * @param ModelContext $context Kontext zum Zugriff auf Datenbank und Konfiguration
	 * @return Array von TextElement-Objekten
	 */
	public static function getAll(ModelContext $context) {
		// In DB suchen, ob existiert
		$resultSet = $context->getDb()->preparedQuery($context->getConf()->getConfString('sql', 'text_element', 'getAll'), Array());
		if ($resultSet == false) 
			throw new ModelException("TextElement::getAll: Fehler beim Lesen in der Datenbank:<br>".$context->getDb()->getError(), 0);
		while (($row = $context->getDb()->fetch_array($resultSet)) != false) {
			$result[] = self::getForDBRow($row);
		}
		return $result;
	}
	
	public function getTtId() {
		return $this->ttId;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getContent() {
		return $this->content;
	}
}
?>
