<?php
    // TigerWiki 2 (Version 2.21 - 2007-07-27)
    // Copyleft (C) Chabel.org 2005-2007, licence GNU/GPL (disponible sur www.gnu.org)
    // http://chabel.org/
    $WIKI_VERSION = "TigerWiki 2.21";
   
//Fichier de configuration
    include("_config.php");
// Trouver la page a afficher
    if (! $PAGE_TITLE = stripslashes(utf8_encode($_GET["page"]))) {
        if ($_GET["action"] == "search")
			if ($_GET["query"] != "")
				$PAGE_TITLE = "$SEARCH_RESULTS " . stripslashes($_GET[query]);
			else
				$PAGE_TITLE = $LIST . " (" . count(glob("$PAGES_DIR/*.*")) . ")";
        elseif ($_GET["action"] == "recent")
            $PAGE_TITLE = "$RECENT_CHANGES";
        else
            $PAGE_TITLE = "$START_PAGE";
    }
    $action = $_GET["action"];
    if (isset($_GET["time"]))
        $gtime = $_GET["time"];
    $datetw = date("Y/m/d H:i", mktime(date("H") + $LOCAL_HOUR));
// Arreter les acces malicieux via repertoire et accents
    if (preg_match("/\//", $PAGE_TITLE))
		$PAGE_TITLE = $START_PAGE;
    if (preg_match("/\//", $gtime))
		$gtime = '';
// Ecrire les modifications, s'il y a lieu
   	if ($_POST["content"] != "") {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {	
			if ($_POST["sc"] == $PASSWORD || $_COOKIE['AutorisationTigerWiki'] == md5($PASSWORD)) {
			    setcookie('AutorisationTigerWiki', md5($PASSWORD), time() + 365*24*3600);
				if (! $file = @fopen($PAGES_DIR . stripslashes($_POST["page"]) . ".txt", "w"))
					die("Could not write page!");
				if (get_magic_quotes_gpc())
					fputs($file, trim(stripslashes(utf8_encode($_POST["content"]))));
				else
					fputs($file, trim(utf8_encode($_POST["content"])));	    
				fclose($file);        
				if ($BACKUP_DIR <> '') {
					$complete_dir_s = $BACKUP_DIR . $_POST["page"] . "/";
                    if (! $dir = @opendir($complete_dir_s)) {
						mkdir($complete_dir_s);
						chmod($complete_dir_s,0777);
				    }
                    if (! $file = @fopen($complete_dir_s . date("Ymd-Hi", mktime(date("H") + $LOCAL_HOUR)) . ".bak", "a"))
                        die("Could not write backup of page!");
                    fputs($file, "\n// " . $datetw . " / " . " " . $_SERVER['REMOTE_ADDR'] . "\n");
                    if (get_magic_quotes_gpc())
                         fputs($file, trim(stripslashes($_POST["content"])));
                    else
                         fputs($file, trim($_POST["content"]) . "\n\n");            
                     fclose($file);
				}
				header("location: ./?page=" . urlencode(stripslashes($_POST[page])));
			}
			else { header("location: ./?page=" . $_POST[page]."&action=edit&error=1"); }
	}
	}
	elseif (isset($_POST["content"]) && $_POST["content"] == "") {
	    if ($_POST["sc"] == $PASSWORD || $_COOKIE['AutorisationTigerWiki'] == md5($PASSWORD)) {
	        setcookie('AutorisationTigerWiki', md5($PASSWORD), time() + 365*24*3600);
	        unlink($PAGES_DIR . stripslashes($_POST["page"]) . ".txt");
	    }
	    else
	        header("location: ./?page=".$_POST["page"]."&action=edit&error=1");
    }
// Lecture et analyse du modèle de page
    if (! $file = @fopen($WIKI_TEMPLATE, "r"))
        die("Wiki template is missing!");
    $template = fread($file, filesize($WIKI_TEMPLATE));
    fclose($file);
