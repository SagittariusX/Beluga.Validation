<?php

// Call this file to download the country depending ZIP code of youre choise.
// see the list at http://download.geonames.org/export/zip/

include __DIR__ . '/../../../../../../vendor/autoload.php';

// Insert here youre required country codes (see list at link above)
$cnt = [
   'AT', 'CH', 'CZ', 'DE', 'DK', 'IE', 'LI', 'LU', 'NL', 'SK'
];




//
// From here there is no need to change anything!
//

$farUrl   = 'http://download.geonames.org/export/zip/%s.zip';

foreach ( $cnt as $country )
{
   echo "\n- Downloading country '", $country, "' ...";
   $zipFile = __DIR__ . '/' . $country . '.zip';
   file_put_contents( $zipFile, file_get_contents( sprintf( $farUrl, $country ) ) );
   echo ' (', number_format( filesize( $zipFile ) ), ' bytes)';
   echo "\n   Extract data ...";
   \Beluga\IO\File::UnZip( $zipFile, __DIR__, false );
   echo ' (', number_format( filesize( __DIR__ . '/' . $country . '.txt' ) ), ' bytes extracted)';
   unlink( $zipFile );
   sleep( 1 );
}

echo "\nDONE!";
