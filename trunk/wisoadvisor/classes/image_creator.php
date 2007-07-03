<?php
/***********************************************************************************
 * WiSo@visor - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de,
 *                                Michael.Gottfried@freenet.de
 *
 * zeichnet verschiedene Grafiken, entweder Ausgabe direkt im Browser
 * oder als File auf der Platte
 ***********************************************************************************/
 
class ImageCreator {
 	
 	// Allgemeine Parameter
 	const BACKGROUND_WHITE = 1;
 	const BACKGROUND_YELLOW = 2;
 	
  	// Parameter für showSmiley
 	const SMILEY_GOOD = 1;
 	const SMILEY_AVERAGE = 2;
 	const SMILEY_POOR = 3;
 	const SMILEY_UNKNOWN = 4;
 
 	//Member:
 	private $conf = null;
 	
	/**
	 * Konstruktor - benötigt wird ein Framework-Config-Container
	 */
	public function __construct(Configuration $configContainer)
	{
		$this->conf = $configContainer;	
	}
	
	/**
	 * Liefert eine definierte Farbe
	 * @param int $image Das Imageobjekt von PHP
	 * @param String $confText Der Konfigurationstext für die Farbe
	 * @return int Farb-ID für die gewünschte Farbe
	 */
	private function getColor($image, $confText) {
		$colorInfo = $this->conf->getConfString('ImageCreator', 'color', $confText);
		$infos = explode (',', $colorInfo);
		return imagecolorallocate($image, $infos[0], $infos[1], $infos[2]);
	}
	
	/**
	 * handelt den Output
	 * @param $image Image-Handler
	 * @param $filename optional übergebener Dateiname zum Schreiben des Bildes als Datei
	 */
	private function pngOutput($image, $filename = null)
	{
		if ($filename)
		{
			imagepng($image, $filename);
		}
		else
		{
			header('Content-type:image/png');
	  		imagepng($image);
		}

  		imagedestroy($image);
	}
	
	/**
	 * handelt den GifOutput
	 * ACHTUNG: Gifs werden NUR aus der Datei gelesen, deshalb wird sie direkt durchgepiped!
	 * @param $image Inhalt des Bildes
	 * @param $filename optional übergebener Dateiname zum Schreiben des Bildes als Datei
	 */
	private function gifOutput($image, $filename = null)
	{
		if ($filename)
		{
			file_put_contents($filename, $image);
		}
		else
		{
			header('Content-type:image/gif');
	  		echo $image;
		}
	}
	

	/**
	 * Die Methode showRankingBar erzeugt einen in Drittel unterteilten Balken
	 * @param $result ist das vom Nutzer erzielte Ergebnis
	 * @param $swColors kann auf true gesetzt werden, um an sw-Ausdruck optimierte Farben zu erhalten
	 * @param String $filename wenn ein Dateiname angegeben wird, dann wird das Bild als Datei ausgegeben
	 */
	public function showRankingBar($result, $swColors = false, $filename = null) 
	{
		//Definitionen
		$width = 200;
		$height = 20;
		$markerWidth = 4;
		
		//Man sieht die Linie am Rand besser, wenn man sie etwas in die Mitte verschiebt
		//...und zwar auf die "halbe MarkerWidth", damit der Marker in voller Breite angezeigt wird
		if ($result >= 99) $result = $result - floor($markerWidth/2) + 1;
		if ($result <= 1) $result = floor($markerWidth/2) - 1;
		
		//Umrechnung (auf Balkenbreite):
		$oneThird = (int) round(33.33 * ($width / 100));
		$twoThirds = (int) round(66.66 * ($width / 100));
		
		$result = (int) round($result * ($width / 100));

		// Bild vorbereiten
		$image = imagecreate($width,$height);

		// Farben laden
		$colorFirstThird = $this->getColor($image, 'lightblue');
		$colorSecondThird = $this->getColor($image, 'middleblue');
		$colorThirdThird = $this->getColor($image, 'darkblue');
		$colorMarker = $this->getColor($image, 'yellow');
		$colorMarkerBorder = $colorMarker;
		
		//bei 'angepassten' Farben wird der Marker weiß mit schwarzer Umrandung
		if ($swColors) 
		{
			$colorMarker = $this->getColor($image, 'white');
			$colorMarkerBorder = $this->getColor($image, 'black');
		}
		
		// Zeichnen
		imagefilledrectangle($image, 0, 0, $oneThird, $height, $colorFirstThird);
		imagefilledrectangle($image, $oneThird, 0, $twoThirds, $height, $colorSecondThird); 
		imagefilledrectangle($image, $twoThirds, 0, $width, $height, $colorThirdThird); 
		//Strich:
		imagefilledrectangle($image, (int) ($result - ($markerWidth/2)), 0, (int) ($result + ($markerWidth/2)), $height, $colorMarker); 
		imagerectangle($image, (int) ($result - ($markerWidth/2)), 0, (int) ($result + ($markerWidth/2)), $height-1, $colorMarkerBorder); 

		// Ausgeben
		$this->pngOutput($image, $filename);
	}

