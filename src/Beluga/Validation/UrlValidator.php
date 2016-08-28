<?php
/**
 * In this file the class {@see \Beluga\Validation\UrlValidator} is defined.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga\Validation
 * @since          2016-08-25
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga\Validation;


use \Beluga\Web\Url;


/**
 * A validator for testing a URL.
 *
 * @property array   $AllowedSchemes     All currently allowed schemes (protocols) (default=http,https,ftp)
 * @property boolean $AcceptLoginData    Are user password plain login data allowed as a part of the URL? (false)
 * @property boolean $AcceptIpAddress    Are IP-Addresses allowed as domain part of the URL? (default=false)
 * @property boolean $RequireScheme      Is the scheme/protocol definition a required URL part? If not, the scheme
 *                                       defined by {@see \Beluga\Validation\UrlValidator::$DefaultScheme} is used.
 *                                       (default=false)
 * @property boolean $AcceptBadPort      Is a port allowed that does not match the default protocol depending port?
 *                                       (default=false)
 * @property boolean $AcceptUrlShortener Are hosts allowed, pointing to a URL shortener service? (default=false)
 * @property boolean $RequireTLD         Is it required that the URL uses a TLD by domain part of the URL (true)
 * @property boolean $RequireKnownTLD    Is it required that the URL uses a well known TLD by domain part of the URL
 *                                       (default=false)
 * @property boolean $AcceptReserved     Are reserved TLDs, Hosts or IPs allowed? (default=false)
 * @property boolean $AcceptLocal        Are local TLDs, Hosts or IPs allowed? (default=false)
 * @property boolean $AcceptDynDns       Are host from dynamic DNS services allowed (default=true)
 * @property boolean $AcceptQueryPart    The URL can also define a query part (default=true)
 * @property boolean $AcceptOpenRedirect Is it allowed that the URL possible uses a open redirection bug? (true)
 * @property integer $MaxQueryCount      How many GET query arguments are max. allowed (default=15)
 * @property integer $MaxQueryLength     How many chars are allowed to be used inside the query string? (default=100)
 * @property array   $TLDBlacklist       Here you can define a blacklist of TLD's. URLs using this TLDs will result
 *                                       in a validation error. (default=array())
 * @property array   $DomainBlacklist    A list of not allowed URL domains. (default=array())
 * @property string  $DefaultScheme      This scheme is used if none is defined and 'RequireScheme' is false ('http')
 * @since    v0.1
 */
