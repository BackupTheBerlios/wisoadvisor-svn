<?php
//UseCase ist die Oberklasse aller Anwendungsfälle. Zusätzlich besitzt sie auch eine Dispatcherklasse,
//die Anfragen an die richtigen Anwendungsfälle leitet. Der UseCase zur Fehlerausgabe ist hier ebenfalls definiert.

//Abhängigkeiten: Configuration, ParameterHandler, Database

//vor der Klasse müssen einige Konstanten definiert werden:
define('USECASE_NOTYPE',0);
define('USECASE_HTML',1);
define('USECASE_XML',2);
define('USECASE_PNG',3);
define('USECASE_GIF',4);
define('USECASE_JPG',5);
define('USECASE_OWN_HTML',6);

abstract class UseCase implements ModelContext
{
	//Member-Variablen (bzw. -objekte), die der UseCase verwendet
	private $configContainer = null;
	private $db = null;
	private $parameterHandler = null;
	private $sessionHandler = null;
	private $error = '';
	private $outputType = USECASE_NOTYPE;
	private $output = '';
	private $templateName = 'html';
	//ContentTypes ist ein Array, das entsprechend den USECASE_xxx Definitionen die richtigen Content-Types enthält
	private $contentTypes = array(0=>'', 1=>'text/html', 2=>'text/xml', 3=>'image/png', 4=>'image/gif', 5=>'image/jpg', 6=>'text/html');

	//Konstruktor
	public function __construct()
	{
	}
	
	//Initialisierung
	public function initialize( $configContainer, $db, $parameterHandler, $sessionHandler = null)
	{
		//initialisiere Member
		$this->configContainer = $configContainer;
		$this->db = $db;
		$this->parameterHandler = $parameterHandler;
		$this->sessionHandler = $sessionHandler;
	}

	//getConf() liefert den ConfigContainer zur Abfrage der Konfiguration
	/**
	 * liefert den ConfigContainer zur Abfrage der Konfiguration
	 */
	public function getConf()
	{
		return $this->configContainer;
	}
	
	//getDb() liefert den DbContainer zur Datenbankbenutzung
	public function getDb()
	{
		return $this->db;
	}

	//getParam() liefert den ParameterHandler
	public function getParam()
	{
		return $this->parameterHandler;
	}
	
	//getSess() liefert den SessionHandler
	public function getSess()
	{
		return $this->sessionHandler;
	}

	//getError() liefert den Error-String zurück - leerer String, wenn kein Fehler auftrat
	public function getError()
	{
		return $this->error;
	}
	
	//sendContentTypeHeader() sendet einen HTTP-Header, der den Content-Type (abhängig von Outputtype) festlegt
	public function sendContentTypeHeader()
	{
		header('Content-type: '.$this->contentTypes[$this->getOutputType()]);
		return true;
	}
	
	//setError() setzt den Error-String - wenn zusätzliche Fehler angehängt werden sollen, sollte appendError benutzt werden
	protected function setError( $error )
	{
		$this->error = $error;
		return true;
	}

	//appendError() erweitert den vorhandenen Error-String
	protected function appendError( $error )
	{
		$this->error .= $error;
		return true;
	}
	
	//getTemplateName() gibt den Namen des zu verwendenden HTML-Templates zurück
	public function getTemplateName()
	{
		return $this->templateName;
	}
	
	//setTemplateName() setzt den Namen des zu verwendenden HTML-Templates
	public function setTemplateName( $newName )
	{
		$this->templateName = $newName;
		return true;
	}

	//getOutputType() legt den Typ des Outputs fest
	public function getOutputType()
	{
		return $this->outputType;
	}
	
	//setOutputType() setzt die OutputType Variable
	protected function setOutputType( $type )
	{
		if ( ($type==USECASE_HTML) || ($type==USECASE_XML) || ($type==USECASE_NOTYPE) 
			|| ($type==USECASE_GIF) || ($type==USECASE_JPG) || ($type==USECASE_PNG) || ($type==USECASE_OWN_HTML))
		{
			$this->outputType = $type;
			return true;
		}
		else return false;
	}

	//setOutput() setzt den Output-String
	protected function setOutput( $output )
	{
		$this->output = $output;
		return true;
	}
	
	//appendOutput() hängt etwas an den Output-String an + Zeilenumbruch
	protected function appendOutput( $output )
	{
		$this->output .= $output.chr(13);
		return true;
	}

	//getOutput() gibt den Inhalt des Outputs aus ;-)
	public function getOutput()
	{
		return $this->output;
	}
	