	/**
	 * Die Methode showBar erzeugt einen Balken, der den erwünschten Bereich für ein Merkmal und
	 * eine Linie für das erzielte Resultat.
	 * Zur Darstellung nutzt der UseCase die GET-Parameter 'minimum', 'maximum' und 'result', jeweils
	 * auf einer Skala von 0 bis 100.
	 * @param $minimum ist die Untergrenze des Wunschbereichs
	 * @param $maximum ist die Obergrenze des Wunschbereuchs
	 * @param $result ist das vom Nutzer erzielte Ergebnis
	 * @param $swColors kann auf true gesetzt werden, um an sw-Ausdruck optimierte Farben zu erhalten
	 * @param String $filename wenn ein Dateiname angegeben wird, dann wird das Bild als Datei ausgegeben
	 * Der Ergebnisbalken ist 20 Pixel hoch und 200 Pixel lang.
	 * Es werden drei Farben verwendet.
	 */
	public function showResultBar($minimum, $maximum, $result, $swColors = false, $filename = null) 
	{
		//Definitionen
		$width = 200;
		$height = 20;
		$markerWidth = 4;
		
		//Man sieht die Linie am Rand besser, wenn man sie etwas in die Mitte verschiebt
		//...und zwar auf die "halbe MarkerWidth", damit der Marker in voller Breite angezeigt wird
		if ($result >= 99) $result = $result - floor($markerWidth/2) + 1;
		if ($result <= 1) $result = floor($markerWidth/2) - 1;

		//Umrechnung (auf Balkenbreite):
		$minimum = (int) round($minimum * ($width / 100));
		$maximum = (int) round($maximum * ($width / 100));
		
		$result = (int) round($result * ($width / 100));

			
		// Bild vorbereiten
		$image = imagecreate(200,20);
		
		// Farben laden
		$colorBackground = $this->getColor($image, 'lightblue');
		$colorCompare = $this->getColor($image, 'darkblue');
		$colorMarker = $this->getColor($image, 'yellow');
		$colorMarkerBorder = $colorMarker;
		
		//bei 'angepassten' Farben wird der Marker weiß, mit schwarzer Umrandung
		if ($swColors)
		{
			$colorMarker = $this->getColor($image, 'white');
			$colorMarkerBorder = $this->getColor($image, 'black');
		}
				
		// Zeichnen
		imagefilledrectangle($image, 0, 0, $width, $height, $colorBackground);
		imagefilledrectangle($image, $minimum, 0, $maximum, $height, $colorCompare);
		imagefilledrectangle($image,  (int) ($result - ($markerWidth/2)), 0, (int) ($result + ($markerWidth/2)), $height, $colorMarker);
		imagerectangle($image, (int) ($result - ($markerWidth/2)), 0, (int) ($result + ($markerWidth/2)), $height-1, $colorMarkerBorder); 
		
		//imagerectangle($image, 0, 0, 199, 19, $colorYe);
		
		// Ausgeben
		$this->pngOutput($image, $filename);
	}

	/**
	 * Die Methode showEmptyBar erzeugt einen grauen Balken
	 * @param String $filename wenn ein Dateiname angegeben wird, dann wird das Bild als Datei ausgegeben
	 * Der Ergebnisbalken ist 20 Pixel hoch und 200 Pixel lang.
	 * Es werden drei Farben verwendet.
	 */
	public function showEmptyResultBar($filename = null) {
			
		// Bild vorbereiten
		$image = imagecreatefrompng( $this->conf->getConfString('ImageCreator', 'image', 'balken_leer') );

		// Ausgeben
		$this->pngOutput($image, $filename);
	}

