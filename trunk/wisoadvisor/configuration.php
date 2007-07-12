<?php
//WisoadvisorConfiguration enthï¿½lt alle wichtigen Konfigurationsdaten
//als Elternklasse wird Configuration auf dem Framework benï¿½tigt
require_once('classes/framework/configuration.php');

class WisoadvisorConfiguration extends Configuration
{
	/**
	 * Konstruktor: hier werden die Konfigurationsdaten zugewiesen
	 * @return WisoadvisorConfiguration
	 */	
	 public function __construct()
	 {
	 	//"Wartungsschalter"
	 	$this->setConfValue('maintenance', null, null, false); //wenn auf True gesetzt, wird nur die Wartungsseite angezeigt
	 	
	 	//allgemeine Standards
	 	$this->setConfValue('dispatchparamname', null, null, 'action'); //der Dispatcherparameter, der den UseCase angibt
	 	$this->setConfValue('stepparamname', null, null, 'step'); //der Dispatcherparameter, der den Verarbeitungsschritt innerhalb eines UseCase angibt
	 	$this->setConfValue('standardusecase', null, null, 'start'); // Der UseCase, der standardmï¿½ï¿½ig aufgerufen wird
	 	$this->setConfValue('maintenanceusecase', null, null, 'maintenance'); // Der Wartungsusecase
	 	$this->setConfValue('usecaseAfterLogin', null, null, 'start'); // Der UseCase, der nach erfolgreichem Login aufgerufen wird
	 	$this->setConfValue('usecaseAfterLogout', null, null, 'start'); // Der UseCase, der nach erfolgreichem Logout aufgerufen wird
	 	$this->setConfValue('standardstep', null, null, 'start'); // Der Schritt, der standardmï¿½ï¿½ig aufgerufen wird
	 	$this->setConfValue('unauthenticatedExceptions', null, null, Array('login', 'registration', 'static', 'overview', 'info', 'graphics', 'maintenance', 'feedback', 'forgotpassword', 'panorama')); //legt UseCases fest, die auch ohne Login aufgerufen werden dï¿½rfen
	 	
	 	//Datenbankkonfiguration
	 	//ausgelagert in SpecialisedDatabase, damit Serverunabhï¿½ngigkeit erreicht wird!
	 	
	 	//allgemeine Einstellungen zu eMail (werden ggf. in den sub-Configuration benutzt)
	 	$this->setConfValue('common', 'email', 'senderAdress', 'wi3@wiso.uni-erlangen.de');
	 	$this->setConfValue('common', 'email', 'senderName', 'WiSoAdvisor-Team');
	 	$this->setConfValue('common', 'email', 'sender', $this->getConfString('common', 'email', 'senderName').' <'.$this->getConfString('common', 'email', 'senderAdress').'>');
	 	$this->setConfValue('common', 'email', 'replytoAdress', 'wi3@wiso.uni-erlangen.de');
	 	//$this->setConfValue('common', 'email', 'replytoName', 'WiSoAdvisor-Team');
	 	$this->setConfValue('common', 'email', 'replyto', $this->getConfString('common', 'email', 'replytoAdress'));
	 	$this->setConfValue('common', 'email', 'receiver', 'WiSoAdvisor-Team <sonja.fischer@wiso.uni-erlangen.de>');
	 	
	 	
	 	//Templates, die vom HTML-Generator verwendet werden
	 	$this->setConfValue('template', 'html', null, 'templates/htmltemplate.tpl'); //Das Template fï¿½r den Seitenaufbau
	 	$this->setConfValue('template', 'popup', null, 'templates/popuptemplate.tpl'); //Ein Template fï¿½r Popup-Fenster
	 	$this->setConfValue('template', 'indicator', 'pre', '###:###'); //Markierung fï¿½r Ersetzungsanweisungen (Prefix)
	 	$this->setConfValue('template', 'indicator', 'after', '###:###'); //Markierung fï¿½r Ersetzungsanweisungen (Postfix)
	 	$this->setConfValue('template', 'menuname', null, 'menudata');
	 	$this->setConfValue('template', 'contentname', null, 'content');
	 	$this->setConfValue('template', 'sessionname', null, 'sessiondata');
	 	$this->setConfValue('template', 'sponsorbox', null, 'sponsorbox');
	 	$this->setConfValue('template', 'linkboxname', null, 'linkbox');
	 	
	 	//Messages: Nachrichtentexte, die das System ausgibt
	 	$this->setConfValue('messages', 'usecasenotfound', null, 'Der angeforderte UseCase ist nicht vorhanden oder nicht konfiguriert. ');
	 	$this->setConfValue('messages', 'pagenotfound', null, 'Die angeforderte Seite wurde nicht gefunden oder kann nur fï¿½r angemeldete Nutzer angezeigt werden. ');
	 	$this->setConfValue('messages', 'surveynotagainallowed', null, 'Der angeforderte Test wurde von Dir bereits ausgefï¿½llt und abgeschickt - eine nochmalige Teilnahme ist nicht mï¿½glich. ');
	 	$this->setConfValue('messages', 'legend_relative', null, '<b>Erklï¿½rung zu den Grafiken:</b><br/>Die Balken zeigen Dich im Vergleich zu den anderen Testteilnehmern. Der gelbe Strich markiert Dein eigenes Testergebnis, wï¿½hrend die Rï¿½nder eines Balkens jeweils das schlechteste bzw. beste Ergebnis der anderen Nutzer (reduziert um die Extremwerte) symbolisieren.<br/><br/><img src="grafik/single_bad.png" class="image_right"/> Du hast leider nur bis zu ein Drittel der gestellten Fragen richtig beantworten kï¿½nnen. Lies Dir die Infoseiten noch einmal gewissenhaft durch und Du wirst den Erfolg sehen, wenn Du den Test wiederholst.<br/><br/><img src="grafik/single_middle.png" class="image_right"/> Du hast zwischen 33,3% und 66,6% aller Fragen richtig beantwortet, damit liegst Du im guten Mittelfeld. Die restlichen Informationen bekommst Du ï¿½ber die Informationsseiten und die Homepage der WiSo-Fakultï¿½t.<br/><br/><img src="grafik/single_good.png" class="image_right"/> Glï¿½ckwunsch, Du hast zwischen 66,6% und 100% der Fragen richtig beantwortet, in diesem Bereich bist Du top informiert!<br/>');
	 	$this->setConfValue('messages', 'legend', null, '<b>Erklï¿½rung zu den Grafiken:</b><br/>Der Balken ganz oben zeigt Dich im Vergleich zu den anderen Testteilnehmern. Der gelbe Strich markiert Dein eigenes Testergebnis, wï¿½hrend die Rï¿½nder des Balkens jeweils das schlechteste bzw. beste Ergebnis der anderen Nutzer (reduziert um die Extremwerte) symbolisieren.<br/>An den Grafiken zu &quot;Deine Ergebnisse im Einzelnen&quot; erkennst Du Deinen Stand im Vergleich zu den Ergebnissen der anderen Testteilnehmer. Der gelbe Strich markiert Dein eigenes Testergebnis, wï¿½hrend der dunkelblaue Bereich die Bandbreite (reduziert um die Extremwerte) der Ergebnisse der anderen Benutzer zeigt.<br/><br/><img src="grafik/single_bad.png" class="image_right"/> Du hast leider nur bis zu ein Drittel der gestellten Fragen richtig beantworten kï¿½nnen. Lies Dir die Infoseiten noch einmal gewissenhaft durch und Du wirst den Erfolg sehen, wenn Du den Test wiederholst.<br/><br/><img src="grafik/single_middle.png" class="image_right"/> Du hast zwischen 33,3% und 66,6% aller Fragen richtig beantwortet, damit liegst Du im guten Mittelfeld. Die restlichen Informationen bekommst Du ï¿½ber die Informationsseiten und die Homepage der WiSo-Fakultï¿½t.<br/><br/><br/><img src="grafik/single_good.png" class="image_right"/> Glï¿½ckwunsch, Du hast zwischen 66,6% und 100% der Fragen richtig beantwortet, in diesem Bereich bist Du top informiert!<br/>');
	 	$this->setConfValue('messages', 'charheader', null, 'Wie gut Du die Fragen beantwortet hast, veranschaulichen die folgenden Grafiken und Texte. Diese sind hinsichtlich verschiedener Gesichtspunkte gegliedert. Dies soll Dir bei der Reflexion Deiner Ergebnisse helfen. Viel Spaï¿½ beim Lesen Deiner Auswertung!');
	 	
	 	//Matching von Klassen- auf Dateinamen - hier mï¿½ssen alle benï¿½tigten Klassen definiert werden
	 	//zuerst die Framework-Komponenten
	 	$phpClassSuffix = '.php';
	 	$frameworkPath = 'classes/framework/';
	 	$this->setConfValue('class', 'WisoadvisorConfiguration', null, 'configuration'.$phpClassSuffix);
	 	$this->setConfValue('class', 'SpecialisedDatabase', null, 'specialisedDatabase'.$phpClassSuffix);

	 	$this->setConfValue('class', 'Configuration', null, $frameworkPath.'configuration'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ParameterHandler', null, $frameworkPath.'parameter'.$phpClassSuffix);
	 	$this->setConfValue('class', 'Database', null, $frameworkPath.'database'.$phpClassSuffix);
	 	$this->setConfValue('class', 'UseCase', null, $frameworkPath.'usecase'.$phpClassSuffix);
	 	$this->setConfValue('class', 'UseCaseDispatcher', null, $frameworkPath.'usecase'.$phpClassSuffix);
	 	$this->setConfValue('class', 'SessionHandler', null, $frameworkPath.'session'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucError', null, $frameworkPath.'usecase'.$phpClassSuffix);
	 	$this->setConfValue('class', 'HtmlGenerator', null, $frameworkPath.'htmloutput'.$phpClassSuffix);
	 	$this->setConfValue('class', 'HtmlFormGenerator', null, $frameworkPath.'html_form_generator'.$phpClassSuffix);
	 	$this->setConfValue('class', 'HtmlFormGeneratorData', null, $frameworkPath.'html_form_generator'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ModelContext', null, $frameworkPath.'model'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ModelHelper', null, $frameworkPath.'model'.$phpClassSuffix);
	 	$this->setConfValue('class', 'Logger', null, $frameworkPath.'logger'.$phpClassSuffix);
	 	//Framework-Exceptions
	 	$this->setConfValue('class', 'ModelException', null, $frameworkPath.'exceptions'.$phpClassSuffix);
	 	$this->setConfValue('class', 'MissingParameterException', null, $frameworkPath.'exceptions'.$phpClassSuffix);
	 	$this->setConfValue('class', 'HtmlFormGeneratorException', null, $frameworkPath.'exceptions'.$phpClassSuffix);
	 	// Logging-Klassen
	 	$loggingPath = 'classes/logging/';
	 	$this->setConfValue('class', 'ActionLogger', null, $loggingPath.'ActionLogger'.$phpClassSuffix);
	 	$this->setConfValue('class', 'LogSource', null, $loggingPath.'LogSource'.$phpClassSuffix);
	 	$this->setConfValue('class', 'EmptyLogSource', null, $loggingPath.'EmptyLogSource'.$phpClassSuffix);
	 	$this->setConfValue('class', 'SimpleLogSource', null, $loggingPath.'SimpleLogSource'.$phpClassSuffix);
	 	$this->setConfValue('class', 'InfoLogSource', null, $loggingPath.'InfoLogSource'.$phpClassSuffix);
	 	$this->setConfValue('class', 'SurveyLogSource', null, $loggingPath.'SurveyLogSource'.$phpClassSuffix);
	 	$this->setConfValue('class', 'SurveyHelpLogSource', null, $loggingPath.'SurveyHelpLogSource'.$phpClassSuffix);
	 	$this->setConfValue('class', 'SurveyResultLogSource', null, $loggingPath.'SurveyResultLogSource'.$phpClassSuffix);

	 	//dann die Anwendungseigenen Klassen
	 	$classPath = 'classes/';
	 	$this->setConfValue('class', 'ucLinkbox', null, $classPath.'uc_linkbox'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucMaintenance', null, $classPath.'uc_maintenance'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucMenu', null, $classPath.'uc_menu'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucInfo', null, $classPath.'uc_info'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucStatic', null, $classPath.'uc_static'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucSession', null, $classPath.'uc_session'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucLogin', null, $classPath.'uc_login'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucLogout', null, $classPath.'uc_logout'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucRegistration', null, $classPath.'uc_registration'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucChangeUserData', null, $classPath.'uc_change_userdata'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucFeedback', null, $classPath.'uc_feedback'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucForgotPwd', null, $classPath.'uc_forgot_pwd'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucOverview', null, $classPath.'uc_overview'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucStart', null, $classPath.'uc_start'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucSponsorBox', null, $classPath.'uc_sponsorbox'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucSurvey', null, $classPath.'uc_survey'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucSurveyHelp', null, $classPath.'uc_survey_help'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucSurveyResult', null, $classPath.'uc_survey_result'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucGraphics', null, $classPath.'uc_graphics'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucGetPdf', null, $classPath.'uc_get_pdf'.$phpClassSuffix);
	 	$this->setConfValue('class', 'ucPanoramaViewer', null, $classPath.'uc_panorama'.$phpClassSuffix);
	 	//Spezialklassen
	 	$this->setConfValue('class', 'ImageCreator', null, $classPath.'image_creator'.$phpClassSuffix);
	 	$this->setConfValue('class', 'AdvisorPdf', null, $classPath.'pdf_creator'.$phpClassSuffix);
	 	$this->setConfValue('class', 'PdfResult', null, $classPath.'pdf_result'.$phpClassSuffix);
	 	$this->setConfValue('class', 'CharResult', null, $classPath.'char_result'.$phpClassSuffix);
	 	$this->setConfValue('class', 'SurveyResult', null, $classPath.'survey_result'.$phpClassSuffix);

	 	//Model-Klassen
	 	$modelPath = $classPath.'model/';
	 	$this->setConfValue('class', 'Survey', null, $modelPath.'survey'.$phpClassSuffix);
	 	$this->setConfValue('class', 'Question', null, $modelPath.'question'.$phpClassSuffix);
	 	$this->setConfValue('class', 'Answer', null, $modelPath.'answer'.$phpClassSuffix);
	 	$this->setConfValue('class', 'SurveyBlock', null, $modelPath.'survey_block'.$phpClassSuffix);
	 	$this->setConfValue('class', 'Characteristic', null, $modelPath.'characteristic'.$phpClassSuffix);
	 	$this->setConfValue('class', 'Group', null, $modelPath.'group'.$phpClassSuffix);
	 	$this->setConfValue('class', 'Info', null, $modelPath.'info'.$phpClassSuffix);
	 	$this->setConfValue('class', 'User', null, $modelPath.'user'.$phpClassSuffix);
	 	$this->setConfValue('class', 'TextElement', null, $modelPath.'text_element'.$phpClassSuffix);
	 	$this->setConfValue('class', 'QuestionType', null, $modelPath.'question_type'.$phpClassSuffix);
	 	$this->setConfValue('class', 'QuestionBlock', null, $modelPath.'question_block'.$phpClassSuffix);
	 	$this->setConfValue('class', 'Rating', null, $modelPath.'rating'.$phpClassSuffix);
	 	//Bibliotheken
	 	$libraryPath = $classPath.'lib/';
	 	$this->setConfValue('class', 'FPDF', null, $libraryPath.'fpdf/fpdf'.$phpClassSuffix);
	 	$this->setConfValue('class', 'htmlMimeMail5', null, $libraryPath.'mimemail/htmlMimeMail5'.$phpClassSuffix);
	 	
	 	//Matching von UseCaseName auf entsprechenden Klassennamen
	 	$this->setConfValue('usecase', 'menu', null, 'ucMenu');
	 	$this->setConfValue('usecase', 'maintenance', null, 'ucMaintenance');
	 	$this->setConfValue('usecase', 'session', null, 'ucSession');
	 	$this->setConfValue('usecase', 'error', null, 'ucError');
	 	$this->setConfValue('usecase', 'linkbox', null, 'ucLinkbox');
	 	$this->setConfValue('usecase', 'static', null, 'ucStatic');
	 	$this->setConfValue('usecase', 'info', null, 'ucInfo');
	 	$this->setConfValue('usecase', 'start', null, 'ucStart');
	 	$this->setConfValue('usecase', 'login', null, 'ucLogin');
	 	$this->setConfValue('usecase', 'logout', null, 'ucLogout');
	 	$this->setConfValue('usecase', 'registration', null, 'ucRegistration');
	 	$this->setConfValue('usecase', 'changeuserdata', null, 'ucChangeUserData');
	 	$this->setConfValue('usecase', 'feedback', null, 'ucFeedback');
	 	$this->setConfValue('usecase', 'forgotpassword', null, 'ucForgotPwd');
	 	$this->setConfValue('usecase', 'overview', null, 'ucOverview');
	 	$this->setConfValue('usecase', 'sponsorbox', null, 'ucSponsorBox');
	 	$this->setConfValue('usecase', 'survey', null, 'ucSurvey');
	 	$this->setConfValue('usecase', 'surveyhelp', null, 'ucSurveyHelp');
	 	$this->setConfValue('usecase', 'survey_result', null, 'ucSurveyResult');
	 	$this->setConfValue('usecase', 'graphics', null, 'ucGraphics');
	 	$this->setConfValue('usecase', 'getpdf', null, 'ucGetPdf');
	 	$this->setConfValue('usecase', 'panorama', null, 'ucPanoramaViewer');
	 	
	 	//rufe die Initialisierung der SQL-Statements auf
	 	$this->configureSql();
	 	
	 	//hier unten sollte der ï¿½bersicht halber jeder UseCase zumindest seine eigene initialize-Methode bekommen
	 	$this->configureUcStatic();
	 	$this->configureUcLogin();
	 	$this->configureUcRegistration();
	 	$this->configureUcSurvey();
	 	$this->configureUcSurveyResult();
	 	$this->configureUcOverview();
	 	$this->configureUcSurveyHelp();
 		$this->configureUcStart();
 		$this->configureUcInfo();
 		$this->configureUcSponsorBox();
 		$this->configureUcFeedback();
 		$this->configureUcForgotPwd();
 		$this->configureUcChangeUserData();
 		$this->configureUcSession();
 		$this->configureUcLinkbox();
 		$this->configureUcGetPdf();
 		$this->configureUcPanoramaViewer();
 		
 		$this->configureImageCreator();
	 }
	 
	 
	/**
	 * configureSql() fï¿½llt die Konfiguration mit den benï¿½tigten Sql-Abfragen
	 * Hier sind alle Tabellennamen abgelegt; nach Mï¿½glichkeit sollten hier alle verwendeten
	 * SQL-Statements (als "prepared Statement") bereits abgelegt werden, so dass sie zentral verfï¿½gbar sind
	 */
	 private function configureSql()
	 {
	 	//Tabellennamen fï¿½r die Datenbank
	 	$tablePrefix = 'advisor__';
	 	$loggingTablePrefix = $tablePrefix.'log_';
	 	
	 	//Allgemeine Tabellen
		$table['user'] = $tablePrefix.'user'; //speichert die Nutzerdaten
		$table['targetgroups'] = $tablePrefix.'targetgroups'; // enthï¿½lt die unterschiedlichen Zielgruppen
		$table['textelements'] = $tablePrefix.'textelements'; // enthï¿½lt Textbausteine
		$table['te_typ'] = $tablePrefix.'textelement_types'; // Typisiert (=gruppiert) die Textbausteine
		$table['tg_te'] = $tablePrefix.'targetgroups_textelements'; // legt fest, welche Textbausteine fï¿½r welche Zielgruppen geeignet sind
		
		// Logging
		$table['log_actions'] = $loggingTablePrefix.'actions';
		$table['log_clicks'] = $loggingTablePrefix.'clicks';

		//Testtool
		$table['answers'] = $tablePrefix.'answers'; // Hier sind mï¿½gliche Antworten abgelegt
		$table['op_res'] = $tablePrefix.'open_results'; // Hier sind Antworten auf OFFENE Fragen abgelegt
		$table['questions'] = $tablePrefix.'questions'; // enthï¿½lt einzelne Fragen
		$table['qu_an'] = $tablePrefix.'questions_answers'; // ordnet den Fragen Antwortalternativen und entsprechende Bewertungseinheiten zu
		$table['results'] = $tablePrefix.'results'; // speichert die gegebenen Antworten der Nutzer
		$table['surveys'] = $tablePrefix.'surveys'; // enthï¿½lt die einzelnen Testmodule
		$table['us_sur'] = $tablePrefix.'users_surveys'; // speichert, wann ein Benutzer welches Testmodul bearbeitet hat
		$table['us_char'] = $tablePrefix.'users_chars'; // speichert das Ergebnis jedes Users in einem Merkmal
		$table['char'] = $tablePrefix.'characteristics'; // enthï¿½lt die Merkmale (z.B. Einzelkompetenzen), die geprï¿½ft werden
		$table['groups'] = $tablePrefix.'groups'; // Gruppierung der Merkmale (z.B. in Kompetenzklassen)
		$table['blocks'] = $tablePrefix.'blocks'; // Gruppierung der Tests
		$table['infos'] = $tablePrefix.'infos'; // Seiten der Informationsplattform
		$table['questiontypes'] = $tablePrefix.'questiontypes'; // Fragentypen
		$table['questionblocks'] = $tablePrefix.'questionblocks'; // Fragenblï¿½cke
		$table['open_results'] = $tablePrefix.'open_results'; // Fragenblï¿½cke
		$table['ratings'] = $tablePrefix.'ratings'; // Ergebnis zu Textelement-Zuordnung

	 	//Weitere "Hilfstabellen"
	 	$table['sponsors'] = $tablePrefix.'sponsors'; //speichert die "Sponsoren" vom Advisor (inkl. Link aufs Logo)
	 	$table['feedback'] = $tablePrefix.'feedback'; //speichert abgegebenes Feedback in der DB
	 	$table['infos'] = $tablePrefix.'infos'; // enthï¿½lt die einzelnen Infoseiten und ihre Kurzversionen
	 	 
		//SQL-Statements
		
			//Allgemein
			
			// Logging
			$this->setConfValue('sql', 'logging', 'actionId', 'SELECT a.aid FROM '.$table['log_actions'].' a WHERE a.action = "?"');
			$this->setConfValue('sql', 'logging', 'firstQuestionBlock', 'SELECT a.qbid FROM '.$table['questionblocks'].' a WHERE a.sid = "?" AND a.position <= (SELECT MIN(position) FROM '.$table['questionblocks'].')');
			$this->setConfValue('sql', 'logging', 'nextQuestionBlockPosition', 'SELECT MIN(a.position) AS position FROM '.$table['questionblocks'].' a, '.$table['questionblocks'].' b WHERE a.sid = "?" AND a.position > b.position AND b.qbid = "?"');
			$this->setConfValue('sql', 'logging', 'nextQuestionBlock', 'SELECT a.qbid FROM '.$table['questionblocks'].' a WHERE a.sid = "?" AND a.position = "?"');
			$this->setConfValue('sql', 'logging', 'click', 'INSERT INTO '.$table['log_clicks'].'(uid, sid, aid, data) VALUES("?", "?", "?", "?")');
			
			
			//Session_Handling
			$this->setConfValue('sql', 'session', 'authentication', 'SELECT * FROM '.$table['user'].' WHERE email = "?" AND passwd = "?" AND confirmed = "true"');
			
			//Registrierung
			$this->setConfValue('sql', 'registration', 'verificate', 'SELECT uid FROM '.$table['user'].' WHERE username = "?" AND auth_code = "?"');
			$this->setConfValue('sql', 'registration', 'verificate_update', 'UPDATE '.$table['user'].' SET confirmed = "true" WHERE username = "?" AND auth_code = "?"');
			
			//Sponsoranzeige
			$this->setConfValue('sql', 'sponsors', 'randomSponsor', 'SELECT sponsorname, logo, href FROM '.$table['sponsors'].' ORDER BY RAND() LIMIT 1');
			$this->setConfValue('sql', 'sponsors', 'allSponsors', 'SELECT sponsorname, logo, href FROM '.$table['sponsors'].' ORDER BY RAND()');
			$this->setConfValue('sql', 'sponsors', 'usercount', 'SELECT count(uid) as zahl FROM '.$table['user']);
			
			//Feebackbogen
		 	$this->setConfValue('sql', 'feedback', 'saveInDb', 'INSERT INTO '.$table['feedback'].' (subject, reference, email, sender, uid, message) VALUES ("?", "?", "?", "?", "?", "?")');

			//Passwort vergessen
			$this->setConfValue('sql', 'user', 'validEmail', 'SELECT uid FROM '.$table['user'].' WHERE email = "?"');

	 		// SQL fï¿½r die Modell-Klassen
	 		// Klasse SurveyBlock
		 	$this->setConfValue('sql', 'survey_block', 'getForId', 'SELECT * FROM '.$table['blocks'].' WHERE blid=?');
		 	$this->setConfValue('sql', 'survey_block', 'getAll', 'SELECT * FROM '.$table['blocks'].' ORDER BY position');
		 	
	 		// Klasse Info
		 	$this->setConfValue('sql', 'info', 'getForId', 'SELECT * FROM '.$table['infos'].' WHERE inid=?');
		 	$this->setConfValue('sql', 'info', 'getForSid', 'SELECT * FROM '.$table['infos'].' WHERE sid=?');
		 	$this->setConfValue('sql', 'info', 'getAll', 'SELECT * FROM '.$table['infos'].' ORDER BY position');
		 	$this->setConfValue('sql', 'info', 'getForBlock', 'SELECT * FROM '.$table['infos'].' WHERE blid=? ORDER BY position');
		 	$this->setConfValue('sql', 'info', 'getFirstForSurveyBlock', 'SELECT * FROM '.$table['infos'].' i WHERE i.blid=? AND i.position <= (SELECT MIN(i2.position) FROM '.$table['infos'].' i2 WHERE i2.blid=i.blid )');
		 	
	 		// Klasse Survey
		 	$this->setConfValue('sql', 'survey', 'getForId', 'SELECT * FROM '.$table['surveys'].' WHERE sid=?');
		 	$this->setConfValue('sql', 'survey', 'getAll', 'SELECT * FROM '.$table['surveys'].' ORDER BY position');
		 	$this->setConfValue('sql', 'survey', 'getForBlock', 'SELECT * FROM '.$table['surveys'].' WHERE blid=? ORDER BY position');
		 	$this->setConfValue('sql', 'survey', 'getForGroup', 'SELECT * FROM '.$table['surveys'].' WHERE gid=? ORDER BY sid');
		 	$this->setConfValue('sql', 'survey', 'getFirstForSurveyBlock', 'SELECT * FROM '.$table['surveys'].' s WHERE s.blid=? AND s.position <= (SELECT Min(s2.position) FROM '.$table['surveys'].' s2 WHERE s2.blid=s.blid )');

		 	$this->setConfValue('sql', 'survey', 'clearAnswers', 'DELETE FROM '.$table['results'].' WHERE uid = ? AND attempt = ? AND quid IN (SELECT q.quid FROM '.$table['questions'].' q, '.$table['questionblocks'].' qb WHERE q.qbid=qb.qbid AND qb.sid=?)');
		 	$this->setConfValue('sql', 'survey', 'clearCompleted', 'DELETE FROM '.$table['us_sur'].' WHERE uid = ? AND sid = ? AND attempt = ?');
		 	$this->setConfValue('sql', 'survey', 'storeComplete', 'INSERT INTO '.$table['us_sur'].' (sid, uid, completed_date, attempt, result) VALUES (?, ?, "?", ?, "?")');
		 	$this->setConfValue('sql', 'survey', 'storeUserResults', 'INSERT INTO '.$table['us_char'].' (uid, sid, chid, attempt, result) VALUES (?, ?, ?, ?, "?")');
		 	$this->setConfValue('sql', 'survey', 'getAttemptForUser', 'SELECT Max(attempt) as maxattempt FROM '.$table['us_sur'].' WHERE sid=? AND uid=?');
		 	
		 	// Alte Version zu langsam:
		 	//$this->setConfValue('sql', 'survey', 'getResultsForChar', 'SELECT SUM(rating)/(SELECT COUNT(*) FROM '.$table['questions'].' q2, '.$table['questionblocks'].' qb2 WHERE q2.qbid=qb2.qbid AND qb2.sid=qb.sid AND q.chid=q2.chid) as rating, r.uid FROM '.$table['results'].' r,'.$table['qu_an'].' qa,'.$table['questions'].' q, '.$table['questionblocks'].' qb WHERE qa.quid=r.quid AND qa.anid=r.anid AND r.quid=q.quid AND q.qbid= qb.qbid AND qb.sid = ? AND q.chid = ? AND r.attempt = ? GROUP BY r.uid;');
		 	$this->setConfValue('sql', 'survey', 'getResultsForChar', 'SELECT uid, result FROM '.$table['us_char'].' WHERE sid = ? AND chid = ? AND attempt = ? ORDER BY result');
		 	// Nicht immer exakt
		 	//$this->setConfValue('sql', 'survey', 'getResultForUserChar', 'SELECT SUM(rating)/(SELECT COUNT(*) FROM '.$table['questions'].' q2, '.$table['questionblocks'].' qb2 WHERE q2.qbid=qb2.qbid AND qb2.sid=qb.sid AND q.chid=q2.chid) as rating FROM '.$table['results'].' r,'.$table['qu_an'].' qa,'.$table['questions'].' q, '.$table['questionblocks'].' qb WHERE qa.quid=r.quid AND qa.anid=r.anid AND r.quid=q.quid AND q.qbid= qb.qbid AND qb.sid = ? AND q.chid = ? AND r.attempt = ? AND r.uid = ?');
		 	$this->setConfValue('sql', 'survey', 'getResultForUserChar', 'SELECT uid, result FROM '.$table['us_char'].' WHERE uid = ? AND sid = ? AND chid = ? AND attempt = ? ORDER BY result');
		 	//$this->setConfValue('sql', 'survey', 'getCurrentAttemptForUser', 'SELECT Max(r.attempt) as maxattempt FROM '.$table['results'].' r, '.$table['questions'].' q, '.$table['questionblocks'].' qb WHERE r.quid=q.quid AND q.qbid=qb.qbid AND qb.sid=? AND r.uid=?');
		 	
		 	// Zu langsam:
		 	//$this->setConfValue('sql', 'survey', 'getAverageResultForChar', 'SELECT SUM(qa.rating) / (Count(DISTINCT r.uid)*Count(DISTINCT r.quid)) AS averagerating FROM advisor__results r, advisor__questions_answers qa, advisor__questions q, advisor__questionblocks qb WHERE r.uid IN (SELECT uid FROM advisor__users_surveys WHERE sid = ?) AND r.quid = q.quid AND q.qbid = qb.qbid AND qb.sid = ? AND q.chid = ? AND attempt = ? AND r.quid = qa.quid AND r.anid = qa.anid;');
		 	$this->setConfValue('sql', 'survey', 'getAverageResultForChar', 'SELECT AVG(result) AS averagerating FROM '.$table['us_char'].' WHERE sid = ? AND chid = ? AND attempt = ?');
		 	$this->setConfValue('sql', 'survey', 'getAverageResult', 'SELECT AVG(result) AS averageresult FROM '.$table['us_sur'].' WHERE sid = ? AND attempt = ?');
		 	// Alte Version zu langsam:
		 	//$this->setConfValue('sql', 'survey', 'getAllResults', 'SELECT SUM(rating)/(SELECT COUNT(*) FROM '.$table['questions'].' q2, '.$table['questionblocks'].' qb2 WHERE q2.qbid=qb2.qbid AND qb2.sid=qb.sid AND q.chid=q2.chid) as rating, r.uid, ch.chid, ch.lower_target FROM '.$table['results'].' r,'.$table['qu_an'].' qa,'.$table['questions'].' q, '.$table['questionblocks'].' qb, '.$table['char'].' ch WHERE qa.quid=r.quid AND qa.anid=r.anid AND r.quid=q.quid AND q.qbid= qb.qbid AND q.chid=ch.chid AND qb.sid = ? AND r.attempt = ? GROUP by r.uid, ch.chid, ch.lower_target');
		 	$this->setConfValue('sql', 'survey', 'getAllResults', 'SELECT * FROM '.$table['us_sur'].' WHERE sid = ? AND attempt = ? ORDER BY result');
		 	$this->setConfValue('sql', 'survey', 'getResultForUser', 'SELECT * FROM '.$table['us_sur'].' WHERE uid = ? AND sid = ? AND attempt = ?');
		 	$this->setConfValue('sql', 'survey', 'getUserRank', 'SELECT (count(*) / (SELECT count(*) FROM '.$table['us_sur'].' us2 WHERE us2.sid = us1.sid AND us2.attempt = us1.attempt ))*100 as rank FROM '.$table['us_sur'].' us1 WHERE us1.sid = ? AND us1.attempt = 1 AND us1.result <= ?');
		 	
		 	// Klasse QuestionBlock
		 	$this->setConfValue('sql', 'question_block', 'getForId', 'SELECT * FROM '.$table['questionblocks'].' WHERE qbid=?');
		 	$this->setConfValue('sql', 'question_block', 'getAll', 'SELECT * FROM '.$table['questionblocks'].' ORDER BY qbid');
		 	$this->setConfValue('sql', 'question_block', 'getForSurvey', 'SELECT * FROM '.$table['questionblocks'].' WHERE sid=? ORDER BY position');
		 	$this->setConfValue('sql', 'question_block', 'getFirstForSurvey', 'SELECT * FROM '.$table['questionblocks'].' WHERE sid=? AND position <= (SELECT Min(position) FROM '.$table['questionblocks'].' WHERE sid=? )');
		 	
		 	// Klasse Question
		 	$this->setConfValue('sql', 'question', 'getForId', 'SELECT * FROM '.$table['questions'].' WHERE quid=?');
		 	$this->setConfValue('sql', 'question', 'getAll', 'SELECT * FROM '.$table['questions'].' ORDER BY chid');
		 	$this->setConfValue('sql', 'question', 'getForSurvey', 'SELECT * FROM '.$table['questions'].' q, '.$table['questionblocks'].' qb WHERE q.qbid=qb.qbid AND qb.sid=? ORDER BY qb.position, q.position');
		 	$this->setConfValue('sql', 'question', 'getForQuestionBlock', 'SELECT * FROM '.$table['questions'].' WHERE qbid=? ORDER BY position');
		 	$this->setConfValue('sql', 'question', 'getOpenAnswer', 'SELECT * FROM '.$table['open_results'].'  WHERE quid = ? AND uid = ? AND attempt = ?');
		 	$this->setConfValue('sql', 'question', 'storeOpenAnswer', 'INSERT INTO '.$table['open_results'].' (quid, uid, openresult, attempt) VALUES (?, ?, \'?\', ?)');
		 	$this->setConfValue('sql', 'question', 'clearAnswers', 'DELETE FROM '.$table['results'].' WHERE quid = ? AND uid = ? AND attempt = ?');
		 	$this->setConfValue('sql', 'question', 'clearOpenAnswers', 'DELETE FROM '.$table['open_results'].' WHERE quid = ? AND uid = ? AND attempt = ?');
		 	
		 	// Klasse Answer
		 	$this->setConfValue('sql', 'answer', 'getForId', 'SELECT qa.qid, qa.anid, a.answer, qa.position, qa.rating FROM '.$table['answers'].' a, '.$table['qu_an'].' qa WHERE a.anid=qa.anid AND a.anid=? AND qa.qid=?');
		 	$this->setConfValue('sql', 'answer', 'getForQuestion', 'SELECT qa.quid, qa.anid, a.answer, qa.position, qa.rating FROM '.$table['answers'].' a, '.$table['qu_an'].' qa WHERE a.anid=qa.anid AND qa.quid=? ORDER BY qa.position');
		 	$this->setConfValue('sql', 'answer', 'getForQuestionUser',	'SELECT qa.quid, qa.anid, a.answer, qa.position, qa.rating FROM '.$table['answers'].' a, '.$table['qu_an'].' qa, '.$table['results'].' r WHERE a.anid = qa.anid AND qa.quid = ? AND r.quid = qa.quid AND r.anid = qa.anid AND r.uid = ? AND r.attempt = ?');
		 	$this->setConfValue('sql', 'answer', 'storeForUser', 'INSERT INTO '.$table['results'].' (quid, anid, uid, attempt) VALUES (?, ?, ?, ?)');
		 	$this->setConfValue('sql', 'answer', 'isSelectedForUser', 'SELECT count(*) AS selected FROM '.$table['results'].' WHERE quid = ? AND  anid = ? AND uid = ? AND attempt = ?');
		 	
		 	// Klasse Group
		 	$this->setConfValue('sql', 'group', 'getForId', 'SELECT * FROM '.$table['groups'].' WHERE gid=?');
		 	$this->setConfValue('sql', 'group', 'getAll', 'SELECT * FROM '.$table['groups'].' ORDER BY gid');
		 	
		 	// Klasse Characteristic
		 	$this->setConfValue('sql', 'characteristic', 'getForId', 'SELECT * FROM '.$table['char'].' WHERE chid=?');
		 	$this->setConfValue('sql', 'characteristic', 'getAll', 'SELECT * FROM '.$table['char'].' ORDER BY gid, chid');
		 	$this->setConfValue('sql', 'characteristic', 'getForSurvey', 'SELECT DISTINCT c.chid, c.characteristic, c.gid , c.lower_target, c.upper_target, c.show_result FROM '.$table['char'].' c, '.$table['questions'].' q, '.$table['questionblocks'].' qb WHERE c.chid = q.chid AND q.qbid = qb.qbid AND qb.sid = ?');
		 	$this->setConfValue('sql', 'characteristic', 'getForBlock', 'SELECT DISTINCT c.chid, c.characteristic, c.gid , c.lower_target, c.upper_target, c.show_result FROM '.$table['char'].' c, '.$table['questions'].' q, '.$table['questionblocks'].' qb, '.$table['surveys'].' s WHERE c.chid = q.chid AND q.qbid = qb.qbid AND qb.sid = s.sid AND s.blid = ?');
		 	
		 	// Klasse User
		 	$this->setConfValue('sql', 'user', 'getForId', 'SELECT * FROM '.$table['user'].' WHERE uid=?');
		 	$this->setConfValue('sql', 'user', 'getAll', 'SELECT * FROM '.$table['user'].' ORDER BY uid');
		 	$this->setConfValue('sql', 'user', 'storeUpdate', 'UPDATE '.$table['user'].' SET username = "?", email = "?", passwd = "?", gender = "?", birthday = "?", tgid = "?", confirmed = "?", auth_code = "?", type = "?", matnr = "?", studies = "?" WHERE uid = "?"');
			$this->setConfValue('sql', 'user', 'storeInsert', 'INSERT INTO '.$table['user'].' (username, email, passwd, gender, birthday, tgid, confirmed, auth_code, matnr, studies, type) VALUES ("?", "?", "?", "?", "?", "?", "?", "?", "?", "?", "user")');
		 	
		 	// Klasse TextElement
		 	$this->setConfValue('sql', 'text_element', 'getForId', 'SELECT * FROM '.$table['textelements'].' WHERE teid=?');
		 	$this->setConfValue('sql', 'text_element', 'getAll', 'SELECT * FROM '.$table['textelements'].' ORDER BY teid');
		 	
		 	// Klasse InfoPage
		 	$this->setConfValue('sql', 'info_page', 'getForId', 'SELECT * FROM '.$table['infos'].' WHERE inid=?');
		 	$this->setConfValue('sql', 'info_page', 'getAll', 'SELECT * FROM '.$table['infos'].' ORDER BY inid');
		 	
		 	// Klasse QuestionType
		 	$this->setConfValue('sql', 'question_type', 'getForId', 'SELECT * FROM '.$table['questiontypes'].' WHERE qtid=?');
		 	$this->setConfValue('sql', 'question_type', 'getAll', 'SELECT * FROM '.$table['questiontypes'].' ORDER BY qtid');
		 	
		 	// Klasse Rating
		 	$this->setConfValue('sql', 'rating', 'getForId', 'SELECT * FROM '.$table['ratings'].' WHERE raid=?');
		 	$this->setConfValue('sql', 'rating', 'getAll', 'SELECT * FROM '.$table['ratings'].' ORDER BY raid');
		 	$this->setConfValue('sql', 'rating', 'getForResult', 'SELECT * FROM '.$table['ratings'].' WHERE chid = ? AND tgid = ? AND type = "?" AND lower_limit <= ? AND ? <= upper_limit');
			
	 }

	/**
	 * configureUcStatic() fï¿½llt die Konfiguration mit den benï¿½tigten Daten fï¿½r ucStatic
	 */
	 private function configureUcStatic()
	 {
		$this->setConfValue('ucStatic', 'templatePath', 'all', 'templates/static/'); //Pfad, in dem die statischen Templates (ï¿½ffentliche, die jeder ansehen darf) abgelegt sind
		$this->setConfValue('ucStatic', 'templatePath', 'authenticated', 'templates/static/authenticated/'); //Pfad, in dem die statischen Templates (nur fï¿½r eingeloggte Nutzer) abgelegt sind
		$this->setConfValue('ucStatic', 'suffix', null, '.tpl'); //Datei-Suffix der Templates
	 }

	/**
	 * configureUcLogin() fï¿½llt die Konfiguration mit den benï¿½tigten Daten fï¿½r ucLogin
	 */
	 private function configureUcLogin()
	 {
		$this->setConfValue('ucLogin', 'loginform_tpl', null, 'templates/ucLogin/loginform.tpl'); //Template fï¿½r das Loginfenster
		$this->setConfValue('ucLogin', 'loginbox_tpl', null, 'templates/ucLogin/loginbox.tpl'); //Template fï¿½r die kleine Loginbox
		//Variable Teile im Template:
		$this->setConfValue('ucLogin', 'loginaction', null, 'loginaction');
		$this->setConfValue('ucLogin', 'registeraction', null, 'registeraction');
		$this->setConfValue('ucLogin', 'datenschutzlink', null, 'datenschutzlink');
		$this->setConfValue('ucLogin', 'passwortlink', null, 'passwortlink');
		$this->setConfValue('ucLogin', 'showerror', null, 'showerror');
		$this->setConfValue('ucLogin', 'targetlink', null, 'targetlink');
		//Fehlermeldung(en):
		$this->setConfValue('ucLogin', 'error', 'noAuthentication', 'Deine eMailadresse und das Passwort passen nicht zusammen. Falls du unser System zum ersten Mal benutzt, musst du dich erst registrieren.');
	 }

	 private function configureUcSession()
	 {
		$this->setConfValue('ucSession', 'authenticated_tpl', null, 'templates/ucSession/authenticated.tpl'); //Template fï¿½r authentifizierte Benutzer
		//Variable Teile im Template:
		$this->setConfValue('ucSession', 'username', null, 'USERNAME');
		$this->setConfValue('ucSession', 'useremail', null, 'USEREMAIL');
		$this->setConfValue('ucSession', 'logout', null, 'LOGOUTLINK');
		$this->setConfValue('ucSession', 'changedata', null, 'CHANGEDATALINK');
	 }

	 private function configureUcSponsorBox()
	 {
		$this->setConfValue('ucSponsorBox', 'sponsorbox_tpl', null, 'templates/ucSponsorBox/sponsorbox.tpl'); //Template
		//Pfad zu den Logos:
		$this->setConfValue('ucSponsorBox', 'imagePath', null, 'grafik/sponsors/');
		//Variable Teile im Template:
		$this->setConfValue('ucSponsorBox', 'logo', null, 'LOGO');
		$this->setConfValue('ucSponsorBox', 'href', null, 'HREF');
		$this->setConfValue('ucSponsorBox', 'sponsorname', null, 'SPONSORNAME');
		$this->setConfValue('ucSponsorBox', 'usercount', null, 'USERCOUNT');
		//UseCases, bei denen KEINE Sponsorbox erscheinen soll:
		$this->setConfValue('ucSponsorBox', 'exceptionsWithoutBox', null, Array('start'));
	 }

	/**
	 * configureUcChangeUserData() fï¿½llt die Konfiguration mit den benï¿½tigten Daten
	 */
	 private function configureUcChangeUserData()
	 {
		$this->setConfValue('ucChangeUserData', 'changeform_tpl', null, 'templates/ucChangeUserData/changeform.tpl'); //Template fï¿½r das Registrierungsfenster
		
		//Variable Teile im Template:
		$this->setConfValue('ucChangeUserData', 'message', null, 'MESSAGE');
		$this->setConfValue('ucChangeUserData', 'username', null, 'USERNAME');
		$this->setConfValue('ucChangeUserData', 'email', null, 'EMAIL');
		$this->setConfValue('ucChangeUserData', 'password', null, 'PASSWORD');
		$this->setConfValue('ucChangeUserData', 'password_rep', null, 'PASSWORDREPEAT');
		$this->setConfValue('ucChangeUserData', 'gender', null, 'GENDER');
		$this->setConfValue('ucChangeUserData', 'birthday', null, 'BIRTHDAY');
		$this->setConfValue('ucChangeUserData', 'studies', null, 'STUDIES');
		$this->setConfValue('ucChangeUserData', 'matnr', null, 'MATNR');
		$this->setConfValue('ucChangeUserData', 'submit', null, 'SUBMIT');
		$this->setConfValue('ucChangeUserData', 'reset', null, 'RESET');
		
		//reguläre Ausdrücke zur Überprüfung der Eingaben: 
		//Hinweis: aufgrund der PHP-Stringbehandlung müssen die Regexe in doppelte Anführungszeichen gesetzt werden!
		$this->setConfValue('ucChangeUserData', 'regex', 'email', "/^(([a-z0-9_-]+(\\.[a-z0-9_-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\\.)+([a-z]{2,4}|museum)))$/i"); //gï¿½ltige (i.S. von mï¿½gliche) eMailadresse oder leer
		$this->setConfValue('ucChangeUserData', 'regex', 'password', "/^([\&\ï¿½\!\?\=\%\ï¿½\ï¿½\ï¿½\ï¿½a-z0-9_-]{5,100})$/i"); //5-100 Zeichen, Alphabeth, Zahlen, teilw. Sonderzeichen
		$this->setConfValue('ucChangeUserData', 'regex', 'gender', "/^([muw]{1,1})$/i"); //Geschlecht
		$this->setConfValue('ucChangeUserData', 'regex', 'birthday', "/^([\\d]{1,4})$/i"); //Jahreszahl oder 0
		$this->setConfValue('ucChangeUserData', 'regex', 'username', "/^([ \ï¿½\ï¿½\ï¿½\ï¿½a-z0-9_-]{0,100})$/i"); //0-100 Zeichen, Alphabeth, Zahlen, teilw. Sonderzeichen
		
		//Fehlermeldung(en):
		$this->setConfValue('ucChangeUserData', 'error', 'username', 'Dein Name darf aus h&ouml;chstens 100 Zeichen bestehen und darf keine Sonderzeichen enthalten. ');
		$this->setConfValue('ucChangeUserData', 'error', 'email', 'Deine eMailadresse ist ung&uuml;ltig. ');
		$this->setConfValue('ucChangeUserData', 'error', 'gender', 'Die Angabe des Geschlechts ist ung&uuml;ltig! ');
		$this->setConfValue('ucChangeUserData', 'error', 'birthday', 'Die Geburtstagsangabe ist ung&uuml;ltig! ');
		$this->setConfValue('ucChangeUserData', 'error', 'password', 'Dein Passwort muss aus mindestens 5 Zeichen bestehen. ');
		$this->setConfValue('ucChangeUserData', 'error', 'password_repeat', 'Passwort und Passwort-Wiederholung m&uuml;ssen &uuml;bereinstimmen. ');
		
		//Nachricht:
		$this->setConfValue('ucChangeUserData', 'message_text', 'stored', 'Wir haben Deine &Auml;nderungen erfolgreich gespeichert.');
	 }
	 
	/**
	 * configureUcRegistration() fï¿½llt die Konfiguration mit den benï¿½tigten Daten fï¿½r ucRegistration
	 */
	 private function configureUcRegistration()
	 {
		$this->setConfValue('ucRegistration', 'registrationform_tpl', null, 'templates/ucRegistration/registrationform.tpl'); //Template fï¿½r das Registrierungsfenster
		$this->setConfValue('ucRegistration', 'confirmation_tpl', null, 'templates/ucRegistration/confirmation.tpl'); //Template fï¿½r die Bestï¿½tigungsseite
		$this->setConfValue('ucRegistration', 'error_verification_tpl', null, 'templates/ucRegistration/verification_error.tpl'); //Template fï¿½r die Bestï¿½tigungsseite
		$this->setConfValue('ucRegistration', 'confirm_verification_tpl', null, 'templates/ucRegistration/verification_confirmation.tpl'); //Template fï¿½r die Bestï¿½tigungsseite
		
		//Variable Teile im Template:
		$this->setConfValue('ucRegistration', 'registeraction', null, 'registeraction');
		$this->setConfValue('ucRegistration', 'datenschutzlink', null, 'datenschutzlink');
		$this->setConfValue('ucRegistration', 'targetlink', null, 'targetlink');
		$this->setConfValue('ucRegistration', 'showerror', null, 'showerror');
		$this->setConfValue('ucRegistration', 'email', null, 'email');
		$this->setConfValue('ucRegistration', 'username', null, 'username');
		$this->setConfValue('ucRegistration', 'password', null, 'password');
		$this->setConfValue('ucRegistration', 'gender', null, 'gender');
		$this->setConfValue('ucRegistration', 'birthday', null, 'birthday');
		$this->setConfValue('ucRegistration', 'matnr', null, 'matnr');
		$this->setConfValue('ucRegistration', 'studies', null, 'studies');
		
		//regulï¿½re Ausdrï¿½cke zur ï¿½berprï¿½fung der Eingaben: 
		//Hinweis: aufgrund der PHP-Stringbehandlung mï¿½ssen die Regexe in doppelte Anfï¿½hrungszeichen gesetzt werden!
		$this->setConfValue('ucRegistration', 'regex', 'username', "/^([ \ï¿½\ï¿½\ï¿½\ï¿½a-z0-9_-]{0,100})$/i"); //0-100 Zeichen, Alphabeth, Zahlen, teilw. Sonderzeichen
		$this->setConfValue('ucRegistration', 'regex', 'email', "/^(([a-z0-9_-]+(\\.[a-z0-9_-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\\.)+([a-z]{2,4}|museum)))$/i"); //gï¿½ltige (i.S. von mï¿½gliche) eMailadresse oder leer
		$this->setConfValue('ucRegistration', 'regex', 'password', "/^([\&\ï¿½\!\?\=\%\ï¿½\ï¿½\ï¿½\ï¿½a-z0-9_-]{5,100})$/i"); //5-100 Zeichen, Alphabeth, Zahlen, teilw. Sonderzeichen
		$this->setConfValue('ucRegistration', 'regex', 'gender', "/^([muw]{1,1})$/i"); //Geschlecht
		$this->setConfValue('ucRegistration', 'regex', 'birthday', "/^([\\d]{1,4})$/i"); //Jahreszahl oder 0
		
		//Fehlermeldung(en):
		$this->setConfValue('ucRegistration', 'error', 'username', 'Dein Name darf aus h&ouml;chstens 100 Zeichen bestehen und darf keine Sonderzeichen enthalten. ');
		$this->setConfValue('ucRegistration', 'error', 'email', 'Bitte gib eine g&uuml;ltige eMailadresse ein. ');
		$this->setConfValue('ucRegistration', 'error', 'gender', 'Die Angabe des Geschlechts ist ung&uuml;lltig! ');
		$this->setConfValue('ucRegistration', 'error', 'birthday', 'Die Geburtstagsangabe ist ung&uuml;lltig! ');
		$this->setConfValue('ucRegistration', 'error', 'password', 'Dein Passwort muss aus mindestens 5 Zeichen bestehen. ');
		$this->setConfValue('ucRegistration', 'error', 'password_repeat', 'Passwort und Passwort-Wiederholung m&uuml;lssen &uuml;lbereinstimmen. ');
		$this->setConfValue('ucRegistration', 'error', 'double_email', 'Wir konnten Dich nicht registrieren, weil die eMailadresse schon vergeben ist. ');
		$this->setConfValue('ucRegistration', 'error', 'datenschutz_akzeptieren', 'Du musst der Verwendung Deiner Daten zustimmen, ansonsten kannst Du Dich nicht registrieren. ');
		
		//Registrierungsvorgang:
		$this->setConfValue('ucRegistration', 'authenticationLength', null, 50); //Lï¿½nge des Authentifizierungscode
		
		//eMail-Konfiguration:
		$this->setConfValue('ucRegistration', 'email_confirmation', 'sender', $this->getConfString('common', 'email', 'sender'));
		$this->setConfValue('ucRegistration', 'email_confirmation', 'replyto', $this->getConfString('common', 'email', 'replyto'));
		$this->setConfValue('ucRegistration', 'email_confirmation', 'subject', 'WiSo@visor - Deine Registrierung');
//		$this->setConfValue('ucRegistration', 'email_confirmation', 'subject', 'WiSo@visor-Registrierung bestï¿½tigen');
//		$this->setConfValue('ucRegistration', 'email_confirmation', 'template', 'templates/ucRegistration/email.tpl');
		$this->setConfValue('ucRegistration', 'email_confirmation', 'template', 'templates/ucRegistration/email_without_confirmation.tpl');
		$this->setConfValue('ucRegistration', 'email_confirmation', 'username', 'username'); //Ersetzung fï¿½r den Usernamen
		$this->setConfValue('ucRegistration', 'email_confirmation', 'email', 'email'); //Ersetzung fï¿½r die eMailadresse
		$this->setConfValue('ucRegistration', 'email_confirmation', 'link', 'link'); //Ersetzung fï¿½r den Bestï¿½tigungslink
	 }
	 
	/**
	 * configureUcFeedback() fï¿½llt die Konfiguration mit den benï¿½tigten Daten
	 */
	 private function configureUcFeedback()
	 {
		$this->setConfValue('ucFeedback', 'feedbackform_tpl', null, 'templates/ucFeedback/feedbackform.tpl'); //Template fï¿½r das Formular
		$this->setConfValue('ucFeedback', 'confirmation_tpl', null, 'templates/ucFeedback/confirmation.tpl'); //Template fï¿½r die Bestï¿½tigungsseite
		//Variable Teile in den Templates:
		$this->setConfValue('ucFeedback', 'emailaddy', null, 'EMAIL');
		$this->setConfValue('ucFeedback', 'sender', null, 'SENDER');
		$this->setConfValue('ucFeedback', 'subject', null, 'SUBJECT');
		$this->setConfValue('ucFeedback', 'message', null, 'MESSAGE');
		$this->setConfValue('ucFeedback', 'reference', null, 'REFERENCE');
		$this->setConfValue('ucFeedback', 'userid', null, 'USERID');
		$this->setConfValue('ucFeedback', 'username', null, 'USERNAME');
		$this->setConfValue('ucFeedback', 'useremail', null, 'USEREMAIL');
		$this->setConfValue('ucFeedback', 'showerror', null, 'SHOWERROR');
		$this->setConfValue('ucFeedback', 'submit', null, 'SUBMIT');
		$this->setConfValue('ucFeedback', 'reset', null, 'RESET');
		//regulï¿½re Ausdrï¿½cke zur ï¿½berprï¿½fung der Eingaben: 
		//Hinweis: aufgrund der PHP-Stringbehandlung mï¿½ssen die Regexe in doppelte Anfï¿½hrungszeichen gesetzt werden! - deshalb auch immer der doppelte Backslash
		$this->setConfValue('ucFeedback', 'regex', 'email', "/^(([a-z0-9_-]+(\\.[a-z0-9_-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\\.)+([a-z]{2,4}|museum)))$/i"); //gï¿½ltige (i.S. von mï¿½gliche) eMailadresse oder leer
		$this->setConfValue('ucFeedback', 'regex', 'subject', "/^([\\:\\.\\&\\ï¿½\\ \\!\\?\\=\\%\\ï¿½\\ï¿½\\ï¿½\\ï¿½a-z0-9_-]{1,255})$/i"); //1-255 Zeichen, Alphabeth, Zahlen, teilw. Sonderzeichen
		$this->setConfValue('ucFeedback', 'regex', 'sender', "/^([\\:\\.\\&\\ï¿½\\!\\ \\?\\=\\%\\ï¿½\\ï¿½\\ï¿½\\ï¿½a-z0-9_-]{0,255})$/i"); //0-255 Zeichen, Alphabeth, Zahlen, teilw. Sonderzeichen
		$this->setConfValue('ucFeedback', 'regex', 'reference', "/^([\\:\\.\\&\\ï¿½\\!\\?\\=\\%\\ï¿½\\ï¿½\\ï¿½\\ï¿½a-z0-9_-]{0,255})$/i"); //0-255 Zeichen, Alphabeth, Zahlen, teilw. Sonderzeichen
		$this->setConfValue('ucFeedback', 'regex', 'message', "/^([\\n\\r\\t\\d\\D\\s\\S\\w\\W\\:\\.\\&\\ï¿½\\ \\!\\?\\=\\%\\ï¿½\\ï¿½\\ï¿½\\ï¿½a-z0-9_-]+)$/i"); //nicht leer
		
		//Fehlermeldung(en):
		$this->setConfValue('ucFeedback', 'error', 'email', 'Die Angabe Deiner eMailadresse ist freiwillig; wenn Du eine eMailadresse angibst, dann muss diese gï¿½ltig sein. ');
		$this->setConfValue('ucFeedback', 'error', 'sender', 'Die Angabe Deines Namens ist freiwillig; wenn Du Deinen Namen angibst, darf dieser hï¿½chstens 255 Zeichen lang sein. ');
		$this->setConfValue('ucFeedback', 'error', 'subject', 'Der angegebene Betreff ist leer oder enthï¿½lt ungï¿½ltige Zeichen - erlaubt sind nur Buchstaben, Ziffern und Satzzeichen. ');
		$this->setConfValue('ucFeedback', 'error', 'message', 'Die Nachricht darf nicht leer sein. ');
		$this->setConfValue('ucFeedback', 'error', 'senderror', 'Wir konnten leider Dein Feedback aufgrund eines Systemfehlers nicht weiterleiten. ');
		//eMail-Konfiguration:
		$this->setConfValue('ucFeedback', 'email', 'sender', $this->getConfString('common', 'email', 'sender'));
		$this->setConfValue('ucFeedback', 'email', 'replyto', $this->getConfString('common', 'email', 'replyto'));
		$this->setConfValue('ucFeedback', 'email', 'receiver', $this->getConfString('common', 'email', 'receiver'));
		$this->setConfValue('ucFeedback', 'email', 'subject', 'WiSo@visor - Feedback: ');
		$this->setConfValue('ucFeedback', 'email', 'template', 'templates/ucFeedback/email.tpl');
	 }
	 
	/**
	 * configureUcForgotPwd() fï¿½llt die Konfiguration mit den benï¿½tigten Daten
	 */
	 private function configureUcForgotPwd()
	 {
		$this->setConfValue('ucForgotPwd', 'pwdform_tpl', null, 'templates/ucForgotPwd/pwdform.tpl'); //Template fï¿½r das Formular
		$this->setConfValue('ucForgotPwd', 'confirmation_tpl', null, 'templates/ucForgotPwd/confirmation.tpl'); //Template fï¿½r die Bestï¿½tigungsseite
		//Variable Teile in den Templates:
		$this->setConfValue('ucForgotPwd', 'emailaddy', null, 'EMAIL');
		$this->setConfValue('ucForgotPwd', 'showerror', null, 'SHOWERROR');
		$this->setConfValue('ucForgotPwd', 'submit', null, 'SUBMIT');
		$this->setConfValue('ucForgotPwd', 'username', null, 'USERNAME');
		$this->setConfValue('ucForgotPwd', 'userpwd', null, 'PASSWORD');
		//regulï¿½re Ausdrï¿½cke zur ï¿½berprï¿½fung der Eingaben: 
		//Hinweis: aufgrund der PHP-Stringbehandlung mï¿½ssen die Regexe in doppelte Anfï¿½hrungszeichen gesetzt werden! - deshalb auch immer der doppelte Backslash
		$this->setConfValue('ucForgotPwd', 'regex', 'email', "/^(([a-z0-9_-]+(\\.[a-z0-9_-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\\.)+([a-z]{2,4}|museum)))$/i"); //gï¿½ltige (i.S. von mï¿½gliche) eMailadresse oder leer
		//Fehlermeldung(en):
		$this->setConfValue('ucForgotPwd', 'error', 'email', 'Du hast keine gï¿½ltige eMailadresse angegeben. ');
		$this->setConfValue('ucForgotPwd', 'error', 'nosuchemail', 'Die angegebene eMailadresse existiert in unserer Datenbank nicht. ');
		$this->setConfValue('ucForgotPwd', 'error', 'emailerror', 'Es ist ein Systemfehler aufgetreten - leider konnten wir Dir Deine Zugangsdaten nicht senden. ');
		//eMail-Konfiguration:
		$this->setConfValue('ucForgotPwd', 'email', 'sender', $this->getConfString('common', 'email', 'sender'));
		$this->setConfValue('ucForgotPwd', 'email', 'replyto', $this->getConfString('common', 'email', 'replyto'));
		$this->setConfValue('ucForgotPwd', 'email', 'subject', 'WiSo@visor - Deine Nutzerdaten');
		$this->setConfValue('ucForgotPwd', 'email', 'template', 'templates/ucForgotPwd/email.tpl');
	 }
	 
	 private function configureUcSurveyResult()
	 {
		$this->setConfValue('ucSurveyResult', 'survey_result_tpl', null, 'templates/ucSurveyResult/surveyResult.tpl');
		$this->setConfValue('ucSurveyResult', 'char_header_tpl', null, 'templates/ucSurveyResult/charHeader.tpl'); 
		$this->setConfValue('ucSurveyResult', 'char_result_tpl', null, 'templates/ucSurveyResult/charResult.tpl'); 
		$this->setConfValue('ucSurveyResult', 'char_result_text_only_tpl', null, 'templates/ucSurveyResult/charResultTextOnly.tpl'); 
	 }
	 

	/**
	 * configureUcSurvey() fï¿½llt die Konfiguration mit den Daten fï¿½r ucSurvey
	 */
	 private function configureUcSurvey()
	 {
	 	//Templateersetzungen:
	 	$this->setConfValue('ucSurvey', 'startpage_tpl', null, 'templates/ucSurvey/startpage.tpl');
	 	$this->setConfValue('ucSurvey', 'navigation_tpl', null, 'templates/ucSurvey/navigationPanel.tpl');
	 	$this->setConfValue('ucSurvey', 'complete_tpl', null, 'templates/ucSurvey/completeSurvey.tpl');
	 	$this->setConfValue('ucSurvey', 'clear_tpl', null, 'templates/ucSurvey/clearSurvey.tpl');

	 	$this->setConfValue('ucSurvey', 'surveytitle', null, 'surveytitle'); //Titel der Umfrage
	 	$this->setConfValue('ucSurvey', 'startpage', null, 'startpage'); //Den Text fï¿½r die Startseite
	 	$this->setConfValue('ucSurvey', 'startimage', null, 'startimage'); //Das Bild fï¿½r die Startseite
	 	$this->setConfValue('ucSurvey', 'navigation', null, 'navigationpanel'); //Das Navigationspanel
	 	$this->setConfValue('ucSurvey', 'whysurvey', null, 'whysurvey'); //Button zum ï¿½ffnen einer Hilfeseite
	 	$this->setConfValue('ucSurvey', 'clearsurvey', null, 'clearsurvey'); //Button zum Verwerfen der Umfrage
	 	$this->setConfValue('ucSurvey', 'navbuttons', null, 'navigation'); //Buttons zum Navigieren innerhalb der Umfrage
	 	$this->setConfValue('ucSurvey', 'percentage', null, 'percentage'); //Fortschrittsanzeige
	 	$this->setConfValue('ucSurvey', 'question', null, 'question'); //Fragetext
	 	$this->setConfValue('ucSurvey', 'questionhelp', null, 'questionhelp'); //Hilfetext zur Frage
	 	$this->setConfValue('ucSurvey', 'answers', null, 'answers'); //Antwortmï¿½glichkeiten
	 	$this->setConfValue('ucSurvey', 'answerjavascript', null, 'answerjavascript'); //JavaScript zum Antwortfokussieren
	 	$this->setConfValue('ucSurvey', 'quotation', null, 'quotation'); //Zitatfeld
	 	$this->setConfValue('ucSurvey', 'answererror', null, 'answererror'); //Buttons zum Navigieren innerhalb der Umfrage
	 	
		//Buttonbeschriftungen: hier sind jeweils die Buttonbeschriftungen definiert
	 	$this->setConfValue('ucSurvey', 'button', 'startsurvey', 'Test starten');
	 	$this->setConfValue('ucSurvey', 'button', 'savesurvey', 'Test abschlieï¿½en');
	 	$this->setConfValue('ucSurvey', 'button', 'clearsurvey', 'Alle Antworten verwerfen');
	 	$this->setConfValue('ucSurvey', 'button', 'whysurvey', 'Hilfe');
	 	$this->setConfValue('ucSurvey', 'button', 'feedback', 'Feedback');
	 	$this->setConfValue('ucSurvey', 'button', 'next', 'Weiter >>');
	 	$this->setConfValue('ucSurvey', 'button', 'back', '<< Zurï¿½ck');
	 	
	 	$this->setConfValue('ucSurvey', 'replace', 'multiplechoice', '<br/><br/><span class="questionhelp">(Mehrfachantworten mï¿½glich)</span>');
		$this->setConfValue('ucSurvey', 'message', 'required', 'Bitte beantworte diese Frage. Vorher kannst Du nicht im Test fortfahren.');
		
		//wohin soll nach Abschluss der Umfrage weitergeleitet werden, wohin bei Abbruch? (jeweils ein UseCase-Handle)
	 	$this->setConfValue('ucSurvey', 'result_target_uc', null, 'survey_result');
	 	$this->setConfValue('ucSurvey', 'clear_target_uc', null, 'start');
	 	//wo sind die Grafiken?
	 	$this->setConfValue('ucSurvey', 'graphics', 'usecase', 'graphics');
	 	$this->setConfValue('ucSurvey', 'graphics', 'step', 'percentagebar');
	 }
	 
	 private function configureUcOverview()
	 {
	 	//Templateersetzungen:
	 	$this->setConfValue('ucOverview', 'overview_tpl', null, 'templates/ucOverview/overview.tpl');
	 	$this->setConfValue('ucOverview', 'block_bars_tpl', null, 'templates/ucOverview/block_bars.tpl');
	 	$this->setConfValue('ucOverview', 'line_bars_tpl', null, 'templates/ucOverview/line_bars.tpl');
	 	$this->setConfValue('ucOverview', 'block_icons_tpl', null, 'templates/ucOverview/block_icons.tpl');
	 	$this->setConfValue('ucOverview', 'block_icons_column_tpl', null, 'templates/ucOverview/block_icons_columns.tpl');
	 	$this->setConfValue('ucOverview', 'line_icons_tpl', null, 'templates/ucOverview/line_icons.tpl');
	 	$this->setConfValue('ucOverview', 'line_icons_column_tpl', null, 'templates/ucOverview/line_icons_columns.tpl');
	 	
	 	$this->setConfValue('ucOverview', 'text', 'bar_title', 'Du bist unter den besten ###:###result###:### Prozent der Kandidaten.');
	 	$this->setConfValue('ucOverview', 'text', 'bar_title_best', 'Glï¿½ckwunsch! Es gibt keinen Kandidaten, der besser ist als Du.');
	 	
	 	$this->setConfValue('ucOverview', 'button', 'email', '>> PDF anfordern');
	 }
	 
	 private function configureImageCreator()
	 {
	 	$this->setConfValue('ImageCreator', 'color', 'lightblue', '158,190,223');
	 	$this->setConfValue('ImageCreator', 'color', 'darkblue', '51,102,153');
	 	$this->setConfValue('ImageCreator', 'color', 'middleblue', '128,128,255');
	 	$this->setConfValue('ImageCreator', 'color', 'yellow', '252,195,0');
	 	$this->setConfValue('ImageCreator', 'color', 'white', '255,255,255');
	 	$this->setConfValue('ImageCreator', 'color', 'black', '0,0,0');

	 	$graphicsPath = 'grafik/';
	 	$this->setConfValue('ImageCreator', 'image', 'check', $graphicsPath.'haken.gif');
	 	$this->setConfValue('ImageCreator', 'image', 'check_th', $graphicsPath.'haken_th.gif');
	 	$this->setConfValue('ImageCreator', 'image', 'questionmark', $graphicsPath.'frage.gif');
	 	$this->setConfValue('ImageCreator', 'image', 'questionmark_th', $graphicsPath.'frage_th.gif');
	 	$this->setConfValue('ImageCreator', 'image', 'smiley_unknown', $graphicsPath.'grau2.png');
	 	$this->setConfValue('ImageCreator', 'image', 'smiley_unknown_th', $graphicsPath.'grau2_th.png');
	 	$this->setConfValue('ImageCreator', 'image', 'smiley_poor', $graphicsPath.'single_bad.png');
	 	$this->setConfValue('ImageCreator', 'image', 'smiley_poor_th', $graphicsPath.'single_bad_th.png');
	 	$this->setConfValue('ImageCreator', 'image', 'smiley_average', $graphicsPath.'single_middle.png');
	 	$this->setConfValue('ImageCreator', 'image', 'smiley_average_th', $graphicsPath.'single_middle_th.png');
	 	$this->setConfValue('ImageCreator', 'image', 'smiley_good', $graphicsPath.'single_good.png');
	 	$this->setConfValue('ImageCreator', 'image', 'smiley_good_th', $graphicsPath.'single_good_th.png');
	 	$this->setConfValue('ImageCreator', 'image', 'balken_leer', $graphicsPath.'balken_grau.png');
	 }
	 
	 private function configureUcSurveyHelp()
	 {
	 	//Templateersetzungen:
	 	$this->setConfValue('ucSurveyHelp', 'template', null, 'templates/ucSurveyHelp/whyPage.tpl');
	 }

	 private function configureUcStart()
	 {
	 	//Templateersetzungen:
	 	$this->setConfValue('ucStart', 'start_tpl', null, 'templates/ucStart/start.tpl');
	 	$this->setConfValue('ucStart', 'uni', null, 'INFO_UNI');
	 	$this->setConfValue('ucStart', 'kompetenz', null, 'INFO_KOMPETENZ');
	 	$this->setConfValue('ucStart', 'interessen', null, 'INFO_INTERESSEN');
	 	$this->setConfValue('ucStart', 'ergebnis', null, 'INFO_ERGEBNIS');
	 	$this->setConfValue('ucStart', 'sponsors', null, 'SPONSORS');
	 	$this->setConfValue('ucStart', 'sponsor_tpl', null, 'templates/ucStart/sponsors.tpl');
	 	$this->setConfValue('ucStart', 'href', null, 'SPONSORHREF');
	 	$this->setConfValue('ucStart', 'logo', null, 'SPONSORLOGO');
	 	$this->setConfValue('ucStart', 'spname', null, 'SPONSORNAME');
		$this->setConfValue('ucStart', 'imagePath', null, 'grafik/sponsors/');
		
		//BlockIDs zum Verlinken:
		$this->setConfValue('ucStart', 'blid', 'uni', '1');
		$this->setConfValue('ucStart', 'blid', 'kompetenz', '2');
		$this->setConfValue('ucStart', 'blid', 'interessen', '3');
	 }
	 
	 private function configureUcInfo()
	 {
	 	//Templates und -Ersetzungen:
	 	$this->setConfValue('ucInfo', 'tpl_file', 'page', 'templates/ucInfo/single_page.tpl');
	 	
	 	$this->setConfValue('ucInfo', 'tpl_replace', 'blocktitle', 'BLOCKTITEL');
	 	$this->setConfValue('ucInfo', 'tpl_replace', 'title', 'TITEL');
	 	$this->setConfValue('ucInfo', 'tpl_replace', 'testbutton', 'TEST');
	 	$this->setConfValue('ucInfo', 'tpl_replace', 'infobutton', 'INFOSEITE');
	 	$this->setConfValue('ucInfo', 'tpl_replace', 'infotext', 'INFOTEXT');
	 	
	 	$this->setConfValue('ucInfo', 'button', 'test', 'Zum Test >>');
	 	$this->setConfValue('ucInfo', 'button', 'infos', 'Zu den Infos >>');
	 }
	 
	 private function configureUcLinkbox()
	 {
	 	//Templates:	
	 	$this->setConfValue('ucLinkbox', 'tpl_file', null, 'templates/ucLinkbox/linkbox.tpl');
	 }

	private function configureUcGetPdf()
	{
		$this->setConfValue('ucGetPdf', 'filename', 'location', 'temp_pdf/');
		$this->setConfValue('ucGetPdf', 'filename', 'prefix', 'WiSoAdvisor-Auswertung_');
		$this->setConfValue('ucGetPdf', 'filename', 'postfix', '.pdf');
		
		//Templates:
		$this->setConfValue('ucGetPdf', 'template', 'tpl', 'templates/ucGetPdf/confirmation.tpl');
		$this->setConfValue('ucGetPdf', 'template', 'email', 'EMAIL');
		$this->setConfValue('ucGetPdf', 'template', 'backlink', 'BACKLINK');
		
		//eMail-Konfiguration:
		$this->setConfValue('ucGetPdf', 'email', 'sender', $this->getConfString('common', 'email', 'sender'));
		$this->setConfValue('ucGetPdf', 'email', 'replyto', $this->getConfString('common', 'email', 'replyto'));
		$this->setConfValue('ucGetPdf', 'email', 'subject', 'WiSo@visor - Deine persï¿½nlichen Testergebnisse');
		$this->setConfValue('ucGetPdf', 'email', 'template', 'templates/ucGetPdf/email.tpl');
		$this->setConfValue('ucGetPdf', 'email', 'username', 'username');
		
	}

	private function configureUcPanoramaViewer()
	{
		$this->setConfValue('ucPanoramaViewer', 'htmltemplate', null, 'templates/ucPanoramaViewer/panorama.tpl');
		$this->setConfValue('ucPanoramaViewer', 'panoramapath', null, 'grafik/360degree/');
		$this->setConfValue('ucPanoramaViewer', 'template', 'filename', 'PANORAMAFILE');
		$this->setConfValue('ucPanoramaViewer', 'template', 'path', 'PANORAMAPATH');
	}

}

?>