class UrlValidator extends Validator
{


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Inits a new instance.
    *
    * Usable options are:
    *
    * - <b>AllowedSchemes</b>: All currently allowed schemes (protocols) (default=http,https,ftp)
    * - <b>AcceptLoginData</b>: Are user password plain logindata allowed as a part of the URL? (false)
    * - <b>AcceptIpAddress</b>: Are IP-Adresses allowed as domain part of the URL? (default=false)
    * - <b>RequireScheme</b>: Is the scheme/protocol definition a required URL part? If not, the scheme
    *   defined by {@see \Beluga\Validation\UrlValidator::$DefaultScheme} is used. (default=false)
    * - <b>AcceptBadPort</b>: Is a port allowed that does not match the default protocol depending port? (default=false)
    * - <b>AcceptUrlShortener</b>: Are hosts allowed, pointing to a URL shortener service? (default=false)
    * - <b>RequireTLD</b>: Is it required that the URL uses a TLD by domain part of the URL (true)
    * - <b>RequireKnownTLD</b>: Is it required that the URL uses a well known TLD by domain part of the URL
    *   (default=false)
    * - <b>AcceptReserved</b>: Are reserved TLDs, Hosts or IPs allowed? (default=false)
    * - <b>AcceptLocal</b>: Are local TLDs, Hosts or IPs allowed? (default=false)
    * - <b>AcceptDynDns</b>: Are host from dynamic DNS services allowed (default=true)
    * - <b>AcceptQueryPart</b>: The URL can also define a query part (default=true)
    * - <b>AcceptOpenRedirect</b>: Is it allowed that the URL posible uses a open redirection bug? (true)
    * - <b>MaxQueryCount</b>: How many GET query arguments are max. allowed (default=15)
    * - <b>MaxQueryLength</b>: How many chars are allowed to be used inside the query string? (default=100)
    * - <b>TLDBlacklist</b>: Here you can define a blacklist of TLD's.
    * - <b>DomainBlacklist</b>: A list of not allowed mail address domains.
    * - <b>DefaultScheme</b>: This schem is used if none is defined and 'RequireScheme' is false ('http')
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
   public function __construct( array $options = [ ] )
   {

      static::initTranslator();

      static::setArrayOption  ( 'AllowedSchemes',                  $options, [ 'http', 'https', 'ftp' ] );
      static::setBooleanOption( 'AcceptLoginData',                 $options, false );
      static::setBooleanOption( 'AcceptIpAddress',                 $options, true );
      static::setBooleanOption( 'RequireScheme',                   $options, false );
      static::setBooleanOption( 'AcceptBadPort',                   $options, false );
      static::setBooleanOption( 'AcceptUrlShortener',              $options, false );
      static::setBooleanOption( 'RequireTLD',                      $options, true );
      static::setBooleanOption( 'RequireKnownTLD',                 $options, false );
      static::setBooleanOption( 'AcceptReserved',                  $options, false );
      static::setBooleanOption( 'AcceptLocal',                     $options, false );
      static::setBooleanOption( 'AcceptDynDns',                    $options, true );
      static::setBooleanOption( 'AcceptQueryPart',                 $options, true );
      static::setBooleanOption( 'AcceptOpenRedirect',              $options, true );
      static::setIntegerOption( 'MaxQueryCount',                   $options, 15 );
      static::setIntegerOption( 'MaxQueryLength',                  $options, 100 );
      static::setArrayOption  ( 'TLDBlacklist',                    $options );
      static::setArrayOption  ( 'DomainBlacklist',                 $options );
      static::setStringOption ( 'DefaultScheme',                   $options, 'http' );
      static::setStringOption ( 'DisplayName',                     $options, static::$errors[ 25 ] );

      $options[ 'Result' ] = null;

      parent::__construct( $options );

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Sets a value of a option.
    *
    * Usable options are:
    *
    * - <b>AllowedSchemes</b>: All currently allowed schemes (protocols) (default=http,https,ftp)
    * - <b>AcceptLoginData</b>: Are user password plain login data allowed as a part of the URL? (false)
    * - <b>AcceptIpAddress</b>: Are IP-Addresses allowed as domain part of the URL? (default=false)
    * - <b>RequireScheme</b>: Is the scheme/protocol definition a required URL part? If not, the scheme
    *   defined by {@see \Beluga\Validation\UrlValidator::$DefaultScheme} is used. (default=false)
    * - <b>AcceptBadPort</b>: Is a port allowed that does not match the default protocol depending port? (default=false)
    * - <b>AcceptUrlShortener</b>: Are hosts allowed, pointing to a URL shortener service? (default=false)
    * - <b>RequireTLD</b>: Is it required that the URL uses a TLD by domain part of the URL (true)
    * - <b>RequireKnownTLD</b>: Is it required that the URL uses a well known TLD by domain part of the URL
    *   (default=false)
    * - <b>AcceptReserved</b>: Are reserved TLDs, Hosts or IPs allowed? (default=false)
    * - <b>AcceptLocal</b>: Are local TLDs, Hosts or IPs allowed? (default=false)
    * - <b>AcceptDynDns</b>: Are host from dynamic DNS services allowed (default=true)
    * - <b>AcceptQueryPart</b>: The URL can also define a query part (default=true)
    * - <b>AcceptOpenRedirect</b>: Is it allowed that the URL possible uses a open redirection bug? (true)
    * - <b>MaxQueryCount</b>: How many GET query arguments are max. allowed (default=15)
    * - <b>MaxQueryLength</b>: How many chars are allowed to be used inside the query string? (default=100)
    * - <b>TLDBlacklist</b>: Here you can define a blacklist of TLD's.
    * - <b>DomainBlacklist</b>: A list of not allowed mail address domains.
    * - <b>DefaultScheme</b>: This scheme is used if none is defined and 'RequireScheme' is false ('http')
    *
    * and also the base options:
    *
    * - <b>AllowEmpty</b> (boolean) Are empty values allowed for a valid request? (default=FALSE)
    * - <b>Required</b> (boolean) Defines if the associated value must be defined, to say a valid request exists.
    *   (default=TRUE)
    * - <b>DisplayName</b> (string|null) The (localized) display name of the field.
    * - <b>RequiredValue</b> If the field value can only use one single value to say its a usable request,
    *   you can define here the required string value. (default=undefined)
    * - <b>DefaultValue</b> This value is used if there is no require for the field to be defined for a
    *   request, and the field does not exist. (default=undefined)
    *
    * @param string $optionName The name of the option
    * @param mixed  $value THe value to set. (The type is depending to the option it self)
    * @return \Beluga\Validation\UrlValidator
    */
   public function setOption( string $optionName, $value )
   {

      $tmp = null;

      switch ( $optionName )
      {

         case 'AcceptLoginData':     return $this->setAcceptLoginData( $value );
         case 'AcceptIpAddress':     return $this->setAcceptIpAddress( $value );
         case 'RequireScheme':       return $this->setRequireScheme( $value );
         case 'AcceptBadPort':       return $this->setAcceptBadPort( $value );
         case 'AcceptUrlShortener':  return $this->setAcceptUrlShortener( $value );
         case 'RequireKnownTLD':     return $this->setRequireKnownTLD( $value );
         case 'AcceptReserved':      return $this->setAcceptReserved( $value );
         case 'AcceptLocal':         return $this->setAcceptLocal( $value );
         case 'RequireTLD':          return $this->setRequireTLD( $value );
         case 'AcceptDynDns':        return $this->setAcceptDynDns( $value );
         case 'AcceptQueryPart':     return $this->setAcceptQueryPart( $value );
         case 'AcceptOpenRedirect':  return $this->setAcceptOpenRedirect( $value );
         case 'TLDBlacklist':        return $this->setTLDBlacklist( $value );
         case 'DomainBlacklist':     return $this->setDomainBlacklist( $value );
         case 'AllowedSchemes':      return $this->setAllowedSchemes( $value );
         case 'MaxQueryCount':       return $this->setMaxQueryCount( $value );
         case 'MaxQueryLength':      return $this->setMaxQueryLength( $value );
         case 'DefaultScheme':       return $this->setDefaultScheme( $value );
         default:                    parent::setOption( $optionName, $value );
                                     return $this;

      }

   }

   /**
    * Sets, if user+password plain login data are allowed as a part of the URL?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setAcceptLoginData( bool $value )
      : UrlValidator
   {

      $this->__setBooleanOption( 'AcceptLoginData', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if IP-Addresses allowed as domain part of the URL?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setAcceptIpAddress( bool $value )
      : UrlValidator
   {

      $this->__setBooleanOption( 'AcceptIpAddress', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if the scheme/protocol definition a required URL part? If not, the scheme defined by
    * {@see \Beluga\Validation\Url::$DefaultScheme} is used.
    *
    * @param  boolean $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setRequireScheme( bool $value )
      : UrlValidator
   {

      $this->__setBooleanOption( 'RequireScheme', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if a port is allowed that does not match the default protocol depending port?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setAcceptBadPort( bool $value )
      : UrlValidator
   {

      $this->__setBooleanOption( 'AcceptBadPort', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if hosts are allowed, pointing to a URL shortener service?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setAcceptUrlShortener( bool $value )
      : UrlValidator
   {

      $this->__setBooleanOption( 'AcceptUrlShortener', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if it's required that the URL uses a well known TLD by domain part of the URL
    *
    * @param  boolean $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setRequireKnownTLD( bool $value )
      : UrlValidator
   {

      $this->__setBooleanOption( 'RequireKnownTLD', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if reserved TLDs, Hosts or IPs are allowed.
    *
    * @param  boolean $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setAcceptReserved( bool $value )
      : UrlValidator
   {

      $this->__setBooleanOption( 'AcceptReserved', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if local TLDs, Hosts or IPs are allowed.
    *
    * @param  boolean $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setAcceptLocal( bool $value )
      : UrlValidator
   {

      $this->__setBooleanOption( 'AcceptLocal', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if it's required that the URL uses a TLD.
    *
    * @param  boolean $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setRequireTLD( bool $value )
      : UrlValidator
   {

      $this->__setBooleanOption( 'RequireTLD', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if hosts from dynamic DNS services are allowed.
    *
    * @param  boolean $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setAcceptDynDns( bool $value )
      : UrlValidator
   {

      $this->__setBooleanOption( 'AcceptDynDns', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if the URL can also define a query part or not.
    *
    * @param  boolean $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setAcceptQueryPart( bool $value )
      : UrlValidator
   {

      $this->__setBooleanOption( 'AcceptQueryPart', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if it's allowed that the URL possible uses a open redirection bug.
    *
    * @param  boolean $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setAcceptOpenRedirect( bool $value )
      : UrlValidator
   {

      $this->__setBooleanOption( 'AcceptOpenRedirect', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Here you can define a blacklist of TLD's.
    *
    * @param  array $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setTLDBlacklist( array $value = [] )
      : UrlValidator
   {

      $this->__setArrayOption( 'TLDBlacklist', $value );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets a list of not allowed domains.
    *
    * @param  array $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setDomainBlacklist( array $value )
      : UrlValidator
   {

      $this->__setArrayOption( 'DomainBlacklist', $value );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets all currently allowed schemes (protocols) (default=http,https,ftp)
    *
    * @param  array|string $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setAllowedSchemes( $value )
      : UrlValidator
   {

      if ( \is_array( $value ) && count( $value ) > 0 )
      {
         $this->_options[ 'AllowedSchemes' ] = $value;
      }
      else if ( \is_null( $value ) )
      {
         $this->_options[ 'AllowedSchemes' ] = [ 'http', 'https', 'ftp' ];
      }
      else if ( \is_string( $value ) && ! empty( $value ) )
      {
         $this->_options[ 'AllowedSchemes' ] = \explode( ',', $value );
      }

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, how many GET query arguments are max. allowed.
    *
    * @param  integer $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setMaxQueryCount( int $value )
      : UrlValidator
   {

      $this->__setIntegerOption( 'MaxQueryCount', $value, 15 );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, how many chars are allowed to be used inside the query string.
    *
    * @param  int $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setMaxQueryLength( int $value )
      : UrlValidator
   {

      $this->__setIntegerOption( 'MaxQueryLength', $value, 100 );

      $this->LastResult = null;

      return $this;

   }

   /**
    * This scheme is used if none is defined and 'RequireScheme' is FALSE.
    *
    * @param  string $value
    * @return \Beluga\Validation\UrlValidator
    */
   public function setDefaultScheme( string $value )
      : UrlValidator
   {

      $this->__setStringOption( 'DefaultScheme', $value, 'http' );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if empty values are allowed for a valid request?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\UrlValidator
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
    * @return \Beluga\Validation\UrlValidator
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
    * @return \Beluga\Validation\UrlValidator
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
    * @return \Beluga\Validation\UrlValidator
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
    * @return \Beluga\Validation\UrlValidator
    */
   public function setDisplayName( string $value )
   {

      parent::setDisplayName( $value );

      return $this;

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * @return boolean
    */
   protected function __validate()
   {

      $this->_message = '';

      if ( ! \preg_match( '~^[A-Za-z]{3,7}:~', $this->_value ) )
      {
         if ( $this->_options[ 'RequireScheme' ] )
         {
            // Not a valid web address! Missing a scheme/protocol.
            $this->_message = static::$errors[ 26 ];
            return $this->__setLastResult( false );
         }
         if ( ! empty( $this->_options[ 'DefaultScheme' ] ) )
         {
            Url::$fallbackScheme = $this->_options[ 'DefaultScheme' ];
         }
      }

      if ( false === ( $url = Url::Parse( $this->_value ) ) )
      {
         // Not a valid web address!
         $this->_message = static::$errors[ 27 ];
         return $this->__setLastResult( false );
      }

      if ( ! \in_array( $url->Scheme, $this->_options[ 'AllowedSchemes' ] ) )
      {
         // Invalid web address format! Scheme "%s" is not supported.
         $this->_message = \sprintf(
            static::$errors[ 28 ],
            $url->Scheme
         );
         return $this->__setLastResult( false );
      }

      if ( ! $this->_options[ 'AcceptLoginData' ] && $url->hasLoginData() )
      {
         // Invalid URL! Insecure auth/login information.
         $this->_message = static::$errors[ 29 ];
         return $this->__setLastResult( false );
      }

      if ( ! $this->_options[ 'AcceptIpAddress' ] && $url->useIpAddress() )
      {
         // Web addresses with IP address are not allowed.
         $this->_message = static::$errors[ 30 ];
         return $this->__setLastResult( false );
      }

      if ( ! $this->_options[ 'AcceptBadPort' ] && ! $url->useAssociatedPort() )
      {
         // Web address uses a non standard port!
         $this->_message = static::$errors[ 31 ];
         return $this->__setLastResult( false );
      }

      if ( ! $this->_options[ 'AcceptUrlShortener' ] && $url->isUrlShortenerAddress() )
      {
         // Web address uses a not accepted url shortener service!
         $this->_message = static::$errors[ 32 ];
         return $this->__setLastResult( false );
      }

      $domain = $url->Domain;

      if ( $this->_options[ 'RequireTLD' ] )
      {

         if ( ! $domain->HasTLD )
         {
            // Invalid web address without a valid TLD!
            $this->_message = static::$errors[ 33 ];
            return $this->__setLastResult( false );
         }

         if ( $this->_options[ 'RequireKnownTLD' ] && ! $domain->HasKnownTLD )
         {
            // Web address without a  known TLD!
            $this->_message = static::$errors[ 34 ];
            return $this->__setLastResult( false );
         }

      }
      else if ( $this->_options[ 'RequireKnownTLD' ] )
      {

         if ( ! $domain->HasTLD )
         {
            // Invalid web address without a valid TLD!
            $this->_message = static::$errors[ 33 ];
            return $this->__setLastResult( false );
         }

         if ( ! $domain->HasKnownTLD )
         {
            // Web address without a  known TLD!
            $this->_message = static::$errors[ 34 ];
            return $this->__setLastResult( false );
         }

      }

      if ( ! $this->_options[ 'AcceptReserved' ] && $domain->IsReserved )
      {
         // Web address uses a reserved host (part)!
         $this->_message = static::$errors[ 35 ];
         return $this->__setLastResult( false );
      }

      if ( ! $this->_options[ 'AcceptLocal' ] && $domain->IsLocal )
      {
         // Web address uses a local host (part)!
         $this->_message = static::$errors[ 36 ];
         return $this->__setLastResult( false );
      }

      if ( ! $this->_options[ 'AcceptDynDns' ] && $domain->IsDynamic )
      {
         // Web address points to a dynamic DNS service!
         $this->_message = static::$errors[ 37 ];
         return $this->__setLastResult( false );
      }

      if ( ! $this->_options[ 'AcceptQueryPart' ] )
      {

         if ( ! empty( $url->QueryString ) )
         {
            // Web address contains some query parameters!
            $this->_message = static::$errors[ 38 ];
            return $this->__setLastResult( false );
         }

      }
      else
      {

         if ( $this->_options[ 'MaxQueryCount' ] > -1 &&
              \count( $url->Query ) > $this->_options[ 'MaxQueryCount' ] )
         {
            // Web address contains more than %d query parameters!
            $this->_message = \sprintf(
               static::$errors[ 39 ],
               $this->_options[ 'MaxQueryCount' ]
            );
            return $this->__setLastResult( false );
         }

         if ( $this->_options[ 'MaxQueryLength' ] > -1 &&
              \strlen( $url->QueryString ) > $this->_options[ 'MaxQueryLength' ] )
         {
            // Web address contains to much query data!
            $this->_message = static::$errors[ 40 ];
            return $this->__setLastResult( false );
         }
      }

      $resultPoints = null;
      if ( ! $this->_options[ 'AcceptOpenRedirect' ] && $url->isPossibleOpenRedirect( $resultPoints ) )
      {
         // Web address security issue! (open redirection bug usage)
         $this->_message = static::$errors[ 41 ];
         return $this->__setLastResult( false );
      }

      if ( $domain->HasTLD && \count( $this->_options[ 'TLDBlacklist' ] ) > 0 )
      {

         if ( \in_array( $domain->SLD->TLD->toString(), $this->_options[ 'TLDBlacklist' ] ) )
         {
            // Illegal web address (forbidden TLD part)!
            $this->_message = static::$errors[ 42 ];
            return $this->__setLastResult( false );
         }

      }

      if ( \count( $this->_options[ 'DomainBlacklist' ] ) > 0 )
      {

         if ( \in_array( $domain->SLD->toString(), $this->_options[ 'DomainBlacklist' ] ) ||
              \in_array( $domain->toString(), $this->_options[ 'DomainBlacklist' ] ))
         {
            // Illegal web address (forbidden domain part)!
            $this->_message = static::$errors[ 43 ];
            return $this->__setLastResult( false );
         }

      }

      $this->_value = (string) $url;

      return $this->__setLastResult( true );

   }

   // </editor-fold>


}

