<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakult�t
 * (c) 2006 Lehrstuhl f�r Wirtschaftsinformatik 3, Uni Erlangen-N�rnberg
 * R�ckfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: exceptions.php
 * $Revision: 1.3 $
 * Erstellt am: 11.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/

class ModelException extends Exception {
}

class MissingParameterException extends Exception {
}

class HtmlFormGeneratorException extends Exception {
}

class UseCaseException extends Exception {
}

class DatabaseException extends Exception {
}

?>