// Lecture du contenu et de la date de modification de la page
    if (($file = @fopen($PAGES_DIR . utf8_decode($PAGE_TITLE) . ".txt", "r")) || $action <> "") {
        if (file_exists($PAGES_DIR . utf8_decode($PAGE_TITLE) . ".txt"))
            $TIME = date("Y/m/d H:i", @filemtime($PAGES_DIR . utf8_decode($PAGE_TITLE) . ".txt") + $LOCAL_HOUR * 3600);
    $CONTENT = "\n" . @fread($file, @filesize($PAGES_DIR . utf8_decode($PAGE_TITLE) . ".txt")) . "\n";
        // Restaurer une page
        if (isset($_GET["page"]) && isset($gtime) && $_GET["restore"] == 1)
            if ($file = @fopen($BACKUP_DIR . $PAGE_TITLE . "/" . $gtime, "r"))
                $CONTENT = "\n" . @fread($file, @filesize($BACKUP_DIR . $PAGE_TITLE . "/" . $gtime)) . "\n";
        @fclose($file);
		$CONTENT = preg_replace("/\\$/Umsi", "&#036;", $CONTENT);
		$CONTENT = preg_replace("/\\\/Umsi", "&#092;", $CONTENT);
    }
    else {
        if (!file_exists($PAGES_DIR . $PAGE_TITLE . ".txt"))
            $CONTENT = "\n" . $DEFAULT_CONTENT;
        else
            $action = "edit";
    }
// Déterminer le mode d'accès
    if ($action == "edit" || $action == "search" || $action == "recent")
        $html = preg_replace('/{EDIT}/', $EDIT_BUTTON, $template);
    elseif (is_writable($PAGES_DIR . $PAGE_TITLE . ".txt") || !file_exists($PAGES_DIR . $PAGE_TITLE . ".txt"))
        $html = preg_replace('/{EDIT}/', "<a class=\"menulink\" href=\"./?page=".$PAGE_TITLE."&amp;action=edit\" accesskey=\"5\" rel=\"nofollow\">$EDIT_BUTTON</a>", $template);
    else
        $html = preg_replace('/{EDIT}/', $PROTECTED_BUTTON, $template);
    if ($action == "recent")
        $html = preg_replace('/{RECENT_CHANGES}/', $RECENT_CHANGES, $html);
    else
        $html = preg_replace('/{RECENT_CHANGES}/', "<a href=\"./?action=recent\" accesskey=\"3\">$RECENT_CHANGES</a>", $html);
// Remplacer les variables par des valeurs (dans le style de page)
    $html = preg_replace('/{PAGE_TITLE_BRUT}/', $PAGE_TITLE, $html);
    if ($action != "" && $action != "recent" && $action != "search")
	   $html = preg_replace('/{PAGE_TITLE}/', "<a class=\"menulink\" href=\"./?page=".$PAGE_TITLE."\">".$PAGE_TITLE."</a>", $html);
	else
	    $html = preg_replace('/{PAGE_TITLE}/', "<a class=\"menulink\" href=\"./?page=".$PAGE_TITLE."\">".$PAGE_TITLE."</a>", $html);
    if ($PAGE_TITLE == $START_PAGE && $action <> "search")
        $html = preg_replace('/{HOME}/', "<a class=\"menulink\" href=\"./?page=".$START_PAGE."\" accesskey=\"1\">$HOME_BUTTON</a>", $html);
    else
        $html = preg_replace('/{HOME}/', "<a class=\"menulink\" href=\"./?page=".$START_PAGE."\" accesskey=\"1\">$HOME_BUTTON</a>", $html);
    $html = preg_replace('/{WIKI_TITLE}/', $WIKI_TITLE, $html);
    $html = preg_replace('/{LAST_CHANGE}/', $LAST_CHANGES." :", $html);
    if ($action != "edit")
	    $html = preg_replace('/{HELP}/', "", $html);    
    else
	    $html = preg_replace('/{HELP}/', "(<a href=\"./?page=$HELP_BUTTON\" accesskey=\"2\" rel=\"nofollow\">$HELP_BUTTON</a>)", $html);
    $html = preg_replace('/{SEARCH}/', "<form method=\"get\" action=\"./?page=".$_GET[page]."\"><div><input type=\"hidden\" name=\"action\" value=\"search\" /><input type=\"text\" name=\"query\" value=\"$_GET[query]\" tabindex=1 /> <input type=\"submit\" value=\"$SEARCH_BUTTON\" accesskey=\"q\" /></div></form>", $html);
    if ($action == "edit") {
        $html = preg_replace('/{HISTORY}/', "/ <a href=\"?page=".$PAGE_TITLE."&amp;action=history\" accesskey=\"6\" rel=\"nofollow\">".$HISTORY_BUTTON."</a><br />", $html);
        $CONTENT = "<form method=\"post\" action=\"./\"><textarea name=\"content\" cols=\"80\" rows=\"40\" style=\"width: 100%;\">$CONTENT</textarea><input type=\"hidden\" name=\"page\" value=\"".$PAGE_TITLE."\" /><br /><p align=\"right\">";
        if ($PASSWORD != "" && $_COOKIE['AutorisationTigerWiki'] != md5($PASSWORD))
            $CONTENT .= $MDP." : <input type=\"password\" name=\"sc\" />";
        $CONTENT .= " <input type=\"submit\" value=\"$DONE_BUTTON\" accesskey=\"s\" /></p></form>";
        //Retrait d'un </div> avant le </form>
	}
	elseif ($action != "history")
    	$html = preg_replace('/{HISTORY}/', "", $html);
