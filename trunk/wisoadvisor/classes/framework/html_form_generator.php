<?php
/***********************************************************************************
 * WiSo@visor - Online Studienberatung der WiSo-Fakultät
 * (c) 2006 Lehrstuhl für Wirtschaftsinformatik 3, Uni Erlangen-Nürnberg
 * Rückfragen zu dieser Software: kompetenzmanagement@floooooo.de
 *
 * Datei: html_form_generator.php
 * Erstellt am: 14.05.2006
 * Erstellt von: flo
 * 
 * Der htmlFormGenerator kann verschiedene HTML-Formularelemente automatisiert
 * generieren, so dass Sie nicht in den Code geschrieben werden müssen.
 * Im allgemeinen können die wichtigsten Parameter (name, value, ...)
 * angegeben werden.
 * 
 * Die Vorlagen zur Element-Generierung sind oben im Konstruktor direkt hinterlegt.
 * 
 * Derzeit werden folgende Attribute unterstützt:
 * value
 * class
 * name
 * id
 * tabindex
 * onClick
 * action
 * 
 * (werden weitere Attribute benötigt, können die entsprechenden Templates einfach geändert und erweitert werden)
 ***********************************************************************************/
 
 class HtmlFormGenerator
 {
 		private $template = Array(); //Templates für die HTML-Formularelemente 
 		private $attributes = Array(); //hier werden mögliche (HTML-)Attribute und deren Ersetzungsstring definiert 
 		private $attributePrefix = Array();
 		private $attributePostfix = Array();
 		private $indicator = '###:###'; //Anfangs- und Endindikator für Ersetzungen
 		
 		/*
 		 * Konstruktor
 		 */
 		public function __construct()
 		{
 			//die Templates für die Formularelemente sind hier abgelegt und können notfalls hier geändert werden:
 			
 			//HTML-Formularelemente:
 			$template['radio'] = '<input type="radio" ###:###CLASS###:######:###NAME###:######:###ID###:######:###VALUE###:######:###TABINDEX###:######:###ONCLICK###:######:###SELECTED###:###/>';
 			$template['check'] = '<input type="checkbox" ###:###CLASS###:######:###NAME###:######:###ID###:######:###VALUE###:######:###TABINDEX###:######:###ONCLICK###:######:###SELECTED###:###/>';
 			$template['inputText'] = '<input type="text" ###:###CLASS###:######:###NAME###:######:###ID###:######:###VALUE###:######:###TABINDEX###:######:###ONCLICK###:###/>';
 			$template['inputPwd'] = '<input type="password" ###:###CLASS###:######:###NAME###:######:###ID###:######:###VALUE###:######:###TABINDEX###:######:###ONCLICK###:###/>';
 			$template['hidden'] = '<input type="hidden" ###:###CLASS###:######:###NAME###:######:###ID###:######:###VALUE###:######:###TABINDEX###:######:###ONCLICK###:###/>';
 			$template['area'] = '<textarea ###:###NAME###:######:###ID###:###>###:###VALUE###:###</textarea>';
 			$template['label'] = '<label ###:###FOR###:###>###:###VALUE###:###</label>';
  			//Buttons:
 			$template['button'] = '<input type="button" ###:###CLASS###:######:###NAME###:######:###ID###:######:###VALUE###:######:###TABINDEX###:######:###ONCLICK###:###/>';
 			$template['submit'] = '<input type="submit" ###:###CLASS###:######:###NAME###:######:###ID###:######:###VALUE###:######:###TABINDEX###:######:###ONCLICK###:###/>';
 			$template['reset'] = '<input type="reset" ###:###CLASS###:######:###NAME###:######:###ID###:######:###VALUE###:######:###TABINDEX###:######:###ONCLICK###:###/>';
			//Formularanfang und -ende
			//als Methode wird immer POST verwendet
			$template['formBegin'] = '<form ###:###NAME###:######:###ID###:### method="POST" ###:###ACTION###:###>';
			$template['formEnd'] = '</form>';
			
			//dem Member zuweisen:
			$this->template = $template;
			
			//Ersetzungen:
			$attributes['value'] = 'VALUE';
			$attributes['class'] = 'CLASS';
			$attributes['name'] = 'NAME';
			$attributes['id'] = 'ID';
			$attributes['tabindex'] = 'TABINDEX';
			$attributes['onclick'] = 'ONCLICK';
			$attributes['action'] = 'ACTION';
			$attributes['for'] = 'FOR';
			$attributes['selected'] = 'SELECTED';
			
			//dem Member zuweisen:
			$this->attributes = $attributes;
			
			//Pre- und Postfixe:
			$attributePrefix['value'] = 'value="';
			$attributePrefix['class'] = 'class="';
			$attributePrefix['name'] = 'name="';
			$attributePrefix['id'] = 'id="';
			$attributePrefix['tabindex'] = 'tabindex="';
			$attributePrefix['onclick'] = 'onClick="';
			$attributePrefix['action'] = 'action="';
			$attributePrefix['for'] = 'for="';
			$attributePrefix['selected'] = 'checked="';

			$attributePostfix['value'] = '" ';
			$attributePostfix['class'] = '" ';
			$attributePostfix['name'] = '" ';
			$attributePostfix['id'] = '" ';
			$attributePostfix['tabindex'] = '" ';
			$attributePostfix['onclick'] = '" ';
			$attributePostfix['action'] = '" ';
			$attributePostfix['for'] = '" ';
			$attributePostfix['selected'] = '" ';
			
			//dem Member zuweisen
			$this->attributePrefix = $attributePrefix;
			$this->attributePostfix = $attributePostfix;

 		}
 	
 		/**
 		 * getTpl() gibt ein Formular-Template zurück
 		 * @param $key der SChlüssel, unter dem es in $template abgelegt ist
 		 * @return das Template als String
 		 * @exception eine Exception, wenn der Schlüssel nicht existiert
 		 */
 		 private function getTpl($key)
 		 {
 		 	if ( array_key_exists($key, $this->template) ) return $this->template[$key];
 		 	else throw new HtmlFormGeneratorException('getTpl(): key "'.$key.'" doesnt exist');
 		 }
 		 
 		 /**
 		  * apply() ersetzt in einem Template-String den angegebenen Indikator durch den übergebenen Wert
 		  * @param $template der zu verändernde String
 		  * @param $key der zu ersetzende Indikator
 		  * @param $value der Wert, mit dem der $key ersetzt werden soll
 		  * @return der entsprechende veränderte String
 		  */
 		  private function apply($template, $key, $value = '')
 		  {
 		  	return str_replace( $this->indicator.$key.$this->indicator, 
								$value, 
								$template );
 		  } 
 		  
		 /**
		  * getElement() gibt ein Formularelement (befüllt) zurück
		  * dazu wird das -Data-Objekt benötigt
		  * @param $type Elementtyp als String
		  * @param $data HtmlFormGeneratorData
		  * @return das Element als String
		  */
		  private function getElement($type, HtmlFormGeneratorData $data)
		  {
		  	//zu benutzendes Template
		  	$type = (string) $type;
		  	$template = $this->getTpl($type);
		  	
		  	//Daten in ein Array laden:
		  	$dataArr = $data->getAllAsArray();
		  	
		  	//nun wird das Template befüllt; dazu kann das Array durchlaufen werden:
		  	//es wird jeweils das entsprechende HTML-Attribut eingefügt:
		  	foreach($dataArr as $key => $value)
		  	{
		  		$replacement = '';
		  		//wenn value="", dann wird gar kein Value übergeben
		  		//dazu vorerst Workaround: wenn's eine Textarea oder Label ist, dann muss das value-Attribut anders behandelt werden
		  		if ( ($value == '') || 
		  				(	
		  					(($type == 'area') || ($type == 'label')) 
		  					&& ($key == 'value')	
		  				)
		  			)
		  				$replacement = $value;
		  		else $replacement = $this->attributePrefix[$key].$value.$this->attributePostfix[$key];
		  		
		  		$template = $this->apply(	$template, 
		  									$this->attributes[$key], 
		  									$replacement
		  								);
		  	}
		  	
		  	return $template;
		  }
		 
 		 /**
 		  * getButton() gibt einen HTML-Button zurück
 		  * @param $elementName Name des Buttons
 		  * @param $value (otional) Beschriftung
 		  * @param $javaScript (optional): JavaScript-Funktion für OnClick-Handler
 		  * @deprecated deprecated
 		  */
 		  public function getButton($elementName, $value='', $javaScript = '')
 		  {
 		  	$data = new HtmlFormGeneratorData();
 		  	
 		  	$data->setNameAndId( (string) $elementName);
 		  	$data->setValue((string) $value);
 		  	$data->setOnClick((string) $javaScript);
 		  	
  		  	return $this->getElement('button', $data);
 		  }
 		 
 		 /**
 		  * getSubmitButton() gibt einen HTML-Submit-Button zurück
 		  * @param $elementName Name des Buttons
 		  * @param $value (optional) Beschriftung
 		  * @param $javaScript (optional): JavaScript-Funktion für OnClick-Handler
 		  * @param $tabIndex (optional): Tabindex des Buttons
 		  * @deprecated deprecated
 		  */
 		  public function getSubmitButton($elementName, $value='', $javaScript = '', $tabIndex = '')
 		  {
		  	$data = new HtmlFormGeneratorData();
 		  	
 		  	$data->setNameAndId((string) $elementName);
 		  	$data->setValue((string) $value);
 		  	$data->setOnClick((string) $javaScript);
 		  	$data->setTabIndex((string) $tabIndex);
 		  	
  		  	return $this->getElement('submit', $data);
 		  }
 		 
  		 /**
 		  * getResetButton() gibt einen HTML-Reset-Button zurück
 		  * @param $elementName Name des Buttons
 		  * @param $value (otional) Beschriftung
 		  * @param $javaScript (optional): JavaScript-Funktion für OnClick-Handler
 		  * @deprecated deprecated
 		  */
 		  public function getResetButton($elementName, $value='', $javaScript = '')
 		  {
 		  	$data = new HtmlFormGeneratorData();
 		  	
 		  	$data->setNameAndId((string) $elementName);
 		  	$data->setValue((string) $value);
 		  	$data->setOnClick((string) $javaScript);
 		  	
  		  	return $this->getElement('reset', $data);
 		  }
 		 
  		 /**
 		  * getCheckbox() gibt eine Checkbox zurück
 		  * @param $elementName Name des Elements
 		  * @param $value (otional) Wert
 		  * @param $css (optional): CSS-Klasse
 		  * @deprecated deprecated
 		  */
 		  public function getCheckbox($elementName, $value='', $css = '')
 		  {
  		  	$data = new HtmlFormGeneratorData();
 		  	
 		  	$data->setNameAndId((string) $elementName);
 		  	$data->setValue((string) $value);
 		  	$data->setClass((string) $css);
 		  	
  		  	return $this->getElement('check', $data);
 		  }
 		 
  		 /**
 		  * getRadio() gibt eine Radiobox zurück
 		  * @param $elementName Name des Elements
 		  * @param $elementId ID des Elements (wg. Labeling sollten IDs einer gleichNAMEigen Gruppe unterschiedlich sein!)
 		  * @param $value (optional) Wert
 		  * @param $css (optional): CSS-Klasse
 		  * @deprecated deprecated
 		  */
 		  public function getRadio($elementName, $elementId, $value='', $css = '')
 		  {
  		  	$data = new HtmlFormGeneratorData();
 		  	
 		  	$data->setName((string) $elementName);
 		  	$data->setId((string) $elementId);
 		  	$data->setValue((string) $value);
 		  	$data->setClass((string) $css);
 		  	
  		  	return $this->getElement('radio', $data);
 		  }
 	
  		 /**
 		  * getTextArea() gibt eine Textarea zurück
 		  * @param $elementName Name des Elements
 		  * @param $value (otional) Beschriftung
 		  * @deprecated deprecated
 		  */
 		  public function getTextArea($elementName, $value='')
 		  {
  		  	$data = new HtmlFormGeneratorData();
 		  	
 		  	$data->setNameAndId((string) $elementName);
 		  	$data->setValue((string) $value);
		  	
  		  	return $this->getElement('area', $data);
 		  }
 	
  		 /**
 		  * getHiddenInput() gibt ein verstecktes Input-Feld zurück
 		  * @param $elementName Name des Elements
 		  * @param $value (optional) Wert
 		  * @deprecated deprecated
 		  */
 		  public function getHiddenInput($elementName, $value='')
 		  {
  		  	$data = new HtmlFormGeneratorData();
 		  	
 		  	$data->setNameAndId((string) $elementName);
 		  	$data->setValue((string) $value);
		  	
  		  	return $this->getElement('hidden', $data);
 		  }

  		 /**
 		  * getInput() gibt ein Text-Feld zurück
 		  * @param $elementName Name des Elements
 		  * @param $value (otional) Wert
 		  * @param $css (optional): CSS-Klasse
 		  * @deprecated deprecated
 		  */
 		  public function getInput($elementName, $value='', $css = '')
 		  {
   		  	$data = new HtmlFormGeneratorData();
 		  	
 		  	$data->setNameAndId((string) $elementName);
 		  	$data->setValue((string) $value);
 		  	$data->setClass((string) $css);
		  	
  		  	return $this->getElement('inputText', $data);
 		  }

  		 /**
 		  * getPasswordInput() gibt ein Password-Feld zurück
 		  * @param $elementName Name des Elements
 		  * @param $value (otional) Wert
 		  * @param $css (optional): CSS-Klasse
 		  * @deprecated deprecated
 		  */
 		  public function getPasswordInput($elementName, $value='', $css = '')
 		  {
   		  	$data = new HtmlFormGeneratorData();
 		  	
 		  	$data->setNameAndId((string) $elementName);
 		  	$data->setValue((string) $value);
 		  	$data->setClass((string) $css);
		  	
  		  	return $this->getElement('inputPwd', $data);
 		  }
 		  
 		 /**
 		  * getFormBegin() gibt den Anfang eines HTML-Formulars zurück
 		  * @param $elementName Name des Formulars
 		  * @param $action die Action des Formulars
 		  * @return das <form>-Tag als String
 		   */
 		   private function getFormBegin($elementName, $action)
 		   {
   		  	$data = new HtmlFormGeneratorData();
 		  	
 		  	$data->setNameAndId((string) $elementName);
 		  	$data->setAction((string) $action);
		  	
  		  	return $this->getElement('formBegin', $data);
 		   }

 		 /**
 		  * getFormEnd() gibt das Ende eines HTML-Formulars zurück
 		  * @return das </form>-Tag als String
 		   */
 		   private function getFormEnd()
 		   {
 		   	 return $this->getTpl('formEnd');
 		   }

		/**
		 * getForm() gibt ein HTML-Formular zurück, wobei der Inhalt (inkl. Formularfelder) "selbst" erstellt werden muss
		 * und nur übergeben wird. So kann der $content z.B. mit den anderen Public-Methoden dieser Klasse zusammengesetzt
		 * werden (inkl. eigenem HTML-Code) und anschließend mit dieser Methode zu einem Formular zusammengebaut werden
		 * @param $formName Name (und ID) des Formulars
		 * @param $action Action des Formulars
		 * @param $content Inhalt des Formulars
		 * @return das komplette Formular als String
		 */
		 public function getForm($formName, $action, $content)
		 {
		 	return $this->getFormBegin($formName, $action).$content.$this->getFormEnd();	
		 }

		/**
		 * getLabeledText() erzeugt ein umschließendes Label-Tag um den Antworttext
		 * dadurch wird die Zugänglichkeit Erhöht
		 * @param $elementName ID des Feldes
		 * @param $value der Text
		 * @return das entsprechende HTML-Fragment als String
		 */
		 public function getLabeledText($elementName, $value = '')
		 {
   		  	$data = new HtmlFormGeneratorData();
 		  	
		  	$data->setValue((string) $value);
 		  	$data->setFor((string) $elementName);
		  	
  		  	return $this->getElement('label', $data);
		 }
		 
		 /**
		  * @param $data HtmlFormGeneratorData
		  */
		 public function getButtonByDataObject(HtmlFormGeneratorData $data)
		 {
		 	return $this->getElement('button', $data);
		 }

		 /**
		  * @param $data HtmlFormGeneratorData
		  */
		 public function getSubmitButtonByDataObject(HtmlFormGeneratorData $data)
		 {
		 	return $this->getElement('submit', $data);
		 }
		 
		 /**
		  * @param $data HtmlFormGeneratorData
		  */
		 public function getResetButtonByDataObject(HtmlFormGeneratorData $data)
		 {
		 	return $this->getElement('reset', $data);
		 }
		 
		 /**
		  * @param $data HtmlFormGeneratorData
		  */
		 public function getCheckboxByDataObject(HtmlFormGeneratorData $data)
		 {
		 	return $this->getElement('check', $data);
		 }
		 
		 /**
		  * @param $data HtmlFormGeneratorData
		  */
		 public function getRadioByDataObject(HtmlFormGeneratorData $data)
		 {
		 	return $this->getElement('radio', $data);
		 }
		 
		 /**
		  * @param $data HtmlFormGeneratorData
		  */
		 public function getHiddenInputByDataObject(HtmlFormGeneratorData $data)
		 {
		 	return $this->getElement('hidden', $data);
		 }
		 
		 /**
		  * @param $data HtmlFormGeneratorData
		  */
		 public function getInputByDataObject(HtmlFormGeneratorData $data)
		 {
		 	return $this->getElement('inputText', $data);
		 }
		 
		 /**
		  * @param $data HtmlFormGeneratorData
		  */
		 public function getPasswordInputByDataObject(HtmlFormGeneratorData $data)
		 {
		 	return $this->getElement('inputPwd', $data);
		 }
		 
		 /**
		  * @param $data HtmlFormGeneratorData
		  */
		 public function getLabeledTextByDataObject(HtmlFormGeneratorData $data)
		 {
		 	return $this->getElement('label', $data);
		 }
		 
		 /**
		  * @param $context ModelContext with database
		  * @param $iName Name for DropDown
		  * @param $iSql SQL query
		  * @param $key key for shown text in dropdown
		  * @param $value key to be added as dropdown value
		  * @param $highlightedValue highlighted text
		  */
     public static function getDropDownFromDb (ModelContext $context, $iName, $iSql, $key, $value, $highlightedValue) {
        $ret = '<select name="' . $iName . '">';
		    $resultSet = $context->getDb()->query($iSql);

		    while ($row = $context->getDb()->fetch_array($resultSet)) {
		      $ret .= "<option ";
		      if ($highlightedValue != "" && $highlightedValue == $row[$value]) {
		        $ret .= " selected=\"true\"";
		      }
		      $ret .= "value=\"".$row[$value]."\">" . $row[$key] . "</option>\n" ;
		    }
		    
		    $ret .= '</select>';
        return $ret;
     }
     
		 /**
		  * @param $iName Name for DropDown
		  * @param $highlightedValue highlighted text
		  */
     public static function getDropDownSemester($iName, $highlightedValue) {
        
       // 2006 is the first year with bachelor students
       // start of studies is always in winter
       $ret = '<select name="' . $iName . '">';
       for ($i=2007;$i<(date('Y')+3);$i++) {
         $value='ws'.$i;
		     $ret .= "<option ";
		     if (($highlightedValue != '' && $highlightedValue == $value) || (trim($highlightedValue)=='' && $i==2007)) {
		       $ret .= ' selected="true" ';
		     }
         $ret .= 'value="'.$value.'">Wintersemester ' .$i.'/'. substr($i+1, strlen(rtrim($i+1))-2, 2)."</option>\n" ;
       }
		   $ret .= '</select>';
       return $ret;
       
      }

 }
 
