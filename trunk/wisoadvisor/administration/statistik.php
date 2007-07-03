<?php
/***********************************************************************************
 * WiSo@visor - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de
 *
 * Datei: statistik.php
 * Erstellt am: 27.07.2006
 * Erstellt von: flo
 ***********************************************************************************/

//Bearbeitungszeit erhöhen:
ini_set('max_execution_time','600');


//Status dieser Datei: nur Test!!!
define('FILENAME', 'file.csv');

//benötigte Autoload-Funktion - diese zieht automatisch benötigte Klassen an!
//die Zuordnung von Klassennamen zu Dateinamen ist im Konfigurationsobjekt festgelegt
function __autoload($className) {
    global $conf; //nicht so sauber, geht aber wg. __autoload nicht anders
    require_once $conf->getConfString('class', $className);
}

//es wird immer eine Session verwendet, damit ggf. Objekte darauf abgelegt werden können, bzw. gelesen werden.
//generell gilt: wenn möglich, wird ein Objekt aus der Session gelesen statt neu angelegt
session_start();

//Konfigurations-Klasse wird anfangs manuell geladen - es wird das "spezialisierte" Objekt für WisoAdvisor benutzt;
// die Konfiguration liegt auf der Grundebene, nicht im Classpath!
require_once('configuration.php'); //spezialisiertes Konfigurations-Objekt

$conf = new WisoadvisorAdminConfiguration();

$param = new ParameterHandler();
$param->setAccessMethod(PARAMETERHANDLER_PREFER_POST);
$db = new SpecialisedDatabase();
//Datenbankverbindung:
if (!$db->connect()) die('Datenbankfehler');

/**
 * Funktionsdefinitionen:
 */

function createBrowserCsv($fileName, $columns, $resultArray)
{
		$file = fopen(FILENAME, 'w+');

		fputcsv($file, $columns, ';', '"');

		foreach ($resultArray as $rec)
		{
			//sicherheitshalber: alle ; durch , und alle " durch ' ersetzen:, HTML raus
			foreach ($rec as $key => $val)
			{
				$rec[$key] = str_replace(';', ',', $rec[$key]);
				$rec[$key] = str_replace('"', "'", $rec[$key]);
				$rec[$key] = strip_tags($rec[$key]);
			}

			$csvLine = Array();
			foreach ($columns as $col) $csvLine[] = $rec[$col]; //stellt sicher, dass die Spalten in der richtigen Reihenfolge sortiert werden

			fputcsv($file, $csvLine, ';', '"');
		}

		fclose($file);
		header('Content-Type:txt/csv');
		header('Content-disposition: inline; filename="'.urldecode($fileName).'.csv"');
		echo file_get_contents(FILENAME);
		unlink(FILENAME);

		exit();
}


/**
 * MAIN
 */

$file = $param->getParameter('file');
$sid = $param->getParameter('sid');

