<script language="JavaScript">
var isIEDiv = false;
	var isN6Div = false;
	var dxDiv = 0, dyDiv = 0;
	var currentDiv = null;
	var objDiv = null;
	
	var aktionStartX;
	var aktionStartY;
	
	if ((navigator.appName.indexOf("Microsoft") > -1)&&((parseInt(navigator.appVersion) >= 4))) {
		isIEDiv = true;
	}
	if (( navigator.appName.indexOf("Netscape") != -1 ) && (parseInt(navigator.appVersion) >= 5) ) {
		isN6Div = true;
	}
	
	function OpenDivForm( objName, imgName, ImgPathOpen, ImgPathClose, divName, OpenStr, CloseStr ) {
		objDiv = GetObjDiv( objName );
		var objTextChange = GetObjDiv( divName );
	
		if (isIEDiv) {
			if (objDiv.style.display == "none") {
				objDiv.style.display = "inline";
			}else {
				objDiv.style.display = "none";
			}
		}else {
			// alert('V: ' + objDiv.style.visibility + ' # D: ' + objDiv.style.display);
			if (objDiv.style.display == "none" || objDiv.style.visibility == "hidden") {
				objDiv.style.visibility = "visible";
				window.setTimeout('', 1000);
				objDiv.style.display = "inline";
			}else {
				objDiv.style.visibility = null;
				window.setTimeout('', 1000);
				objDiv.style.display = "none";
			}
			// alert('V: ' + objDiv.style.visibility + ' # D: ' + objDiv.style.display);
		}
	}
	
	function GetObjDiv( objName ) {
		if (( navigator.appName.indexOf("Netscape") != -1 ) && (parseInt(navigator.appVersion) >= 5) ) {
			var objDiv = document.getElementById(objName);
		}
		if ((navigator.appName.indexOf("Microsoft") > -1)&&((parseInt(navigator.appVersion) >= 4))) {
			var objDiv = document.all[objName];
		}
		return objDiv;
	}
</script>
<h2>Pr&uuml;fungsplan für ###:###username###:###<br/>###:###studies###:###</h2>
