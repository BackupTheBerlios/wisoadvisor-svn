<h1>Registrierung</h1>
<p class="error">###:###showerror###:###</p>
<form action="###:###registeraction###:###" method="post">
<input type="hidden" name="target" value="###:###targetlink###:###"/>
<table>
	<tr>
		<td colspan="3">Für die Durchführung der angebotenen Tests ist eine Registrierung notwendig. 
		Hierdurch hast Du die Möglichkeit, die Beantwortung der Testfragen noch vor Testabschluss zu unterbrechen 
		und zu einem späteren Zeitpunkt an der gleichen Stelle fortzufahren. Als Registrierungsdaten benötigen 
		wir Deine E-Mailadresse und ein Passwort, sowie einen Namen zur persönlichen Ansprache. 
		Durch die Angabe Deiner E-Mailadresse ist es möglich, Dir jederzeit ein neues Passwort zuzuschicken. 
		Darüber hinaus kannst Du Dir auf Wunsch nach Beendigung aller Tests Deine Ergebnisse in einem 
		umfangreichen Gesamtdokument (<a href="grafik/pdf/musterreport.pdf" target="_blank">hier findest Du ein Beispiel</a>) per Mail 
		zuschicken lassen. Deshalb ist es wichtig, dass Du eine <b>korrekte E-Mailadresse</b> angibst.<br/>
		Das Passwort garantiert, dass nur Du 
		Deine persönlichen Ergebnisse ansehen kannst.<br/><br/></td>
	</tr>
	<tr>
		<td>E-Mailadresse</td>
		<td><input type="text" name="email" value="###:###email###:###" tabindex="1"/></td>
		<td class="helptext">Gib hier Deine E-Mailadresse ein.</td>
	</tr>
	<tr>
		<td>Passwort</td>
		<td><input type="password" name="passwd" value="###:###password###:###" tabindex="2"/></td>
		<td rowspan="2" class="helptext">Wähle hier ein Passwort und wiederhole es zur Sicherheit.</td>
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
	<tr>
		<td colspan="3"><br/>Die folgenden Angaben sind freiwillig. Sie dienen statistischen Auswertungen 
		und geben uns die Möglichkeit, zu ermitteln, welche Personen den WiSo@visor nutzen.</td>
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
	<tr>
		<td colspan="3" class="justified"><br/>Bitte beachte auch unsere <a href="###:###datenschutzlink###:###">Datenschutzerklärung</a>.<br/><br/>
		<input type="checkbox" name="datenschutz" id="datenschutz" value="datenschutz_akzeptiert" tabindex="9"/><label for="datenschutz"> Bitte kreuze das Kästchen an, wenn Du damit einverstanden bist.</label></td>
	</tr>
	<tr>
		<td colspan="3" class="right"><input type="submit" value="registrieren" id="surveyor_next_button" tabindex="10"/></td>
	</tr>
</table>
</form>