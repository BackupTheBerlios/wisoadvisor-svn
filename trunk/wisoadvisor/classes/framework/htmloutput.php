<?php
//Der HtmlGenerator sorgt für die Umsetzung von Ausgaben der UseCases in HTML.
class HtmlGenerator
{
	//Member-Variablen (bzw. -objekte)
	private $indicatorPre = '';
	private $indicatorAfter = '';
	private $templateFile = '';
	private $replacements = null;

	//Konstruktor
	public function HtmlGenerator( $templateFile, $indicatorPre, $indicatorAfter)
	{
		//initialisiere Member
		$this->indicatorPre = $indicatorPre;
		$this->indicatorAfter = $indicatorAfter;
		$this->templateFile = $templateFile;
	}

	/*
	 * setTemplate() weist dem generator eine neue Template-Datei zu
	 * @param $templateFile Dateiname inkl. Pfad
	 * @return true
	 */
	public function setTemplate($templateFile)
	{
		$this->templateFile = $templateFile;
		return true;
	}
	
	//apply weist einem Indikator, der im Template steht den Wert des übergebenen Inhalts zu
	public function apply( $indicator, $input )
	{
		$this->replacements[] = array('indicator'=>$indicator, 'input'=>$input);
	}

	//getHTML sorgt für die Ausgabe der 'fertigen' HTML-Seite
	public function getHTML()
	{
		//zuerst: lies das Template ein
		$template = file_get_contents($this->templateFile);
		
		//dann: suche nach "IndikatorStrings" und ersetze sie mit den korrekten Ersetzungen
    if (!empty($this->replacements)) {
			foreach ($this->replacements as $replacement) {
				$template = str_replace( $this->indicatorPre.$replacement['indicator'].$this->indicatorAfter, 
								$replacement['input'], 
								$template );
			}
    }
		//fertig: das template wurde verändert und kann zurückgegeben werden
		return $template;
	}
}
?>