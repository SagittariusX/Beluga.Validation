<?php

// Call this file to convert the downloaded country depending ZIP code text files to SQLite databases.
// It requires that you call .data/zip-codes/txt/downloader.php before, to download the required files.

include __DIR__ . '/../../../../../../vendor/autoload.php';


$countries = [];

$dp = dir( dirname( __DIR__ ) . '/txt' );
while ( false !== ( $entry = $dp->read() ) )
{

   if ( '.' === $entry || '..' === $entry )
   {
      continue;
   }

   if ( ! preg_match( '~^([A-Z]{2})\.txt$~', $entry, $m ) )
   {
      continue;
   }

   $countries[] = $m[ 1 ];

}
$dp->close();

if ( count( $countries ) < 1 )
{
   die( 'There are no country files defined inside .data/zip-codes/txt/' );
}


$sqlCreateTable = "
   CREATE TABLE zipcodes
   (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      postal_code VARCHAR(20) NOT NULL,
      place_name VARCHAR(180) NOT NULL,
      admin_name1 VARCHAR(100) NOT NULL, -- 1. order subdivision (state)
      admin_code1  VARCHAR(20), --1. order subdivision (state)
      admin_name2  VARCHAR(100), -- 2. order subdivision (county/province)
      admin_code2  VARCHAR(20), -- 2. order subdivision (county/province)
      admin_name3  VARCHAR(100), -- 3. order subdivision (community)
      admin_code3 VARCHAR(20), -- 3. order subdivision (community)
      latitude REAL, -- estimated latitude (wgs84)
      longitude REAL, -- estimated longitude (wgs84)
      accuracy INTEGER -- accuracy of lat/lng from 1=estimated to 6=centroid
   )
";
$sqlCreateIndex = "CREATE UNIQUE INDEX idx1 ON zipcodes (postal_code, place_name, admin_name1)";
$sqlInsert = '
   INSERT INTO zipcodes
   (
      postal_code, place_name, admin_name1, admin_code1, admin_name2, admin_code2, admin_name3,
      admin_code3, latitude, longitude, accuracy
   )
   VALUES(
      $1, $2, $3, $4, $5, $6, $7,
      $8, $9, $10, $11
   )
';

foreach ( $countries as $country )
{

   echo "\n- Creating the '", $country, "' country database...";
   $db = __DIR__ . '/' . $country . '.sqlite3';
   $pdo = new PDO( 'sqlite:' . $db );
   echo "\n  - Create the '", $country, "' country zip codes table...";
   $pdo->exec( $sqlCreateTable );
   $pdo->exec( $sqlCreateIndex );
   $todos  = 0;
   $cntAll = 0;
   $fp     = fopen( dirname( __DIR__ ) . '/txt/' . $country . '.txt', 'rb' );
   $pdo->beginTransaction();
   $stmt   = $pdo->prepare( $sqlInsert );
   $ln     = 1;

   while ( false !== ( $data = fgetcsv( $fp, 900, "\t" ) ) )
   {
      if ( 12 !== count( $data ) )
      {
         die( 'Invalid record at line ' . $ln . ' with ' . count( $data ) . ' elements.' );
      }
      $ln++;
      for ( $i = 1; $i < 12; $i++ )
      {
         $data[ $i ] = trim( $data[ $i ] );
      }
      $params = [
         $data[ 1 ], $data[ 2 ], $data[ 3 ],
         empty( $data[ 4 ] )  ? null : $data[ 4 ],
         empty( $data[ 5 ] )  ? null : $data[ 5 ],
         empty( $data[ 6 ] )  ? null : $data[ 6 ],
         empty( $data[ 7 ] )  ? null : $data[ 7 ],
         empty( $data[ 8 ] )  ? null : $data[ 8 ],
         empty( $data[ 9 ] )  ? null : doubleval( $data[ 9 ] ),
         empty( $data[ 10 ] ) ? null : doubleval( $data[ 10 ] ),
         empty( $data[ 11 ] ) ? null : intval( $data[ 11 ] )
      ];
      $stmt->execute( $params );
      $todos++;
      $cntAll++;
      if ( $todos > 50 )
      {
         $pdo->commit();
         $todos = 0;
         $pdo->beginTransaction();
      }
   }

   if ( $todos > 0 )
   {
      $pdo->commit();
   }

   echo ' (' . $cntAll . ' records inserted)';
   sleep( 1 );
   $stmt = null;
   $pdo  = null;

}

echo "\nDONE!";

