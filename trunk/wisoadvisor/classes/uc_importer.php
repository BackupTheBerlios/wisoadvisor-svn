<?php
class ucImporter extends UseCase {

//Ausführung: Business-Logik
public function execute()	{

	$user = User::getForId($this, $this->getSess()->getUid());
	
	if (!$this->getSess()->isAuthenticated() && $user->getType != 'admin') {
		header('location:'.$this->getMainLink());
		$this->setOutputType(USECASE_NOTYPE);
	
	} else {

	  $htmlToAppend='';
	  $this->setOutputType(USECASE_HTML);
    switch ($this->getStep()) {
			
			case 'import':
			  $htmlToAppend = $this->performImport();
			  //break;
			  
			default:
        $this->appendOutput($this->getForm().$htmlToAppend);
    }
	}
	return true;
}

private function performImport() {
	
  $ret='';
  
	if(isset($_FILES['thefile']['tmp_name'])) {
	  $lines=file($_FILES['thefile']['tmp_name']);
	  while (list($key,$val)=each($lines)) {
	    
	    $lines[$key] = explode(';',$val);
	    
	    $i=0;	    
	    $lect = $lines[$key][$i++]; // erstes Element der CSV-Datei, vielleicht Vorlesung
	    $mark = $lines[$key][$i++]; // zweites Element der CSV-Datei, vielleicht Note
	    $mcnt = $lines[$key][$i++]; // drittes Element der CSV-Datei, vielleicht Häufigkeit der Note
	    
	    // hier die Routine zum Schreiben in die Datenbank einfügen

	  }  
	}
  return $ret;
}

private function getForm() {
  $generator = new HtmlGenerator( $this->getConf()->getConfString('ucImporter', 'htmltemplate'), 
	                                $this->getConf()->getConfString('template', 'indicator', 'pre'), 
	                                $this->getConf()->getConfString('template', 'indicator', 'after'));
	                                
	
  $str = $this->getConf()->getConfString('ucImporter', 'useimportedmarks') == 'false' ? '<b>nicht</b>' : '';
	$generator->apply($this->getConf()->getConfString('ucImporter', 'config_import'), $str);
	$generator->apply($this->getConf()->getConfString('ucImporter', 'linkstepimport'), $this->getOwnLink('import'));
	
  return $generator->getHTML();	
}

} // class
?>