<h1>Registrierung</h1>
<p class="error">###:###showerror###:###</p>
<form action="###:###registeraction###:###" method="post">
<input type="hidden" name="target" value="###:###targetlink###:###"/>
<table>
	<!--  Stammdaten Wiso@visor -->
	<tr>
		<td colspan="3">F&uuml;r die Durchf&uuml;hrung der angebotenen Tests ist eine Registrierung notwendig. 
		Hierdurch hast Du die M&ouml;glichkeit, die Beantwortung der Testfragen noch vor Testabschluss zu unterbrechen 
		und zu einem sp&auml;teren Zeitpunkt an der gleichen Stelle fortzufahren. Als Registrierungsdaten ben&ouml;tigen 
		wir Deine E-Mailadresse und ein Passwort, sowie einen Namen zur pers&ouml;nlichen Ansprache. 
		Durch die Angabe Deiner E-Mailadresse ist es m&ouml;glich, Dir jederzeit ein neues Passwort zuzuschicken. 
		Dar&uuml;ber hinaus kannst Du Dir auf Wunsch nach Beendigung aller Tests Deine Ergebnisse in einem 
		umfangreichen Gesamtdokument (<a href="grafik/pdf/musterreport.pdf" target="_blank">hier findest Du ein Beispiel</a>) per Mail 
		zuschicken lassen. Deshalb ist es wichtig, dass Du eine <b>korrekte E-Mailadresse</b> angibst.<br/>
		Das Passwort garantiert, dass nur Du Deine pers&ouml;nlichen Ergebnisse ansehen kannst.<br/><br/></td>
	</tr>
	<tr>
		<td>E-Mailadresse</td>
		<td><input type="text" name="email" value="###:###email###:###" tabindex="1"/></td>
		<td class="helptext">Gib hier Deine E-Mailadresse ein.</td>
	</tr>
	<tr>
		<td>Passwort</td>
		<td><input type="password" name="passwd" value="###:###password###:###" tabindex="2"/></td>
		<td rowspan="2" class="helptext">W&auml;hle hier ein Passwort und wiederhole es zur Sicherheit.</td>
	</tr>
	<tr>
		<td>Wiederholung</td>
		<td><input type="password" name="passwd_repeat" value="" tabindex="3"/></td>
	</tr>
	<tr>
		<td>(Vor)Name</td>
		<td><input type="text" name="username" value="###:###username###:###" tabindex="4"/></td>
		<td class="helptext">Wie sollen wir Dich ansprechen?</td>
	</tr>
	
	<!--  freiwillige Angaben fuer Statistik -->
	
	<tr>
		<td colspan="3"><br/>Die folgenden Angaben sind freiwillig. Sie dienen statistischen Auswertungen 
		und geben uns die M&ouml;glichkeit, zu ermitteln, welche Personen den WiSo@visor nutzen.</td>
	</tr>
	<tr>
		<td>Geschlecht</td>
		<td>###:###gender###:###</td>
		<td class="helptext">Bitte gib Dein Geschlecht an.</td>
	</tr>
	<tr>
		<td>Geburtsjahr</td>
		<td>###:###birthday###:###</td>
		<td class="helptext">Bitte gib Dein Geburtsjahr an.</td>
	</tr>
	
	<!--  Stammdaten fuer Wiso@visor V2 -->

	<tr>
		<td colspan="3"><br/>Die folgenden Daten solltest Du dann angeben, wenn Du bereits Student an der WiSo
		bist und die erweiterten Funktionen des Wiso@visor nutzen m&ouml;chtest. Darunter fallen z. B. die Studienverlaufsplanung
		und die Noten&uuml;bersicht.</td>
	</tr>
	<tr>
		<td>Matrikelnummer</td>
		<td><input type="text" name="matnr" value="###:###matnr###:###" tabindex="9"/></td>
		<td class="helptext">Bitte gib Deine Matrikelnummer an.</td>
	</tr>
	<tr>
		<td>Studiengang</td>
		<td>###:###studies###:###</td>
		<td class="helptext">Bitte gib Deinen Studiengang an.</td>
	</tr>
	<tr>
		<td>Studienbeginn</td>
		<td>###:###sem_start###:###</td>
		<td class="helptext">Bitte gib Deinen Studienbeginn an.</td>
	</tr>
		
	<!--  Datenschutzerklaerung -->
	
	<tr>
		<td colspan="3" class="justified"><br/>Bitte beachte auch unsere <a href="###:###datenschutzlink###:###">Datenschutzerkl&auml;rung</a>.<br/><br/>
		<input type="checkbox" name="datenschutz" id="datenschutz" value="datenschutz_akzeptiert" tabindex="11"/><label for="datenschutz"> Bitte kreuze das K&auml;stchen an, wenn Du damit einverstanden bist.</label></td>
	</tr>
	
	<!--  Submit -->
	<tr>
		<td colspan="3" class="right"><input type="submit" value="registrieren" id="surveyor_next_button" tabindex="12"/></td>
	</tr>
	
</table>
</form>