// Liste des versions historiques d'une page
    if ($action == "history" && !isset($gtime)) {
    	$html = preg_replace('/{HISTORY}/', "/ ".$HISTORY_BUTTON, $html);
		$complete_dir = $BACKUP_DIR . $_GET["page"] . "/";
    	if ($opening_dir = @opendir($complete_dir)) {
        	while (false !== ($filename = @readdir($opening_dir)))
        		$files[] = $filename;
        	sort ($files);
           	for($cptfiles = 2; $files[$cptfiles] != ''; $cptfiles++)
        	    $affichage = $affichage."<a href=\"?page=".$_GET["page"]."&amp;action=history&amp;time=".$files[$cptfiles]."\" rel=\"nofollow\">".$files[$cptfiles]."</a><br />";
        	$html = preg_replace('/{CONTENT}/', $affichage, $html);
        }
        else
        	$html = preg_replace('/{CONTENT}/', $NO_HISTORY, $html);
    }
// Affichage d'un fichier historique
	if ($action == "history" && isset($gtime)) {
	    $complete_dir = $BACKUP_DIR . $PAGE_TITLE . "/";
	    if ($file = @fopen($BACKUP_DIR . $PAGE_TITLE . "/" . $gtime, "r")) {
    	    $html = preg_replace('/{HISTORY}/', "/ <a href=\"?page=".$PAGE_TITLE."&amp;action=history\" rel=\"nofollow\">".$HISTORY_BUTTON."</a> (<a href=\"?page=".$PAGE_TITLE."&amp;action=edit&amp;time=".$gtime."&amp;restore=1\" rel=\"nofollow\">".$RESTORE."</a>)", $html);
            $CONTENT = @fread($file, @filesize($complete_dir . $gtime)) . "\n";
      	}
      	else
    	    $html = preg_replace('/{HISTORY}/', "/ <a href=\"?page=".$PAGE_TITLE."&amp;action=history\" rel=\"nofollow\">".$HISTORY_BUTTON."</a> (-)", $html);
    }
// Erreur du mot de passe
	if ($_GET['error'] == 1)
		$html = preg_replace('/{ERROR}/', $ERROR, $html);
	else
		$html = preg_replace('/{ERROR}/', "", $html);
// Effacement du cookie
    if ($_GET['erasecookie'] == 1)
        setcookie('AutorisationTigerWiki');
// Page de recherche
    if ($action == "search") {
        $dir = opendir(getcwd() . "/$PAGES_DIR");
        while ($file = readdir($dir)) {
            if (preg_match("/.txt/", $file)) {
                $handle = fopen($PAGES_DIR . $file, "r");
                @$content = fread($handle, filesize($PAGES_DIR . $file));
                fclose($handle);
                if (preg_match("/$_GET[query]/i", $content) || preg_match("/$_GET[query]/i", "$PAGES_DIR/$file")) {
                    $file = substr($file, 0, strlen($file) - 4);
                    $CONTENT .= "<a href=\"./?page=".utf8_encode($file)."\">".utf8_encode($file)."</a><br />";
                }
            }
        }
    }
// Changements récents
    elseif ($action == "recent") {
        $dir = opendir(getcwd() . "/$PAGES_DIR");
        while ($file = readdir($dir))
            if (preg_match("/.txt/", $file))
                $filetime[$file] = filemtime($PAGES_DIR . $file);
        arsort($filetime);
        $filetime = array_slice($filetime, 0, 10);
        foreach ($filetime as $filename => $timestamp) {
            $filename = substr($filename, 0, strlen($filename) - 4);
            $CONTENT .= "<a href=\"./?page=".utf8_encode($filename)."\">".utf8_encode($filename)."</a> (" . strftime("$TIME_FORMAT", $timestamp + $LOCAL_HOUR * 3600) . ")<br />";
        }
    }
