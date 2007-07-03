In diesem Unterordner liegen alle HTML-Templates für "statische" Seiten.
Der Aufruf erfolgt jeweils über index.php?action=static&step=[parameter],
wobei [parameter] durch den (Datei-)Namen des Templates in diesem Ordner ersetzt wird.

Bsp.: Liegt hier ein Template "impressum.tpl" wird es durch den Aufruf von index.php?action=static&step=impressum angezeigt.

Die Templates in diesem Ordner werden in den Content-Bereich des allgemeinen Grundtemplates eingefügt!

WICHTIG:
Bitte nur "Plain HTML", also möglichst einfache Tags (p, h1, h2, ...) ohne Klassen etc. verwenden - der Sauberkeit wegen.
Die Formatierung der Elemente sollte über die Stylesheets im CSS-Bereich erfolgen.

WEITER WICHTIG:
Es gibt 2 Sichtbarkeitsstufen: Liegt ein Template im Unterordner "authenticated" wird es nur eingeloggten Usern angezeigt; andere Templates sind "öffentlich" und werden jedem angezeigt.