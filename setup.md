# Initialisieren einer neuen Liga Instanz
## Inhaltsverzeichnis
- [Voraussetzungen](#voraussetzungen)
- [Vorbereitung](#vorbereitung)
  - [Herunterladen des Liga Codes](#codeladen)
  - [Erstellen der Datenbank](#initDB)
- [Starten des Liga Projekts](#ligaStart)  

<a name="voraussetzungen"></a>
## Voraussetzungen

Zunächst wird ein funktionierender Webserver benötigt. (Hierbei sind alle gängigen Webserver möglich. Z.B. Apache, Microsoft IIS ...)</br>
Lokal kann bspw. XAMPP genutzt werden um die Initialisierung und das Liga System zu testen.

Bevor Sie mit den weiteren Schritten fortfahren stellen Sie bitte sicher, dass sie diese Abhängigkeiten installiert haben:
- [PHP](https://www.php.net/) `>=7.0.X`
- [MariaDB](https://mariadb.org/) oder eine "baugleiche" Alternative wie bspw. [MySQL](https://www.mysql.com/)

### Zusätzlich genutzte Software
Zur Darstellung im Web werden folgende Dateien extern eingebunden:

- [jquery.js v3.3.1](https://code.jquery.com/jquery-3.3.1.slim.min.js) zu finden in `footer.html` Zeile 2
- [popper.js v1.14.7](https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js) zu finden in `footer.html` Zeile 3
- [bootstrap.js v4.3.1](https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js) zu finden in `footer.html` Zeile 4
- [bootstrap.css v4.3.1](https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css) zu finden in `header.html` Zeile 9
- [chart.js v2.8.0](https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js) zu finden in `index.php` Zeile 91 & `graph.php` Zeile 51
<a name="vorbereitung"></a>
## Vorbereitung

<a name="codeladen"></a>
### Herunterladen des Liga Codes in den gewünschten Webserver Pfad

1. Laden Sie das Liga projekt von Github mit folgendem Befehl.

          $ git clone https://github.com/Eurogroup-Consulting/liga
    (Alternativ können sie das [Liga](https://github.com/Eurogroup-Consulting/liga) Projekt über den Link als Zip herunterladen )

2. Kopieren Sie den heruntergeladenen Ordner in ihr gewünschtes Webserver Verzeichniss.</br>
Empfohlen ist hierbei der "root"-Pfad des Webservers. Sollte der "root"-Pfad gewählt werden fahren Sie bitte mit [Erstellen der benötigten Datenbank](#initDB) fort.<br>
Andernfalls folgt hier ein Beispiel für den Unterordner "Liga" mit XAMPP:
<br>
<br>
  2.1.  Fügen Sie den heruntergeladenen Code in den gewünschten Ordner  <br><br>
  2.2.  Öffnen Sie die Datei httpd.conf und passen Sie die Zeilen DocumentRoot und die davon     gefolgte ```<Directory>``` an. Bspw.</br>
  `DocumentRoot "C:\WEBSERVERPATH\Liga"`</br>
  ```Directory "C:\WEBSERVERPATH\Liga"  ```
<br><br>
  2.3  Passen Sie in der Datei `/layout/header.html` die angegebene `base` an. Bspw. </br> 
  `<base href="/Liga/" >`
  2.4  Passen Sie in der Datei `auth.php` die angegebene `baseURL` an. Bspw. </br> 
  `$baseURL = "/Liga";`

<a name="initDB"></a>
###  Erstellen der benötigten Datenbank 

1. Erstellen Sie Die Datenbank `liga_db`. 
2. Konfigurieren Sie in der Datei `/setup/liga_config.php` die Datenbankverbindung.
Hierzu passen Sie bitte die folgenden Felder an:</br>
`DB_USER` - Username mit Datenbank Zugriff auf `liga_db` </br>
`DB_PASSWORD` - Datenbank Passwort</br>
`DB_HOST` - Datenbank Hostname (Sollten sich Datenbank und Liga App auf dem selben Gerät befinden kann diese Einstellung auf localhost gesetzt bleiben)</br>
<a name="ligaStart"></a>
## Starten des Projekts

Starten Sie nun Ihren Webserver und rufen sie die Seite `/setup/init_db.php` in dem von Ihnen festgelegten Webpfad auf.</br>
Sie werden nun nach einer Administratoren Email Addresse und einem Passwort gefragt. 
Wenn Sie diese eingegeben haben können Sie entscheiden, ob das Liga System mit Demo Daten gefüllt werden soll. Hierzu wählen Sie die Option `Demo-Daten mit erstellen?` aus. (Diese Option ist empfehlenswert, um eine Testumgebung aufzusetzen und sich zunächst zu orientieren. Für eine aktive Umgebung ist diese Option <b>NICHT</b> zu empfehlen.)
Schließen Sie den Vorgang anschließend mit einem Klick auf `Datenbank anlegen?` ab. Die benötigten Datenbank Einträge werden hierdurch durchgeführt und Sie können sich anschließend mit den vergebenen Administrator Zugangsdaten in das Liga System einloggen.