// Formatage de page
    elseif ($action <> "edit") {

        if (preg_match("/%html%\s/", $CONTENT))     

            $CONTENT = preg_replace("/%html%\s/", "", $CONTENT);
        else {
            $CONTENT = htmlentities($CONTENT);
    		    $CONTENT = preg_replace("/&amp;#036;/Umsi", "&#036;", $CONTENT);
    		    $CONTENT = preg_replace("/&amp;#092;/Umsi", "&#092;", $CONTENT);
    		    $CONTENT = preg_replace("/\^(.)/Umsie", "'&#'.ord('\\1').';'", $CONTENT);
    		    $CONTENT = preg_replace('#\[(.+)\|([0-9a-zA-Z\.\'\s\#/~\-_%=\?\&amp;,\+]*)\]#U', '<a href="$2" class="url">$1</a>', $CONTENT);
    		    $CONTENT = preg_replace('#\[(.+)\|h(ttps?://[0-9a-zA-Z\.\#/~\-_%=\?\&amp;,\+]*)\]#U', '<a href="xx$2" class="url">$1</a>', $CONTENT);
    		    $CONTENT = preg_replace('#\[h(ttps?://[0-9a-zA-Z\.\&amp;\#\:/~\-_%=?]*\.(jpeg|jpg|gif|png))\]#i', '<img src="xx$1" />', $CONTENT);
    		    $CONTENT = preg_replace('#\[([0-9a-zA-Z\.\&amp;\#\:/~\-_%=?]*\.(jpeg|jpg|gif|png))\]#i', '<img src="$1" />', $CONTENT);
    		    $CONTENT = preg_replace('#(https?://[0-9a-zA-Z\.\&amp;\#\:/~\-_%=?]*)#i', '<a href="$0" class="url">$1</a>', $CONTENT);
    		    $CONTENT = preg_replace('#xxttp#', 'http', $CONTENT);
    		    
    		    preg_match_all("/\[([^\/]+)\]/U", $CONTENT, $matches, PREG_PATTERN_ORDER);
    		    foreach ($matches[1] as $match)
    			    if (file_exists(html_entity_decode($PAGES_DIR."$match.txt")))
    				    $CONTENT = str_replace("[$match]", "<a href=\"./?page=".$match."\">$match</a>", $CONTENT);
    			    else
    				    $CONTENT = str_replace("[$match]", "<a class=\"pending\" href=\"./?page=".$match."\">$match</a>", $CONTENT);

    		    $CONTENT = preg_replace('#\[INCLUDE:([0-9a-zA-Z\.\&amp;\#\:/~\-_%=?]*\.map)\]#ei', "getFileContents('\\1')", $CONTENT);

    		    $CONTENT = preg_replace('#(\[\?(.+)\]*)#i', '<a href="http://de.wikipedia.org/wiki/$0" class="url">$0</a>', $CONTENT);
		        $CONTENT = preg_replace('#([0-9a-zA-Z\./~\-_]+@[0-9a-z\./~\-_]+)#i', '<a href="mailto:$0">$0</a>', $CONTENT);
		        $CONTENT = preg_replace('/^\*\*\*(.*)(\n)/Um', "<ul><ul><ul><li>$1</li></ul></ul></ul>$2", $CONTENT);
		        $CONTENT = preg_replace('/^\*\*(.*)(\n)/Um', "<ul><ul><li>$1</li></ul></ul>$2", $CONTENT);
		        $CONTENT = preg_replace('/^\*(.*)(\n)/Um', "<ul><li>$1</li></ul>$2", $CONTENT);
		        $CONTENT = preg_replace('/^\#\#\#(.*)(\n)/Um', "<ol><ol><ol><li>$1</li></ol></ol></ol>$2", $CONTENT);
		        $CONTENT = preg_replace('/^\#\#(.*)(\n)/Um', "<ol><ol><li>$1</li></ol></ol>$2", $CONTENT);
		        $CONTENT = preg_replace('/^\#(.*)(\n)/Um', "<ol><li>$1</li></ol>$2", $CONTENT);
		
		        $CONTENT = preg_replace('/(<\/ol>\n*<ol>|<\/ul>\n*<ul>)/', "", $CONTENT); 
		        $CONTENT = preg_replace('/(<\/ol>\n*<ol>|<\/ul>\n*<ul>)/', "", $CONTENT); 
		        $CONTENT = preg_replace('/(<\/ol>\n*<ol>|<\/ul>\n*<ul>)/', "", $CONTENT); 
		
		
		        $CONTENT = preg_replace('/^!!!(.*)(\n)/Um', '<h1>$1</h1>$2', $CONTENT);
		        $CONTENT = preg_replace('/^!!(.*)(\n)/Um', '<h2>$1</h2>$2', $CONTENT);
		        $CONTENT = preg_replace('/^!(.*)(\n)/Um', '<h3>$1</h3>$2', $CONTENT);
		        
		        while (preg_match('/^  /Um', $CONTENT))
              $CONTENT = preg_replace('/^( +) ([^ ])/Um', '$1&nbsp;&nbsp;&nbsp;&nbsp;$2', $CONTENT);
              $CONTENT = preg_replace('/^ /Um', '&nbsp;&nbsp;&nbsp;&nbsp;', $CONTENT);
              $CONTENT = preg_replace('/----*(\r\n|\r|\n)/m', '<hr />', $CONTENT);
              $CONTENT = preg_replace('/\n/', '<br />', $CONTENT);
              $CONTENT = preg_replace('#</ul>(<br />)*#', "</ul>", $CONTENT);
              $CONTENT = preg_replace('#</ol>(<br />)*#', "</ol>", $CONTENT);

              $CONTENT = preg_replace('#</li><ul><li>*#', "<ul><li>", $CONTENT);
              $CONTENT = preg_replace('#</ul></ul>*#', "</ul></li></ul>", $CONTENT);
              $CONTENT = preg_replace('#</ul></ul>*#', "</ul></li></ul>", $CONTENT);
              $CONTENT = preg_replace('#</li></ul><li>*#', "</li></ul></li><li>", $CONTENT);

              $CONTENT = preg_replace('#</li><ol><li>*#', "<ol><li>", $CONTENT);
              $CONTENT = preg_replace('#</ol></ol>*#', "</ol></li></ol>", $CONTENT);
              $CONTENT = preg_replace('#</ol></ol>*#', "</ol></li></ol>", $CONTENT);
              $CONTENT = preg_replace('#</li></ol><li>*#', "</li></ol></li><li>", $CONTENT);

              $CONTENT = preg_replace('#(</h[123]>)<br />#', "$1", $CONTENT);
              //$CONTENT = preg_replace("/{(.+)}/Ue", "'<pre><code>' . preg_replace('#<br />#', '', '\\1') . '</code></pre>'", $CONTENT);
              $CONTENT = preg_replace("/{(.+)}/Ue", "'' . preg_replace('#<br />#', '', '\\1') . ''", $CONTENT);
              $CONTENT = preg_replace("/'--(.*)--'/Um", '<del>$1</del>', $CONTENT);
              $CONTENT = preg_replace("/'''''(.*)'''''/Um", '<strong><em>$1</em></strong>', $CONTENT);
              $CONTENT = preg_replace("/'''(.*)'''/Um", '<strong>$1</strong>', $CONTENT);
              $CONTENT = preg_replace("/''(.*)''/Um", '<em>$1</em>', $CONTENT); 
              $CONTENT = substr($CONTENT, 6, strlen($CONTENT) - 6);
              $CONTENT = html_entity_decode($CONTENT);
        }
    }
    if ($action != "" && $action != "edit" || (!file_exists($PAGES_DIR . $PAGE_TITLE . ".txt")))
        $TIME = "-";
    $html = preg_replace("/{CONTENT}/", $CONTENT, $html);
    $html = preg_replace("/{LANG}/", $LANG, $html);
    $html = preg_replace("/{WIKI_VERSION}/", $WIKI_VERSION, $html);
    $html = preg_replace("/{CHARSET}/", $CHARSET, $html);
    $html = preg_replace('/{TIME}/', $TIME, $html);
    $html = preg_replace('/{DATE}/', $datetw, $html);
    $html = preg_replace('/{IP}/', $_SERVER['REMOTE_ADDR'], $html);
    if ($_COOKIE['AutorisationTigerWiki'] != "")
        $html = preg_replace('/{COOKIE}/', '-- <a href="./?page='.$PAGE_TITLE.'&erasecookie=1" rel=\"nofollow\">'.$ERASE_COOKIE.'</a>', $html);
    else
        $html = preg_replace('/{COOKIE}/', '', $html);
// Affichage de la page
    echo utf8_decode($html);
    
 function getFileContents ($iFileName) {
    if ($file = fopen($iFileName, "r")) {
      $ret = fread($file, filesize($iFileName));
      fclose($file);
    }
    
    return $ret;   
 }
?>