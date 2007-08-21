<html>
<head>
	<title>WiSo@visor</title>
	<link rel=stylesheet type="text/css" href="../css/layout.css">
	<link rel=stylesheet type="text/css" href="../css/elements.css">
	<link rel=stylesheet type="text/css" href="../css/startpage.css">
	<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
</head>

<body>
<div id="layout">
	<div id="header"><a href="../index.php" alt="WiSo@visor Startseite"><img src="/wisoadvisor/grafik/logos/wiso.gif"/></a></div>
	<div id="menu">
	  <p>&nbsp;{HOME}<br/>
	     &nbsp;&nbsp;{PAGE_TITLE}</p>
		<div id="common_links">
			<a href="../index.php" class="menulink"><img src="../grafik/pfeil.gif"/>&nbsp;Startseite</a><br/>
			<a href="../index.php?action=overview&step=" class="menulink"><img src="../grafik/pfeil.gif"/>&nbsp;Ergebnis&uuml;bersicht</a><br/>
			<a href="../index.php?action=feedback&step=&reference=Position%3A" target="_blank" onClick="window.open(this.href, 'WiSoAdVisorPopup', 'dependent=yes,height=600,width=600,locationbar=no,toolbar=no,menubar=no,directories=no,scrollbars=yes,resizable=yes,status=no,screenX=100,screenY=100');return false" class="menulink"><img src="../grafik/pfeil.gif"/>&nbsp;Feedback</a><br/>
			<br/>		
			<a href="http://www.wiso.uni-erlangen.de" target="blank" class="menulink"><img src="../grafik/pfeil.gif"/>&nbsp;WiSo-Fakult&auml;t</a><br/>
			<a href="./index.php" target="blank" class="menulink"><img src="../grafik/pfeil.gif"/>&nbsp;Studienanfang</a><br/>
			<a href="http://www.wiso.uni-erlangen.de/studium/studienberatung/index.shtml" target="blank" class="menulink"><img src="../grafik/pfeil.gif"/>&nbsp;Studienberatung</a><br/>
			<br/>
			<a href="../index.php?action=static&step=datenschutz" class="menulink"><img src="../grafik/pfeil.gif"/>&nbsp;Datenschutz</a><br/>
			<a href="../index.php?action=static&step=impressum" class="menulink"><img src="../grafik/pfeil.gif"/>&nbsp;Impressum</a><br/>
		</div>
	</div>

  <div id="content">
		<div class="error">{ERROR}</div>
		{CONTENT}
  	<p>&nbsp;</p>	
  	<p>&nbsp;</p>	
  	<p>&nbsp;</p>	
		<div align="right">
			{EDIT} {HELP}
		</div>
  </div>
  
</div>
</body>
</html>