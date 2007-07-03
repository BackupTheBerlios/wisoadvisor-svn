<?php
//Der Parameter-Handler sorgt für den Zugriff auf im HTTP-Request übergebene Parameter.
//Parameter werden grundsätzlich so geliefert, wie sie ankommen - also meist als String.

//vor der Klasse müssen einige Konstanten definiert werden:
//soll ein Parameter bevorzugt aus dem POST bzw. dem GET-Teil gelesen werden, 
//oder ist exakt nur ein Teil erlaubt?
//diese Konstanten erleichtern die Benutzung des ParameterHandler
define('PARAMETERHANDLER_PREFER_POST', 0);
define('PARAMETERHANDLER_PREFER_GET', 1);
define('PARAMETERHANDLER_ONLY_POST', 2);
define('PARAMETERHANDLER_ONLY_GET', 3);

class ParameterHandler
{

	//Variable, die das Verhalten (s.o.) festlegt:
	private $accessMethod;
	
	//Konstruktor
	public function ParameterHandler()
	{
		//initialisiere mit Standardverhalten:
		$this->accessMethod = PARAMETERHANDLER_PREFER_POST;
	}

	//setAccessMethod legt das Auslese-Verhalten neu fest; benutze eine der oben definierten Konstanten;
	//true, wenn Variable gesetzt wird, false wenn ungültiger Wert übergeben wurde
	public function setAccessMethod( $newMethod = PARAMETERHANDLER_PREFER_POST )
	{
		if ( ($newMethod==PARAMETERHANDLER_PREFER_POST) || 
			($newMethod==PARAMETERHANDLER_PREFER_GET) || 
			($newMethod==PARAMETERHANDLER_ONLY_POST) || 
			($newMethod==PARAMETERHANDLER_ONLY_GET) )
		{
			$this->accessMethod = $newMethod;
			return true;
		}
		else return false;
	}
	
	//getAccessMethod gibt das derzeitige Ausleseverhalten zurück
	public function getAccessMethod()
	{
		return $this->accessMethod;
	}

	//getParameter gibt den Wert des mit $key spezifizierten Parameters zurück
	public function getParameter( $key )
	{
		//Fallunterscheidung: abhängig vom eingestellten Ausleseverhalten:
		switch ($this->accessMethod)
		{
			case PARAMETERHANDLER_ONLY_POST:
				return $this->getPostParameter($key);
				//break; <- unnötig, da durch das return sowieso rausgesprungen wird

			case PARAMETERHANDLER_ONLY_GET:
				return $this->getGetParameter($key);
				//break; <- unnötig, da durch das return sowieso rausgesprungen wird

			case PARAMETERHANDLER_PREFER_GET:
				//wenn der aus dem GET gelesene Wert nicht null ist, gib das GET zurück, sonst versuche es im POST:
				if ( $this->getGetParameter($key)!= null ) return $this->getGetParameter($key);
				else return $this->getPostParameter($key);
				//break; <- unnötig, da durch das return sowieso rausgesprungen wird

			case PARAMETERHANDLER_PREFER_POST:
			default:
				//wenn der aus dem POST gelesene Wert nicht null ist, gib das POST zurück, sonst versuche es im GET:
				if ( $this->getPostParameter($key)!= null ) return $this->getPostParameter($key);
				else return $this->getGetParameter($key);
				//break; <- unnötig, da durch das return sowieso rausgesprungen wird
		}
	}
	
	//setParameter setzt einen Parameterwert - abhängig vom spezifizierten Ausleseverhalten
	public function setParameter( $key, $value )
	{
		//Fallunterscheidung: abhängig vom eingestellten Ausleseverhalten:
		switch ($this->accessMethod)
		{
			case PARAMETERHANDLER_ONLY_GET:
			case PARAMETERHANDLER_PREFER_GET:
				$this->setGetParameter($key,$value);
				break;

			case PARAMETERHANDLER_ONLY_POST:
			case PARAMETERHANDLER_PREFER_POST:
			default:
				$this->setPostParameter($key,$value);
				break;
		}
		return true;
	}
	
	//getGetParameter gibt den Wert des mit $key spezifizierten Parameters aus dem GET zurück
	protected function getGetParameter( $key )
	{
		return @$_GET[$key];
	}
	
	//getPostParameter gibt den Wert des mit $key spezifizierten Parameters aus dem POST zurück
	protected function getPostParameter( $key )
	{
		return @$_POST[$key];
	}

	//setGetParameter gibt den Wert des mit $key spezifizierten Parameters aus dem GET zurück
	protected function setGetParameter( $key, $value )
	{
		$_GET[$key] = $value;
		return true;
	}
	
	//setPostParameter gibt den Wert des mit $key spezifizierten Parameters aus dem POST zurück
	protected function setPostParameter( $key, $value )
	{
		$_POST[$key] = $value;
		return true;
	}
}

?>