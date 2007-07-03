<?php
//***********************************************************************************
//WiSo@vice - Online Studienberatung der WiSo-Fakultät
//***********************************************************************************
//Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de
//
//***********************************************************************************
//index.php: wird bei jedem Programmlauf aufgerufen - Entspricht einer main()-Methode
//***********************************************************************************

//benötigte Autoload-Funktion - diese zieht automatisch benötigte Klassen an!
//die Zuordnung von Klassennamen zu Dateinamen ist im Konfigurationsobjekt festgelegt
function __autoload($className) {
    global $configContainer; //nicht so sauber, geht aber wg. __autoload nicht anders
    require_once $configContainer->getConfString('class', $className);
}

//es wird immer eine Session verwendet, damit ggf. Objekte darauf abgelegt werden können, bzw. gelesen werden.
//generell gilt: wenn möglich, wird ein Objekt aus der Session gelesen statt neu angelegt
session_start();

//Konfigurations-Klasse wird anfangs manuell geladen - es wird das "spezialisierte" Objekt für WisoAdvisor benutzt;
// die Konfiguration liegt auf der Grundebene, nicht im Classpath!
require_once('configuration.php'); //spezialisiertes Konfigurations-Objekt

//die Variable $error speichert, ob ein Fehler aufgetreten ist
$error = '';
//$ok ist true, wenn die BusinessLogik ausgeführt werden konnte; ok2 bezieht sich auf die Menüerzeugung
$ok = null;
$ok2 = null;
//$actualUseCase ist später der Ausführer der BusinessLogik
$actualUseCase = null;
//$session ist für die Abfrage und das Erzeugen der Usersession zuständig
$session = null;
//$configContainer enthält später die Programmkonfiguration
$configContainer = null;
//parameterHandler dient zum Zugriff auf GET bzw POST Parameter
$parameterHandler = null;
//$db dient zum Zugriff auf die Datenbank
$db = null;
//$dispatcher hilft bei der Verzweigung auf die angeforderten UseCases: entspricht einer Factoryklasse
$dispatcher = null;
//$logger dient zum Loggen der Benutzerinteraktionen mit der Webseite
$logger = null;

//im configContainer wird die Programmkonfiguration abgelegt
$configContainer = new WisoadvisorConfiguration();

//Hier können die 4 Objekte aufeinmal abgefragt werden, da entweder alle vorhanden sind oder keines
if ( isset($_SESSION['parameterHandler']) && isset($_SESSION['db']) && isset($_SESSION['sessionHandler']) &&
	 isset($_SESSION['logger']))
{
	//alle schon vorhanden: Hole einfach die Objekte aus der Session
	$parameterHandler = unserialize($_SESSION['parameterHandler']);
	$db = unserialize($_SESSION['db']);
	$session = unserialize($_SESSION['sessionHandler']);
	$logger = unserialize($_SESSION['logger']);
}
else
{
	//es sind also noch nicht alle benötigten Objekte vorhanden: dann werden sie hier erstellt und zugewiesen

	//der Parameter-Handler dient zum Auslesen der GET bzw. POST-Parameter
	//Standardmethode zum Lesen: Ein gleichnamiger POST-Parameter
	//soll Vorrang vor dem GET-Parameter haben
	$parameterHandler = new ParameterHandler();
	$parameterHandler->setAccessMethod(PARAMETERHANDLER_PREFER_POST);

	//lege das Datenbank-Objekt an und stelle eine Verbindung zur DB her
	$db = new SpecialisedDatabase();
  
	//ein neuer Session-Handler muss auch noch angelegt werden
	$session = new SessionHandler($db, $configContainer);

	//ein neues Logger-Objekt wird erstellt
	$logger = new ActionLogger($configContainer, $db, $parameterHandler, $session);
	$logger->addLogSource('changeuserdata', new SimpleLogSource('step'));
	$logger->addLogSource('feedback', new SimpleLogSource('reference'));
	$logger->addLogSource('forgotpassword', new SimpleLogSource('step'));
	$logger->addLogSource('getpdf', new EmptyLogSource());
	$logger->addLogSource('info', new InfoLogSource());
	$logger->addLogSource('login', new SimpleLogSource('step'));
	$logger->addLogSource('logout', new EmptyLogSource());
	$logger->addLogSource('overview', new EmptyLogSource());
	$logger->addLogSource('registration', new SimpleLogSource('step'));
	$logger->addLogSource('start', new EmptyLogSource());
	$logger->addLogSource('static', new SimpleLogSource('step'));
	$logger->addLogSource('survey', new SurveyLogSource());
	$logger->addLogSource('surveyhelp', new SurveyHelpLogSource());
	$logger->addLogSource('survey_result', new SurveyResultLogSource());
}

//eine Instanz des allgemeinen Dispatchers wird benötigt, um Anfragen an die spezialisierten UseCases zu dispatchen
$dispatcher = new UseCaseDispatcher( $configContainer, $db, $parameterHandler, $session );

//jetzt sind ersteinmal alle "globalen" Objekte vorhanden

