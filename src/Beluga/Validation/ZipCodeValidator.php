<?php
/**
 * In this file the class {@see \Beluga\Validation\ZipCodeValidator} is defined.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga\Validation
 * @since          2016-08-25
 * @version        0.1.0
 */


namespace Beluga\Validation;
use Beluga\TypeTool;


/**
 * This is a ZIP code (postal code) validator.
 *
 * Initially known postal codes are defined for countries AT, CH, CZ, DE, DK, IE, LI, LU, NL, SK.
 *
 * If you want more/other you have to edit .data/zip-codes/txt/downloader.php to say
 * what counties are required. (All documented inside this file) After editing you have to execute this file.
 * It loads the *.zip archives for required languages, extracts the contained *.txt files and deletes the *.zip files.
 *
 * After its done, you have to run .data/zip-codes/sqlite/converter.php to generate the required
 * *.sqlite3 database files (one for each country)
 *
 * If no usable database exists the validator will only do a simple check (3-6 numbers)
 *
 * @since v0.1
 * @property-read string $country The 2 letter iso language code (e.g: 'DE', 'AT', ...)
 */
class ZipCodeValidator extends Validator
{


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new instance.
    *
    * Usable options are:
    *
    * - <b>Country</b>: The 2 letter ISO country code of the coutry where the postal codes are required for (Default=DE)
    * - <b>MinLength</b>: The min. required postal code length. (Default=3)
    * - <b>MaxLength</b>: The min. accepted postal code length. (Default=6)
    *
    * and also the base options:
    *
    * - <b>AllowEmpty</b> (boolean) Are emty values allowed for a valid request? (default=FALSE)
    * - <b>Required</b> (boolean) Defines if the associated value must be defined, to say a valid request exists.
    *   (default=TRUE)
    * - <b>DisplayName</b> (string|null) The (localized) display name of the field.
    * - <b>RequiredValue</b> If the field value can only use one single value to say its a usable request,
    *   you can define here the required string value. (default=undefined)
    * - <b>DefaultValue</b> This value is used if there is no require for the field to be defined for a
    *   request, and the field does not exist. (default=undefined)
    *
    * @param  array   $options  All validator options
    */
   public function __construct( array $options = [] )
   {

      $options[ 'Country' ] = isset( $options[ 'Country' ] ) ? \strtoupper( $options[ 'Country' ] ) : 'DE';
      $min                  = 3;
      $max                  = 6;

      switch ( $options[ 'Country' ] )
      {
         case 'IE':                                             $min = 3; $max = 3; break;
         case 'AT': case 'CH': case 'DK': case 'LI': case 'NL': $min = 4; $max = 4; break;
         case 'CZ': case 'LU':                                  $min = 6; $max = 6; break;
         case 'DE':                                             $min = 5; $max = 5; break;
         case 'SK':                                             $min = 5; $max = 6; break;
         default:                                                                   break;
      }

      static::setIntegerOption( 'MinLength',       $options, $min );
      static::setIntegerOption( 'MaxLength',       $options, $max );
      static::setStringOption ( 'Regex',           $options, '~^\d{' . $min . ',' . $max . '}$~' );

      $requireDbFile  = __DIR__ . '/.data/zip-codes/sqlite/' . $options[ 'Country' ] . '.sqlite3';

      if ( \file_exists( $requireDbFile ) )
      {
         $options[ 'DbFile' ] = $requireDbFile;
      }

      $options[ 'Result' ] = null;

      parent::__construct( $options );

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Sets a option value.
    *
    * @param  string $optionName The name of the option to set the value at.
    * @param  mixed  $value      The new value. (the value type depends to the option)
    * @return \Beluga\Validation\ZipCodeValidator
    */
   public function setOption( $optionName, $value )
   {

      switch ( $optionName )
      {

         case 'MinLength': return $this->setMinLength( $value );
         case 'MaxLength': return $this->setMaxLength( $value );
         case 'Country'  : return $this->setCountry( $value );
         default         : parent::setOption( $optionName, $value );
                           return $this;

      }

   }

   /**
    * Set the minimal required length.
    *
    * @param  integer $value
    * @return \Beluga\Validation\ZipCodeValidator
    */
   public function setMinLength( $value )
      : ZipCodeValidator
   {

      if ( \is_integer( $value ) && $value <= 0 )
      {
         $value = null;
      }

      $this->__setIntegerOption( 'MinLength', $value, 3 );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Set the max allowed length.
    *
    * @param  integer $value
    * @return \Beluga\Validation\ZipCodeValidator
    */
   public function setMaxLength( $value )
      : ZipCodeValidator
   {

      if ( \is_integer( $value ) && $value <= 0 )
      {
         $value = null;
      }

      $this->__setIntegerOption( 'MaxLength', $value, 6 );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Set the 2 letter ISO country code of the country where the postal codes are required for
    *
    * @param  string $value
    * @return \Beluga\Validation\ZipCodeValidator
    */
   public function setCountry( string $value )
      : ZipCodeValidator
   {

      if ( \is_string( $value ) && \strlen( $value ) == 2 && preg_match( '~^[A-Z]{2}$~', $value ) )
      {
         $this->_options[ 'Country' ] = $value;
         $dbFile = __DIR__ . '/.data/zip-codes/sqlite/' . $this->_options[ 'Country' ] . '.sqlite3';
         if ( \file_exists( $dbFile ) )
         {
            $this->_options[ 'DbFile' ] = $dbFile;
         }
         else
         {
            unset( $this->_options[ 'DbFile' ] );
         }
         $min = 3;
         $max = 6;
         switch ( $this->_options[ 'Country' ] )
         {
            case 'IE':                                             $min = 3; $max = 3; break;
            case 'AT': case 'CH': case 'DK': case 'LI': case 'NL': $min = 4; $max = 4; break;
            case 'CZ': case 'LU':                                  $min = 6; $max = 6; break;
            case 'DE':                                             $min = 5; $max = 5; break;
            case 'SK':                                             $min = 5; $max = 6; break;
            default:                                                                   break;
         }
         $this->_options[ 'MinLength' ] = $min;
         $this->_options[ 'MaxLength' ] = $max;
         $this->_options[ 'Regex'     ] = '~^\d{' . $min . ',' . $max . '}$~';
         $this->LastResult              = null;
         $this->_options[ 'Result'    ] = null;
      }

      return $this;

   }

   /**
    * Sets, if empty values are allowed for a valid request?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\ZipCodeValidator
    */
   public function setAllowEmpty( bool $value )
   {

      parent::setAllowEmpty( $value );

      return $this;

   }

   /**
    * Sets, if the associated value must be defined, to say a valid request exists.
    *
    * @param  boolean $value
    * @return \Beluga\Validation\ZipCodeValidator
    */
   public function setRequired( bool $value )
   {

      parent::setRequired( $value );

      return $this;

   }

   /**
    * This value is used if there is no require for the field to be defined for a request, and the field does not
    * exist.
    *
    * @param  string $value
    * @return \Beluga\Validation\ZipCodeValidator
    */
   public function setDefaultValue( $value )
   {

      parent::setDefaultValue( $value );

      return $this;

   }

   /**
    * If the field value can only use one single value to say its a usable request, you can define here the required
    * string value.
    *
    * @param  string $value
    * @return \Beluga\Validation\ZipCodeValidator
    */
   public function setRequiredValue( $value )
   {

      parent::setRequiredValue( $value );

      return $this;

   }

   /**
    * Sets the (localized) display name of the field.
    *
    * @param  string $value
    * @return \Beluga\Validation\ZipCodeValidator
    */
   public function setDisplayName( string $value )
   {

      parent::setDisplayName( $value );

      return $this;

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = =">

   protected function __validate()
   {

      $this->_message = '';

      if ( ! TypeTool::IsStringConvertible( $this->_value, $resultString ) )
      {
         $this->_message = \sprintf(
            static::$errors[ 6 ],
            $this->_options[ 'DisplayName' ]
         );
         return $this->__setLastResult( false );
      }

      $this->_options[ 'Result' ] = $resultString;

      if ( isset( $this->_options[ 'MinLength' ] ) &&
           \Beluga\strLen( $this->_options[ 'Result' ] ) < $this->_options[ 'MinLength' ] )
      {
         $this->_message = \sprintf(
            static::$errors[ 4 ],
            $this->_options[ 'DisplayName' ],
            $this->_options[ 'MinLength' ]
         );
         return $this->__setLastResult( false );
      }

      if ( isset( $this->_options[ 'MaxLength' ] ) &&
           \Beluga\strLen( $this->_options[ 'Result' ] ) > $this->_options[ 'MaxLength' ] )
      {
         $this->_message = \sprintf(
            static::$errors[ 5 ],
            $this->_options[ 'DisplayName' ],
            $this->_options[ 'MaxLength' ]
         );
         return $this->__setLastResult( false );
      }

      if ( isset( $this->_options[ 'Regex' ] ) &&
           ! \preg_match( $this->_options[ 'Regex' ], $this->_options[ 'Result' ] ) )
      {
         // 31 = The %s value uses an invalid format!
         $this->_message = \sprintf(
            static::$errors[ 9 ],
            $this->_options[ 'DisplayName' ]
         );
         return $this->__setLastResult( false );
      }

      if ( ! isset( $this->_options[ 'DbFile' ] ) )
      {

         // No country depending postal code database => only the default regex check is used
         return $this->__setLastResult( true );

      }

      $pdo = new \PDO( 'sqlite:' . $this->_options[ 'DbFile' ] );

      $sql = '
         SELECT
               COUNT(*) as cnt
            FROM
               zipcodes
            WHERE
               postal_code = $1';

      $stmt = $pdo->prepare( $sql );
      $stmt->execute( [ $this->_options[ 'Result' ] ] );

      $val  = $stmt->fetchColumn( 0 );

      $stmt = null;
      $pdo  = null;

      if ( \is_null( $val ) || \intval( $val ) < 1 )
      {
         // 200 = The %s value defines an unknown postal code for country %s!
         $this->_message = \sprintf(
            static::$errors[ 55 ],
            $this->_options[ 'DisplayName' ],
            $this->_options[ 'Country' ]
         );
         return $this->__setLastResult( false );
      }

      return $this->__setLastResult( true );

   }

   // </editor-fold>


}

