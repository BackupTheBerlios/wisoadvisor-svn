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
  
  // TODO: Transfer to Parameter Class
	if(isset($_FILES['thefile']['tmp_name'])) {
	  $lines=file($_FILES['thefile']['tmp_name']);
	  while (list($key,$val)=each($lines)) {
	    
	    //$ret .= $lines[$key].'<br>';
	    $lines[$key] = explode(';',$val);
	    $i=0;
	    
	    $vl = $lines[$key][$i++];
	    $tp = $lines[$key][$i++];
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