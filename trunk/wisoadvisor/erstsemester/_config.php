<?php
// Configuration du mot de passe
	$PASSWORD			=	"rmpflpfrmpf";
// Paramètres divers
    $WIKI_TEMPLATE  = "../templates/wikitemplate.tpl";
    $LANG		        =	"en";
	$CHARSET	        =	"utf-8";
    $TIME_FORMAT		=	"%Y-%m-%d %R";
    $LOCAL_HOUR			=	"0";
	$PAGES_DIR			= 	"pages/";
	$BACKUP_DIR			=	""; //historique/
// Variables de configuration de langues
    $WIKI_TITLE			=	"Das WiSo-Wiki zum Studienanfang";
    $START_PAGE			=	"Studienanfang";
    $HOME_BUTTON		=	"Studienanfang";
    $HELP_BUTTON		=	"Hilfe";
    $DEFAULT_CONTENT	=	"Die Seite \"".stripslashes($_GET["page"])."\" existiert nicht."; //The center variable is the page requested
    $EDIT_BUTTON		=	"Admin";
    $DONE_BUTTON		=	"Speichern";
    $PROTECTED_BUTTON 	=	"Gesperrte Seite";
    $SEARCH_BUTTON		=	"Suchen";
    $SEARCH_RESULTS		=	"Suchergebnisse";
	$LIST				=	"Liste der Seiten";
    $RECENT_CHANGES		=	"Neueste &Auml;nderungen";
	$LAST_CHANGES		=	"Letzte &Auml;nderung";
	$HISTORY_BUTTON		=	"History";
	$NO_HISTORY			=	"Keine History gefunden.";
	$RESTORE            =   "Wiederherstellen";
	$MDP                =   "Passwort";
	$ERROR				=	$MDP." specified is incorrect.";
	$ERASE_COOKIE       =   "Cookie l&ouml;schen";
?>