	//getStep() gibt den gewünschten Schritt innerhalb eines UseCases zurück
	//wrappt das Ganze, da es fast jeder abgeleitete UseCase benötigt, und somit nicht selbst die Abfrage und Fallunterscheidung machen muss
	protected function getStep()
	{
		$step = $this->getParam()->getParameter($this->getConf()->getConfString('stepparamname'));
		//ist der Parameter leer, nimm den Standardstep:
		if ((!$step) || ($step=='')) $step = $this->getConf()->getConfString('standardstep');
		return $step;
	}
	
	//getDispatcherParam() gibt den Aufrufparameter (des Haupt-UseCases) zurück
	protected function getDispatcherParam()
	{
		$dispatcher = $this->getParam()->getParameter($this->getConf()->getConfString('dispatchparamname'));
		//ist der Parameter leer, nimm den Standardwert:
		if ((!$dispatcher) || ($dispatcher=='')) $dispatcher = $this->getConf()->getConfString('standardusecase');
		return $dispatcher;
	}
	
	/**
	 * getOwnLink() gibt einen Link auf den UseCase selbst zurück, inkl. dem gesetzten Step Parameter - also ein Link der Form: 'irgeneineseite.php?action=usecase&step='
	 * @param $step ein zu setzender Step (optional)
	 * @return der entsprechende Link
	 */
	protected function getOwnLink($step = '', $params = null)
	{
		return $this->getUsecaseLink($this->getParam()->getParameter($this->getConf()->getConfString('dispatchparamname')), $step, $params);
	}
	
	//getMainLink() gibt den "Grundlink" auf die Anwendung, ohne irgendwelche Parameter, zurück
	protected function getMainLink()
	{
		return $_SERVER['PHP_SELF'];
	}
	
	/**
	 * getUsecaseLink() gibt den Link auf einen UseCase zurück
	 * @param $useCase der angeforderte Usecase
	 * @param $step ein Step im UseCase (optional)
	 * @return der entsprechende Link
	 */
	protected function getUsecaseLink($usecase, $step = '', $params = null)
	{
		$moreParams = '';
		if ($params != null) foreach ($params as $par) {
			$moreParams .= '&'.$par;
		}		
		return $this->getMainLink().'?'.$this->getConf()->getConfString('dispatchparamname').'='.$usecase.'&'.$this->getConf()->getConfString('stepparamname').'='.$step.$moreParams;
	}

	//execute() muss jeder UseCase implementieren - enthält die BusinessLogik
	abstract public function execute();
}

//der UseCaseDispatcher kann spezialisierte UseCaseObjekte zurückliefern
class UseCaseDispatcher
{
	//Member-Variablen (bzw. -objekte), die der UseCaseDispatcher verwendet
	private $configContainer = null;
	private $db = null;
	private $parameterHandler = null;
	private $sessionHandler = null;

	//Konstruktor
	public function __construct( $configContainer, $db, $parameterHandler, $sessionHandler )
	{
		//initialisiere Member
		$this->configContainer = $configContainer;
		$this->db = $db;
		$this->parameterHandler = $parameterHandler;
		$this->sessionHandler = $sessionHandler;
	}

	//dispatch(); zurückgeliefert wird entweder ein Kindobjekt von UseCase, und zwar der angeforderte spezialisierte UseCase, oder ein Error-UseCase, vorinitialisiert mit der UseCase-nicht-gefunden-Meldung
	public function dispatch( $requestedUseCase )
	{
		$useCase = null;
		
		if (class_exists($requestedUseCase)) 
		{
			$useCase = new $requestedUseCase;
			$useCase->initialize($this->configContainer, $this->db, $this->parameterHandler, $this->sessionHandler);
		}
		else
		{
			$useCase = new ucError;
			$useCase->initialize($this->configContainer, $this->db, $this->parameterHandler);
			$useCase->setError($this->configContainer->getConfString('messages', 'usecasenotfound'));
		}
		return $useCase;
	}
}

//der Error-UseCase dient lediglich der Fehlerausgabe
class ucError extends UseCase
{
	//setError() überschreibt die protected-Methode des Parents
	public function setError( $error )
	{
		$this->error = $error;
		return true;
	}

	public function execute()
	{
		//der Error-UseCase muss eigentlich nur 'seinen' Fehler als Output angeben
		$this->setOutput($this->error);
		$this->setOutputType(USECASE_HTML);
		return true;
	}
}
?>