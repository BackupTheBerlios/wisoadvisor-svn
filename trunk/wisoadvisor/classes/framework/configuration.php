<?php
//Configuration ist eine Klasse, die zum Abfragen von Konfigurationsdaten benutzt wird, die 
// als mehrdimensionales Array abgelegt sind - dazu werden verschiedene Zugriffsfunktionen
// zur Verfügung gestellt
//die Konfigurationsdaten selbst sollten in abgeleiteten Kindobjekten liegen - so lassen sich ggf. für jeden UseCase
// eigene Konfigurationen schnell erstellen
abstract class Configuration
{
	private $conf = null; //enthält nach der Initialisierung das Array mit den Konfigurationsdaten
	
	/*
	 * getConfiguration: gibt den Wert des übergebenen Key (auch mehrdimensional) als undefiniertes Objekt zurück.
	 * @param $key1 erster Key (String oder Int)
	 * @param $key2 zweiter Key (String oder Int)
	 * @param $key3 dritter Key (String oder Int)
	 * @return Wert (undefiniert) oder null, wenn nicht vorhanden
	 */
	private function getConfiguration( $key1, $key2 = null, $key3 = null )
	{
		$temp = null;
		
		if (!$key3)
		{
			if (!$key2)	$temp = $this->conf[$key1];
			else $temp = $this->conf[$key1][$key2];
		}
		else $temp = $this->conf[$key1][$key2][$key3];

		return $temp;
	}

	/*
	 * setConfValue: legt einen Wert in der Konfiguration ab.
	 * @param $key1 erster Key (String oder Int)
	 * @param $key2 zweiter Key (String oder Int)
	 * @param $key3 dritter Key (String oder Int)
	 * @param $value der Wert
	 */
	protected function setConfValue( $key1, $key2 = null, $key3 = null, $value )
	{
		if (!$key3)
		{
			if (!$key2)
			{
				$this->conf[$key1] = $value;
			}
			else
			{
				$this->conf[$key1][$key2] = $value;
			}
		}
		else
		{
			$this->conf[$key1][$key2][$key3] = $value;
		}		

	}

	/*
	 * getConfString: gibt den Wert des übergebenen Key (auch mehrdimensional) als STRING zurück.
	 * @param $key1 erster Key (String oder Int)
	 * @param $key2 zweiter Key (String oder Int)
	 * @param $key3 dritter Key (String oder Int)
	 * @return STRING oder null, wenn nicht vorhanden oder keine Umwandlung möglich
	 */
	public function getConfString( $key1, $key2 = null, $key3 = null )
	{
		if ( (string) $this->getConfiguration( $key1, $key2, $key3 ) ) 
				return (string) $this->getConfiguration( $key1, $key2, $key3 );
		else return null;
	}

	/*
	 * getConfArray: gibt den Wert des übergebenen Key (auch mehrdimensional) als ARRAY zurück.
	 * @param $key1 erster Key (String oder Int)
	 * @param $key2 zweiter Key (String oder Int)
	 * @param $key3 dritter Key (String oder Int)
	 * @return ARRAY oder null, wenn nicht vorhanden oder keine Umwandlung möglich
	 */
	public function getConfArray( $key1, $key2 = null, $key3 = null )
	{
		if ( is_array($this->getConfiguration( $key1, $key2, $key3 )) ) 
				return $this->getConfiguration( $key1, $key2, $key3 );
		else return null;
	}

	/*
	 * getConfInt: gibt den Wert des übergebenen Key (auch mehrdimensional) als INT zurück.
	 * @param $key1 erster Key (String oder Int)
	 * @param $key2 zweiter Key (String oder Int)
	 * @param $key3 dritter Key (String oder Int)
	 * @return INT oder null, wenn nicht vorhanden oder keine Umwandlung möglich
	 */
	public function getConfInt( $key1, $key2 = null, $key3 = null )
	{
		if ( (int) $this->getConfiguration( $key1, $key2, $key3 ) ) 
				return (int) $this->getConfiguration( $key1, $key2, $key3 );
		else return null;
	}

	/*
	 * getConfFloat: gibt den Wert des übergebenen Key (auch mehrdimensional) als FLOAT zurück.
	 * @param $key1 erster Key (String oder Int)
	 * @param $key2 zweiter Key (String oder Int)
	 * @param $key3 dritter Key (String oder Int)
	 * @return FLOAT oder null, wenn nicht vorhanden oder keine Umwandlung möglich
	 */
	public function getConfFloat( $key1, $key2 = null, $key3 = null )
	{
		if ( (float) $this->getConfiguration( $key1, $key2, $key3 ) ) 
				return (float) $this->getConfiguration( $key1, $key2, $key3 );
		else return null;
	}

	/*
	 * getConfBool: gibt den Wert des übergebenen Key (auch mehrdimensional) als BOOLEAN zurück.
	 * @param $key1 erster Key (String oder Int)
	 * @param $key2 zweiter Key (String oder Int)
	 * @param $key3 dritter Key (String oder Int)
	 * @return BOOLEAN oder null, wenn nicht vorhanden oder keine Umwandlung möglich
	 */
	public function getConfBool( $key1, $key2 = null, $key3 = null )
	{
		if ( (bool) $this->getConfiguration( $key1, $key2, $key3 ) ) 
				return (bool) $this->getConfiguration( $key1, $key2, $key3 );
		else return null;
	}
}

?>