	/**
	 * Die Methode showPercentageBar erzeugt einen Fortschrittsbalken
	 * auf einer Skala von 0 bis 100.
	 * Der Ergebnisbalken ist 15 Pixel hoch und 120 Pixel lang.
	 * Es werden drei Farben verwendet.
	 * @param int $percentage Prozentzahl
	 * @param String $filename wenn ein Dateiname angegeben wird, dann wird das Bild als Datei ausgegeben
	 */
	public function showPercentageBar($percentage, $filename = null) {
		
		//Definitionen
		$width = 120;
		$height = 15;
		
		//Umrechnung (auf Balkenbreite):
		$percentage = (int) round($percentage * ($width / 100));
		
		// Bild vorbereiten
		$image = imagecreate($width, $height);

		// Farben laden
		$colorLb = $this->getColor($image, 'lightblue');
		$colorYe = $this->getColor($image, 'yellow');
		
		// Zeichnen
		imagefilledrectangle($image, 0, 0, $width, $height, $colorLb);
		imagefilledrectangle($image, 0, 0, $percentage, $height, $colorYe);
		
		// Ausgeben
		$this->pngOutput($image, $filename);
	}
	
	/**
	 * Zeigt einen Smiley an, abhängig vom übergebenen Parameter 'aspect'
	 * @param int $aspect 'Ausdruck' des Smileys
	 * @param int $bg optional - anderer Hintergrund 
	 * @param String $filename wenn ein Dateiname angegeben wird, dann wird das Bild als Datei ausgegeben
	 */
	public function showSmiley($aspect, $bg = self::BACKGROUND_WHITE, $filename = null) 
	{
		$smileyfilename = null;
		if ($bg != self::BACKGROUND_YELLOW) {
			switch ($aspect) {
				case self::SMILEY_UNKNOWN:
					$smileyfilename = $this->conf->getConfString('ImageCreator', 'image', 'smiley_unknown');
					break;
				case self::SMILEY_POOR:
					$smileyfilename =  $this->conf->getConfString('ImageCreator', 'image', 'smiley_poor');
					break;
				case self::SMILEY_AVERAGE:
					$smileyfilename =  $this->conf->getConfString('ImageCreator', 'image', 'smiley_average');
					break;
				case self::SMILEY_GOOD:
					$smileyfilename = $this->conf->getConfString('ImageCreator', 'image', 'smiley_good');
					break;
			}
		} else {
			switch ($aspect) {
				case self::SMILEY_UNKNOWN:
					$smileyfilename = $this->conf->getConfString('ImageCreator', 'image', 'smiley_unknown_th');
					break;
				case self::SMILEY_POOR:
					$smileyfilename =  $this->conf->getConfString('ImageCreator', 'image', 'smiley_poor_th');
					break;
				case self::SMILEY_AVERAGE:
					$smileyfilename =   $this->conf->getConfString('ImageCreator', 'image', 'smiley_average_th');
					break;
				case self::SMILEY_GOOD:
					$smileyfilename = $this->conf->getConfString('ImageCreator', 'image', 'smiley_good_th');
					break;
			}
		}
		if ($smileyfilename == null) 
			throw new MissingParameterException('Parameter $aspect hat ungültigen Wert.');
		$image = imagecreatefrompng($smileyfilename);
		
		$this->pngOutput($image, $filename);
	}
	
	/**
	 * Zeigt einen Haken an.
	 * @param int $bg optional - anderer Hintergrund 
	 * @param String $filename wenn ein Dateiname angegeben wird, dann wird das Bild als Datei ausgegeben
	 */
	public function showCheck($bg = self::BACKGROUND_WHITE, $filename = null) 
	{
		$hakenfilename = $this->conf->getConfString('ImageCreator', 'image', 'check');
		if ($bg == self::BACKGROUND_YELLOW)
			$hakenfilename = $this->conf->getConfString('ImageCreator', 'image', 'check_th');
			
		$image = file_get_contents($hakenfilename);
		
		$this->gifOutput($image, $filename);
	}
	
	/**
	 * Zeigt ein Fragezeichen an.
	 * @param int $bg optional - anderer Hintergrund 
	 * @param String $filename wenn ein Dateiname angegeben wird, dann wird das Bild als Datei ausgegeben
	 */
	public function showQuestionMark($bg = self::BACKGROUND_WHITE, $filename = null) 
	{
		$qufilename = $this->conf->getConfString('ImageCreator', 'image', 'questionmark');
		if ($bg == self::BACKGROUND_YELLOW)
			$qufilename = $this->conf->getConfString('ImageCreator', 'image', 'questionmark_th');
			
		$image = file_get_contents($qufilename);
		
		$this->gifOutput($image, $filename);
	}

 }
?>