/**
 * HtmlFormGeneratorData() ist ein Hilfsobjekt, das mit den Daten für Formularelemente befüllt werden kann
 * besitzt einfache Getter und Setter für alle von HtmlFormGenerator unterstützten Attribute
 */
 class HtmlFormGeneratorData
 {
  	//unterstützte Attribute (mit Standardwert)
  	private $value = '';
  	private $cssclass = '';
  	private $name = '';
  	private $id = '';
  	private $tabindex = '';
  	private $onclick = '';
  	private $action = '';
  	private $selected = '';
  	private $labelFor = '';
  	
  	/**
 	 * Konstruktor
 	 */
 	public function __construct()
 	{
 	}
 		
	/**
	 * getAllAsArray() spezieller getter, der ALLE Attribute liefert
	 * ACHTUNG: Sollte nur vom HtmlFormGenerator benutzt werden
	 * ACHTUNG2: muss auch angepasst werden, falls neue Attribute hinzugefügt werden!
	 * @return ein assoziatives Array mit 'html-Attribut' = 'Wert' als Elemente
	 */
	 public function getAllAsArray()
	 {
	 	return Array( 	'value'=> $this->value, 
	 					'class'=> $this->cssclass,
	 					'name'=> $this->name,
	 					'id'=> $this->id,
	 					'tabindex'=> $this->tabindex,
	 					'onclick'=> $this->onclick,
	 					'action'=> $this->action,
	 					'selected'=> $this->selected,
	 					'for'=> $this->labelFor
	 				);	
	 }
	
	/**
	 * setter-Methoden
	 */ 	
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	public function setClass($class)
	{
		$this->cssclass = $class;	
	}
	
	public function setName($name)
	{
		$this->name = $name;
	}
	
	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function setTabIndex($tabindex)
	{
		$this->tabindex = $tabindex;
	}
	
	public function setOnClick($onclick)
	{
		$this->onclick = $onclick;
	}
	
	public function setAction($action)
	{
		$this->action = $action;
	}
	
	public function setSelected($selected)
	{
		$this->selected = $selected;
	}
	
	public function setFor($labelFor)
	{
		$this->labelFor = $labelFor;
	}
	
	/**
	 * noch einige "vereinfachende" Setter
	 */
	public function setNameAndId($id)
	{
		$this->id = $id;
		$this->name = $id;
	}

	/**
	 * getter-Methoden
	 */ 	
	public function getValue()
	{
		return (string) $this->value;
	}
	
	public function getClass()
	{
		return (string) $this->cssclass;
	}
	
	public function getName()
	{
		return (string) $this->name;
	}
	
	public function getId()
	{
		return (string) $this->id;
	}
	
	public function getOnClick()
	{
		return (string) $this->onclick;
	}
	
	public function getTabIndex()
	{
		return (string) $this->tabindex;
	}
	
	public function getAction()
	{
		return (string) $this->action;
	}
	
	public function getSelected()
	{
		return (string) $this->selected;
	}
	
	public function getFor()
	{
		return (string) $this->labelFor;
	}
}
?>
