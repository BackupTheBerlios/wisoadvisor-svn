<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 ***********************************************************************************/


class ucPanoramaViewer extends UseCase {
	
	//Ausführung: Business-Logik
	public function execute() {
		try {
			//alles ganz einfach: Es wird eine VOLLSTÄNDIGE HTML-Seite generiert, in der
			//für den Panoramaviewer einfach der übergebene Step-Parameter eingesetzt wird
			
			$panoramaFile = $this->getStep();
			if ($panoramaFile == null or $panoramaFile == $this->getConf()->getConfString('standardstep'))
				throw new MissingParameterException('Ein Parameter fehlt.');
				
			$panoramaPath = $this->getConf()->getConfString('ucPanoramaViewer', 'panoramapath');
			
			//zur Anzeige der Seite wird der HTML-Generator benutzt
			$generator = new HtmlGenerator( $this->getConf()->getConfString('ucPanoramaViewer', 'htmltemplate'), $this->getConf()->getConfString('template', 'indicator', 'pre'), $this->getConf()->getConfString('template', 'indicator', 'after'));
			//befülle den Generator mit den zu ersetzenden Anteilen...
			$generator->apply($this->getConf()->getConfString('ucPanoramaViewer', 'template', 'path'), $panoramaPath);
			$generator->apply($this->getConf()->getConfString('ucPanoramaViewer', 'template', 'filename'), $panoramaFile);
			//HTML in den Output schreiben...
			$this->appendOutput($generator->getHTML());
			
			$this->setOutputType(USECASE_OWN_HTML);
			return true;
		} catch (ModelException $e) {
 			$this->setError('Bei der Verarbeitung ist ein Fehler aufgetreten.<br>'.$e->getMessage());
 			return false;
 		}  catch (MissingParameterException $e) {
 			$this->setError($e->getMessage());
 			return false;
		}
	}
}
?>