if ($db->connect())
{
	//jetzt muss auf den korrekten UseCase verzweigt werden
	$requestedUseCase = $parameterHandler->getParameter($configContainer->getConfString('dispatchparamname')); //lese den Paramter aus, der den UseCase angibt
	//ist der Parameter leer, nimm den Standardusecase:
	if ((!$requestedUseCase) || ($requestedUseCase=='')) $requestedUseCase = $configContainer->getConfString('standardusecase');

	//weitere Fallunterscheidung: wenn kein User angemeldet ist, dann soll ebenfalls der Standardusecase gezogen werden
	//Ausnahmen: Usecases "Login", "Registrierung" und "Static" (in der Configuration definiert)
	if ( (!$session->isAuthenticated()) &&
			(!in_array($requestedUseCase, $configContainer->getConfArray('unauthenticatedExceptions'))) )
				 $requestedUseCase = $configContainer->getConfString('standardusecase');
	//falls der Wartungsschalter gesetzt ist, wird nur die Wartungsseite angezeigt:
	if ($configContainer->getConfBool('maintenance')) $requestedUseCase = $configContainer->getConfString('maintenanceusecase');

	//hole eine Instanz des angeforderten UseCase
	$actualUseCase = $dispatcher->dispatch($configContainer->getConfString('usecase', $requestedUseCase));

	//Usecase-Zugriff wird gespeichert
	$logger->log($actualUseCase);

	//jetzt muss der UseCase seine internen Berechnungen durchführen
	$ok = $actualUseCase->execute();
}
else
{
	//sonst: setze die Error-Variable
	$error .= 'Keine Verbindung zur Datenbank möglich.';
}

//das Menü und die Linkbox muss erzeugt werden
$menuUseCase = $dispatcher->dispatch($configContainer->getConfString('usecase', 'menu'));
$linkboxUseCase = $dispatcher->dispatch($configContainer->getConfString('usecase', 'linkbox'));
$sessionUseCase = $dispatcher->dispatch($configContainer->getConfString('usecase', 'session'));
$sponsorboxUseCase = $dispatcher->dispatch($configContainer->getConfString('usecase', 'sponsorbox'));
$ok2 = $menuUseCase->execute();
$ok3 = $linkboxUseCase->execute();
$ok4 = $sessionUseCase->execute();
$ok5 = $sponsorboxUseCase->execute();

//wenn nicht alles geklappt hat, erzeuge einen Fehler
if (!($db && $ok && $ok2 && $ok3 && $ok4 && $ok5))
{
	$errorUseCase = $dispatcher->dispatch($configContainer->getConfString('usecase', 'error'));

	//füge evtl. Fehlermeldungen zusammen
	if ($actualUseCase) $error .= $actualUseCase->getError();
	else $error .= $configContainer->getConfString('messages', 'usecasenotfound');
	$error .= $menuUseCase->getError();
	$error .= $linkboxUseCase->getError();
	$error .= $sessionUseCase->getError();
	$error .= $sponsorboxUseCase->getError();

	//übergib den Fehler an den Error-UseCase
	$errorUseCase->setError($error);
	if (!$errorUseCase->execute()) die('Abbruch erfolgt wegen Fehler in der Fehlerbehandlung...');

	//weise den Error-UseCase dem ActualUseCase zu - denn der wird nicht mehr benötigt
	$actualUseCase = $errorUseCase;
}

//abhängig vom Typ des Outputs (HTML, XML, Grafikausgaben, ... nichts (nur Header)) wird das Template benutzt oder auch nicht
switch ($actualUseCase->getOutputType())
{
	case USECASE_HTML:
		//HTML: erzeuge eine vollständige HTML-Seite (mit Menü & Content) aus dem Template
		//der Usecase selbst legt das Template fest, das verwendet werden soll
		$templateToUse = $actualUseCase->getTemplateName();
		$htmlGenerator = new HtmlGenerator( $configContainer->getConfString('template', $templateToUse), $configContainer->getConfString('template', 'indicator', 'pre'), $configContainer->getConfString('template', 'indicator', 'after'));
		//befülle den Generator mit dem Menü...
		$htmlGenerator->apply($configContainer->getConfString('template', 'menuname'), $menuUseCase->getOutput());
		//...mit der Linkbox...
		$htmlGenerator->apply($configContainer->getConfString('template', 'linkboxname'), $linkboxUseCase->getOutput());
		//...mit der Sponsorbox...
		$htmlGenerator->apply($configContainer->getConfString('template', 'sponsorbox'), $sponsorboxUseCase->getOutput());
		//...den Sessiondaten...
		$htmlGenerator->apply($configContainer->getConfString('template', 'sessionname'), $sessionUseCase->getOutput());
		//...und mit dem eigentlichen Seiteninhalt
		$htmlGenerator->apply($configContainer->getConfString('template', 'contentname'), $actualUseCase->getOutput());

		//...und gib das HTML aus:
		echo $htmlGenerator->getHTML();
		break;

	case USECASE_XML:
		//XML verwendet kein Template; das XML-File ist direkt der Output des UseCase
		//natürlich muss der Content-Type korrekt angegeben werden
		$actualUseCase->sendContentTypeHeader();
		echo $actualUseCase->getOutput();
		break;

	case USECASE_GIF:
	case USECASE_PNG:
	case USECASE_JPG:
		//eine Grafik wird direkt ausgegeben
		$actualUseCase->sendContentTypeHeader();
		echo $actualUseCase->getOutput();
		break;

	case USECASE_OWN_HTML:
		echo $actualUseCase->getOutput();
		break;

	default:
		//im Falle von "nichts" tu auch nichts ;-)
		break;
}

//zum Schluss wird die DB-Verbindung gekappt
if ($db) $db->disconnect();
//ausserdem müssen die globalen Objekte in der Session abgelegt werden - Achtung: configContainer & dispatcher werden nicht abgelegt, da sie ihrerseits Objekte enthalten, die dann verloren gehen
$_SESSION['parameterHandler'] = serialize($parameterHandler);
$_SESSION['db'] = serialize($db);
$_SESSION['sessionHandler'] = serialize($session);
$_SESSION['logger'] = serialize($logger);
?>