<?php


return [

   // Main errors of the abstract Validator class
   0   => 'KEIN REQUEST: Der benötigte %s Wert "%s" von einem %s Request ist nicht definiert.',
   1   => 'Das %s Feld darf nicht leer sein!',
   2   => 'KEIN REQUEST: Der benötigte %s Wert "%s" von einem %s Request hat nicht den benötigten Wert.',

   // DateValidator + DateTimeValidator + …more
   3  => 'Kein gültiger %s Wert!',
   4  => 'Der Wert ist kleiner als erlaubt! (Kleinst mögl. Wert ist %s)',
   5  => 'Der Wert ist größer als erlaubt! (Größt mögl. Wert ist %s)',
   6  => 'Der %s Wert ist keine gültige Zeichenkette!',
   7  => 'Der %s Wert ist kürzer als erlaubt! (Erlaubte Minimallänge ist %d)',
   8  => 'Der %s Wert ist länger als erlaubt! (Erlaubte Maximallänge ist %d)',
   9  => 'Der %s Wert hat ein ungültiges Format!',
   10 => 'Der %s Wert ist falsch/ungültig!',

   // DateValidator
   11 => 'Datum',

   // DateTimeValidator
   12 => 'Datum+Zeit',

   // MailAddressValidator
   13 => 'E-Mail Adresse',
   14 => 'E-Mail Adresse ohne gültige Domain!',
   15 => 'Reservierte lokale E-Mail Adress Domain!',
   16 => 'Reservierte E-Mail Adress Domain!',
   17 => 'E-Mail Adresse nutzt geographische TLD!',
   18 => 'Nicht erlaubte internationale E-Mail Adress Domain!',
   19 => 'E-Mail Adresse ohne TLD!',
   20 => 'E-Mail Adresse mit unbekannter TLD!',
   21 => 'E-Mail Adress Domain nutzt dynamischen DNS Service!',
   22 => 'E-Mail Adresse mit verbotener TLD!',
   23 => 'E-Mail Adresse mit verbotener Domain!',
   24 => 'E-Mail Adresse mit ungültiger Domain! (Kein MX host)',

   // UrlValidator
   25 => 'Webadresse',
   26 => 'Ungültige Webadresse! Kein gültiges Schema/Protokoll.',
   27 => 'Ungültige Webadresse!',
   28 => 'Ungültige Webadress-Format! Das Schema "%s" wird nicht unterstützt.',
   29 => 'Ungültige Webadresse! Unsichere Login-Informationen.',
   30 => 'Webadressen die IP-Adressen nutzen werden nicht akzepziert.',
   31 => 'Webadresse nutzt einen Protokollfremden Port!',
   32 => 'Webadresse verweist auf einen nicht akzeptierten KURZ-URL Dienst!',
   33 => 'Webadresse hat keine gültige TLD!',
   34 => 'Webadresse mit unbekannter TLD!',
   35 => 'Webadresse nutzt einen reservierten Hostnamen!',
   36 => 'Webadresse nutzt einen lokal reservierten Hostnamen!',
   37 => 'Webadresse verweist auf einen dynamischen DNS service!',
   38 => 'Webadresse enthält parameter!',
   39 => 'Webadresse enthält mehr als %d Parameter!',
   40 => 'Webadresse ebthält zu viele Parameter-Daten!',
   41 => 'Webadress Sicherheitsproblem! (Open Redirection Bug Nutzung)',
   42 => 'Ungültige Webadresse (verbotene TLD)!',
   43 => 'Ungültige Webadresse (verbotene Domain)!',

   // TextValidator
   44 => 'Der %s Wert enthält Zeilenumbrüche.',
   45 => 'Der %s Wert enthält %d Zeilenumbrüche. (Erlaubt sind max. %d!)',
   46 => 'Der %s Wert enthält HTML Formatierung.',
   47 => 'Der %s Wert enthält URLs (Webadressen).',
   48 => 'Der %s Wert enthält mehr als %d URLs (Webadressen)!',
   49 => 'Der %s Wert enthält E-Mail Adressen.',
   50 => 'Der %s Wert enthält Spam Inhalt.',
   51 => 'Der %s Wert enthält Spam Inhalt. (Entitäten)',
   52 => 'Der %s Wert enthält Inhalt der wie Spam aussieht.',
   53 => 'Der %s Wert enthält kodierten Inhalt',

   // IntegerValidator
   54 => 'Ganzzahl',

   // ZipCodeValidator
   55 => 'Der %s Wert ist keine gültige Postleitzahl für %s!'

];

