<?php
/***********************************************************************************
 * WiSo@vice - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * Datei: uc_graphics.php
 * $Revision: 1.14 $
 * Erstellt am: 14.05.2006
 * Erstellt von: Michael Gottfried
 ***********************************************************************************/
 
 /**
  * Der Usecase Graphics dient zur Erzeugung von einfachen grafischen Darstellungen
  * und liefert sie direkt als Bild zurück
  * 
  * @author Michael Gottfried
  */
 class ucGraphics extends UseCase {
 	
 	// Mögliche Ausgaben
 	const STEP_RESULTBAR = 'resultbar';
 	const STEP_EMPTY_RESULTBAR = 'emptyresultbar';
 	const STEP_RANKINGBAR ='rankingbar';
 	const STEP_PERCENTAGEBAR = 'percentagebar';
 	const STEP_LEVELBAR = 'levelbar';
 	const STEP_SMILEY = 'smiley';
 	const STEP_CHECK = 'check';
 	const STEP_QUESTIONMARK = 'questionmark';
 
 	// Parameter für showResultBar
 	const PAR_MINIMUM = 'minimum';
 	const PAR_MAXIMUM = 'maximum';
 	const PAR_RESULT = 'result';
 	
 	// Parameter für showRankingBar
 	const PAR_RANKING = 'ranking';
 	
 	// Parameter für showPercentageBar
 	const PAR_PERCENTAGE = 'percentage';

	// Parameter für showLevelBar
 	const PAR_LEVEL = 'level';
 	
 	// Parameter für showSmiley
 	const PAR_SMILEY_ASPECT = 'aspect';
 	
 	// Parameter für showWay
 	const PAR_UID = 'uid';

 	// Allgemeine Parameter
 	const PAR_BACKGROUND = 'bg';
 	const BACKGROUND_WHITE = 'white';
 	const BACKGROUND_YELLOW = 'yellow';
 	
	public function execute()
	{
		try {
			switch ($this->getStep()) {
				case self::STEP_RESULTBAR:
					$this->showResultBar();
					break;
				case self::STEP_EMPTY_RESULTBAR:
					$this->showEmptyResultBar();
					break;
				case self::STEP_RANKINGBAR:
					$this->showRankingBar();
					break;
				case self::STEP_PERCENTAGEBAR:
					$this->showPercentageBar();
					break;
				case self::STEP_LEVELBAR:
					$this->showLevelBar();
					break;
				case self::STEP_SMILEY:
					$this->showSmiley();
					break;
				case self::STEP_CHECK:
					$this->showCheck();
					break;
				case self::STEP_QUESTIONMARK:
					$this->showQuestionMark();
					break;
			}
			return true;
		} catch (MissingParameterException $e) {
			$this->setError('Fehler beim Aufruf von ucGraphics: '.$e->getMessage());			
			return false;
		}
	}
	
	/**
	 * Die Methode showBar erzeugt einen Balken, der den erwünschten Bereich für ein Merkmal und
	 * eine Linie für das erzielte Resultat.
	 * Zur Darstellung nutzt der UseCase die GET-Parameter 'minimum', 'maximum' und 'result', jeweils
	 * auf einer Skala von 0 bis 100.
	 *  'minimum' ist die Untergrenze des Wunschbereichs
	 *  'maximum' ist die Obergrenze des Wunschbereuchs
	 *  'result' ist das vom Nutzer erzielte Ergebnis
	 * Der Ergebnisbalken ist 20 Pixel hoch und 200 Pixel lang.
	 * Es werden drei Farben verwendet.
	 */
	private function showResultBar() {
		// Parameter einlesen		
		$minimum = $this->getParam()->getParameter(self::PAR_MINIMUM);
		if ($minimum == null)
			throw new MissingParameterException('Parameter '.self::PAR_MINIMUM.' fehlt.');
		$maximum = $this->getParam()->getParameter(self::PAR_MAXIMUM);
		if ($maximum == null)
			throw new MissingParameterException('Parameter '.self::PAR_MAXIMUM.' fehlt.');
		$result = $this->getParam()->getParameter(self::PAR_RESULT);
		if ($result == null)
			throw new MissingParameterException('Parameter '.self::PAR_RESULT.' fehlt.');
			
		$imgCreator = new ImageCreator($this->getConf());
		
		$this->sendContentTypeHeader(USECASE_PNG);
		$imgCreator->showResultBar($minimum, $maximum, $result);
	}
	
	

	/**
	 * erzeugt den "leeren" Balken
	 */
	private function showEmptyResultBar() {

		$imgCreator = new ImageCreator($this->getConf());
		
		$this->sendContentTypeHeader(USECASE_PNG);
		$imgCreator->showEmptyResultBar();
	}
	
	
	/**
	 * Die Methode showRankingBar erzeugt einen Balken, der in drei gleichgroße Bereiche
	 * unterteilt ist. über den Parameter PAR_RANING (0-100) kann das Ergebnis des Users
	 * angezeigt werden. 
	 */
	private function showRankingBar() {
		// Parameter einlesen		
		
		$ranking = $this->getParam()->getParameter(self::PAR_RANKING);
		if ($ranking == null)
			throw new MissingParameterException('Parameter '.self::PAR_RANKING.' fehlt.');
			
		$imgCreator = new ImageCreator($this->getConf());
		
		$this->sendContentTypeHeader(USECASE_PNG);
		
		$imgCreator->showRankingBar($ranking);
	}

	/**
	 * Die Methode showPercentageBar erzeugt einen Fortschrittsbalken
	 * Zur Darstellung nutzt der UseCase den GET-Parameter 'percentage'
	 * auf einer Skala von 0 bis 100.
	 */
	private function showPercentageBar() {
		// Parameter einlesen		
		$percentage = $this->getParam()->getParameter(self::PAR_PERCENTAGE);
		if ($percentage == null)
			throw new MissingParameterException('Parameter '.self::PAR_PERCENTAGE.' fehlt.');
			
		$imgCreator = new ImageCreator($this->getConf());
		
		$this->sendContentTypeHeader(USECASE_PNG);
		$imgCreator->showPercentageBar($percentage);
	}
	
	/**
	 * Zeigt einen Smiley an, abhängig vom übergebenen Parameter 'aspect'
	 * Mit dem optionalen Parameter Background 'bg' kann die Hintergrundfarbe bestimmt werden.
	 */
	private function showSmiley() {
		$aspect = $this->getParam()->getParameter(self::PAR_SMILEY_ASPECT);
		if ($aspect == null)
			throw new MissingParameterException('Parameter '.self::PAR_SMILEY_ASPECT.' fehlt.');
		$bgParam = $this->getParam()->getParameter(self::PAR_BACKGROUND);
		
		$bg = ImageCreator::BACKGROUND_WHITE;
		if ($bgParam==self::BACKGROUND_YELLOW) $bg = ImageCreator::BACKGROUND_YELLOW;
		
		$imgCreator = new ImageCreator($this->getConf());
		
		$this->sendContentTypeHeader(USECASE_PNG);
		$imgCreator->showSmiley($aspect, $bg);
	}
	
	/**
	 * Zeigt einen Haken an.
	 * Mit dem optionalen Parameter Background 'bg' kann die Hintergrundfarbe bestimmt werden.
	 */
	private function showCheck() {
		$bgParam = $this->getParam()->getParameter(self::PAR_BACKGROUND);
		
		$bg = ImageCreator::BACKGROUND_WHITE;
		if ($bgParam==self::BACKGROUND_YELLOW) $bg = ImageCreator::BACKGROUND_YELLOW;
			
		$imgCreator = new ImageCreator($this->getConf());
		
		$this->sendContentTypeHeader(USECASE_GIF);
		$imgCreator->showCheck($bg);
	}
	
	/**
	 * Zeigt ein Fragezeichen an.
	 * Mit dem optionalen Parameter Background 'bg' kann die Hintergrundfarbe bestimmt werden.
	 */
	private function showQuestionMark() {
		$bgParam = $this->getParam()->getParameter(self::PAR_BACKGROUND);
		
		$bg = ImageCreator::BACKGROUND_WHITE;
		if ($bgParam==self::BACKGROUND_YELLOW) $bg = ImageCreator::BACKGROUND_YELLOW;
			
		$imgCreator = new ImageCreator($this->getConf());
		
		$this->sendContentTypeHeader(USECASE_GIF);
		$imgCreator->showQuestionMark($bg);
	}

 }
?>