//Unterscheidung: Nutzerstatistik oder Testauswertung?, ...
switch ($param->getParameter('do'))
{
	case 'user':
		$result = $db->query('SELECT uid, username, email, gender, birthday FROM advisor__user ORDER BY uid');
		$columns = Array('uid', 'username', 'email', 'gender', 'birthday');
		$resArr = Array();
		while ($rec = $db->fetch_array($result)) $resArr[] = $rec;
		createBrowserCsv($file, $columns, $resArr);
		break;

	case 'questions':
		$result = $db->query('SELECT quid, required as pflicht, questiontype as typ, question as frage FROM advisor__questions q, advisor__questiontypes t WHERE q.qtid = t.qtid ORDER BY quid');
		$columns = Array('quid', 'pflicht', 'typ', 'frage');
		$resArr = Array();
		while ($rec = $db->fetch_array($result)) $resArr[] = $rec;
		createBrowserCsv($file, $columns, $resArr);
		break;

	case 'answers':
		$result = $db->query('SELECT anid, answer as antwort FROM advisor__answers ORDER BY anid');
		$columns = Array('anid', 'antwort');
		$resArr = Array();
		while ($rec = $db->fetch_array($result)) $resArr[] = $rec;
		createBrowserCsv($file, $columns, $resArr);
		break;

	case 'survey':
		//die Fragenauswertung ist etwas komplexer:
		//zuerst brauchen wir alle Fragen der gewünschten Umfrage:
		$questions = $db->query('SELECT q.quid AS quid, questiontype AS qt FROM advisor__questions q, advisor__questionblocks b, advisor__questiontypes t WHERE q.qbid = b.qbid AND q.qtid = t.qtid AND sid = '.$sid.' ORDER BY b.position, q.position');

		$exportCols = Array();
		$exportCols[] = Array('typ'=>'uid', 'bez'=>'uid');
		while ($frage = $db->fetch_array($questions))
		{
			//wieviel Spalten wir für die Frage anlegen, hängt vom Typ ab:
			switch ($frage['qt'])
			{
				//diese Fälle bedingen alle lediglich eine Spalte, teilweise nur unterschiedliche Behandlung:
				case 'singlechoice':
				case 'restrictedinput':
					$exportCols[] = Array('typ'=>'qu_sc', 'quid'=>$frage['quid'], 'bez'=>'frage'.$frage['quid']);
					//ausserdem: ein Rating-Feld
					$exportCols[] = Array('typ'=>'qu_sc_ra', 'quid'=>$frage['quid'], 'bez'=>'frage'.$frage['quid'].'_rating');
					break;

				case 'openinput':
				case 'opentext':
					$exportCols[] = Array('typ'=>'qu_ot', 'quid'=>$frage['quid'], 'bez'=>'frage'.$frage['quid']);
					break;

				case 'testedinput': //wenn keine Verknüpfung dann falsch?
					$exportCols[] = Array('typ'=>'qu_ti', 'quid'=>$frage['quid'], 'bez'=>'frage'.$frage['quid']);
					break;

				case 'singlechoiceother': //wie sc, nur dass bei "other" in open_results die quid mit Antwort steht
					$exportCols[] = Array('typ'=>'qu_so', 'quid'=>$frage['quid'], 'bez'=>'frage'.$frage['quid']);
					break;

				//MC-Fragen brauchen pro Antwortmöglichkeit eine Spalte:
				case 'multiplechoice':
					$answers = $db->query('SELECT anid FROM advisor__questions_answers WHERE quid = '.$frage['quid'].' ORDER BY position');
					while ($antwort = $db->fetch_array($answers)) $exportCols[] = Array('typ'=>'qu_mc', 'quid'=>$frage['quid'], 'anid'=>$antwort['anid'], 'bez'=>'frage'.$frage['quid'].'_antwort'.$antwort['anid']);
					//ausserdem: ein Rating-Feld
					$exportCols[] = Array('typ'=>'qu_mc_ra', 'quid'=>$frage['quid'], 'bez'=>'frage'.$frage['quid'].'_rating');
					break;
			}
		}
		//jetzt alle Merkmale der survey, für jedes Char eine Spalte:
		$chars = $db->query('SELECT DISTINCT c.chid as chid, characteristic FROM advisor__questions q, advisor__questionblocks b, advisor__characteristics c WHERE q.qbid = b.qbid AND q.chid = c.chid AND b.sid = '.$sid.' ORDER BY characteristic');
		while ($char = $db->fetch_array($chars)) $exportCols[] = Array('typ'=>'char', 'chid'=>$char['chid'], 'bez'=>'merkmal_'.str_replace(' ', '_', $char['characteristic']) );

		//zu guter letzt noch den Gesamtwert für den Test und das "Abschlussdatum":
		$exportCols[] = Array('typ'=>'result', 'bez'=>'testergebnis');
		$exportCols[] = Array('typ'=>'datum', 'bez'=>'Datum_Testabschluss');

		//ab jetzt enthält exportCols Informationen zu allen zu exportierenden Spalten...
		//das Array mit den Spaltennamen für die Tabelle können wir daraus schon generieren:
		$columns = Array();
		foreach ($exportCols as $col) $columns[] = $col['bez'];

		//jetzt beginnt der Export; aus Gründen der Übersichtlichkeit bauen wir kein großes DB-Statement, sondern skripten die Auswertung:
		$users = $db->query('SELECT uid FROM advisor__user ORDER BY uid');
		$resArr = Array(); //hier kommt das Ergebnis rein
		//...für alle User:
		while ($user = $db->fetch_array($users))
		{
			$uid = $user['uid'];
			$line = Array();

			//kurze Prüfung, ob der User die Umfrage schon abgeschlossen hat:
			$resAbschluss = $db->query('SELECT result, completed_date FROM advisor__users_surveys WHERE uid = '.$uid.' AND sid = '.$sid.' AND attempt = 1');
			$completed = false;
			$abschluss = null;
			if ($resAbschluss) { $abschluss = mysql_fetch_array($resAbschluss); if ($abschluss['completed_date']>0) $completed = true;}

			//für jeden User müssen wir alle benötigten Spalten befüllen: die Reihenfolge ist relevant und kommt durch das Array
			foreach ($exportCols as $col)
			{
				if ($completed) {
					//abhängig vom 'Typ':
					switch ($col['typ'])
					{
						case 'uid':
							//userid: einfach reinschreiben:
							$line[$col['bez']] = $uid;
							break;

						case 'result':
							//gesamtergebnis:
							$line[$col['bez']] = $abschluss['result'];
							break;

						case 'datum':
							//Testdatum:
							$line[$col['bez']] = date('Y-m-d H:i:s', $abschluss['completed_date']);
							break;

						case 'char':
							//Merkmal:
							$res = $db->fetchRecord('SELECT result FROM advisor__users_chars WHERE uid = '.$uid.' AND sid = '.$sid.' AND attempt = 1 AND chid = '.$col['chid']);
							$line[$col['bez']] = $res['result'];
							break;

						case 'qu_sc':
							//singlechoice-Frage:
							$answer = $db->query('SELECT anid FROM advisor__results WHERE uid = '.$uid.' AND quid = '.$col['quid'].' AND attempt = 1');
							$answerGiven = '';
							if ($answer)
							{
								$ans = mysql_fetch_array($answer);
								if ($ans['anid']<>'') $answerGiven = $ans['anid'];
							}

							$line[$col['bez']] = $answerGiven;
							break;

						case 'qu_ot':
							//offener Text:
							$answer = $db->query('SELECT openresult FROM advisor__open_results WHERE uid = '.$uid.' AND quid = '.$col['quid'].' AND attempt = 1');
							$answerGiven = '';
							if ($answer)
							{
								$ans = mysql_fetch_array($answer);
								if ($ans['openresult']<>'') $answerGiven = $ans['openresult'];
							}

							$line[$col['bez']] = $answerGiven;
							break;

						case 'qu_so':
							//sc-other: es gibt bei sc auch die "andere" option, dann steht die Antwort in openresults:
							$answer = $db->query('SELECT anid FROM advisor__results WHERE uid = '.$uid.' AND quid = '.$col['quid'].' AND attempt = 1');
							$answerGiven = '';
							if ($answer)
							{
								$ans = mysql_fetch_array($answer);
								if ($ans['anid']<>'') $answerGiven = $ans['anid'];
								else { $anOpen = $db->query('SELECT openresult FROM advisor__open_results WHERE uid = '.$uid.' AND quid = '.$col['quid'].' AND attempt = 1');
										$opRes = $db->fetch_array($anOpen);
										$answerGiven = 'Andere: '.$opRes['openresult'];
								}
							}

							$line[$col['bez']] = $answerGiven;
							break;

						case 'qu_ti':
							//TestedInput: wird gegen eine anid verglichen, wenn nicht vorhanden, dann falsch
							$answer = $db->query('SELECT uid FROM advisor__results WHERE uid = '.$uid.' AND quid = '.$col['quid'].' AND anid = '.$col['anid'].' AND attempt = 1');
							$answerGiven = 'falsche Antwort gegeben';
							if ($answer) { $ans = $db->fetch_array($answer); if ($ans['uid']<>'') $answerGiven = 'richtige Antwort gegeben'; }

							$line[$col['bez']] = $answerGiven;
							break;

						case 'qu_mc':
							//mc-Frage:
							//d.h.: es gibt für jede mögliche Antwort ein Feld:
							$answer = $db->query('SELECT uid FROM advisor__results WHERE uid = '.$uid.' AND quid = '.$col['quid'].' AND anid = '.$col['anid'].' AND attempt = 1');
							$answerGiven = 'false';
							if ($answer) { $ans = $db->fetch_array($answer); if ($ans['uid']<>'') $answerGiven = 'true'; }

							$line[$col['bez']] = $answerGiven;
							break;

						case 'qu_sc_ra':
							//Rating-Feld:
							$rating = $db->query('SELECT rating FROM advisor__results r, advisor__questions_answers a WHERE uid = '.$uid.' AND r.quid = '.$col['quid'].' AND attempt = 1 AND r.anid = a.anid AND r.quid = a.quid');
							$ratGiven = '';
							if ($rating)
							{
								$rating = mysql_fetch_array($rating);
								if ($rating['rating']<>'') $ratGiven = $rating['rating'];
							}

							$line[$col['bez']] = $ratGiven;
							break;

						case 'qu_mc_ra':
							//Rating-Feld:
							$rating = $db->query('SELECT rating FROM advisor__results r, advisor__questions_answers a WHERE uid = '.$uid.' AND r.quid = '.$col['quid'].' AND attempt = 1 AND r.anid = a.anid AND r.quid = a.quid');
							$ratGiven = '';
							if ($rating)
							{
								$ratNumber = 0;
								while ($ans = mysql_fetch_array($rating)) $ratNumber += $ans['rating'];

								$ratGiven = $ratNumber;
							}

							$line[$col['bez']] = $ratGiven;
							break;

						default:
							die('Fehler im Spalten-Array!!!!!');
							break;
					}
				}
				else
				{
					//ansonsten "leeres" Feld; Ausnahme: uid:
					if ($col['bez']=='uid') $line[$col['bez']] = $uid;
					else $line[$col['bez']] = '';
				}
			}

			$resArr[] = $line;
		}
		createBrowserCsv($file, $columns, $resArr);
		break;

	default:
		//default: mach nix...
		break;
}


//jetzt: Liste anzeigen:
$result = $db->query('SELECT sid, title FROM advisor__surveys ORDER BY position');

$menuitems = Array( Array('link'=>'do=user&file=Nutzerstatistik', 'item'=>'Nutzerstatistik exportieren') );
$menuitems[] = Array('link'=>'do=questions&file=Fragenzuordnung', 'item'=>'Zuordnung der Fragen-IDs (quid) exportieren');
$menuitems[] = Array('link'=>'do=answers&file=Antwortenzuordnung', 'item'=>'Zuordnung der Antwort-IDs (anid) exportieren');

while ($record = $db->fetch_array($result)) $menuitems[] = Array('link'=>'do=survey&sid='.$record['sid'].'&file=Testauswertung_'.urlencode($record['title']), 'item'=>'Testauswertung: '.$record['title']);

$db->disconnect();

?>
<h1>Statistiktool</h1>
<p>Bitte wähle deine Auswertung:<br/><br/>
<? foreach ($menuitems as $menu)
{
?>
<a href="<?=$_SERVER['PHP_SELF'].'?'.$menu['link']?>" target="_blank"><?=$menu['item']?></a><br/><br/>
<?
}
?>
</p>