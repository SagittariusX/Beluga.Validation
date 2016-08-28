<?php
/**
 * In this file the class {@see \Beluga\Validation\MailAddressValidator} is defined.
 *
 * @author         Michael Rushton <michael@squiloople.com>
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga\Validation
 * @since          2016-08-25
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga\Validation;


use \Beluga\Web\Domain;


/**
 * A validator for testing a mail address.
 *
 * A Email address must have a <b>local-part</b> and a <b>domain-part</b> separated by an <b>@at</b> symbol.
 *
 *
 * <h3>The local part</h3>
 *
 * The local-part must be either a <b>dot-atom</b> or a <b>quoted string</b>,
 *
 * <h4>dot-atom</h4>
 *
 * A dot-atom may only contain letters, numbers, dots, and the following characters:
 *
 * <code>! # $ % & ‘ * + – / = ? ^ _ ` { | } ~</code>.
 *
 * However, neither the first nor the last character can be a dot, and two or more consecutive dots are not allowed.
 * The maximum length of a dot-atom is 64 characters.
 *
 * <h4>quoted string</h4>
 *
 * A quoted string may only contain printable US-ASCII characters or the space character, all contained within
 * double quotes.
 *
 * Double quotes and backslashes are allowed only if part of a quoted-pair (escaped with a backslash).
 * A quoted string may be empty. The maximum length of a quoted string is 64 characters, not including the
 * enclosing double-quotes or the escaping backslash of a quoted-pair.
 *
 *
 * <h3>The domain part</h3>
 *
 * The domain must be either a <b>domain name</b> or a <b>domain literal</b>.
 *
 * <h4>domain name</h4>
 *
 * A domain name consists of 1 to 127 labels (not including the (empty) root domain), separated by dots,
 * each containing any combination of letters, numbers, or hyphens. However, neither the first nor the last
 * character can be a hyphen. The maximum length of a domain name and label is 253 and 63 characters respectively.
 *
 * <h4>domain literal</h4>
 *
 * A domain literal is one of an <b>IPv4 address</b>, an <b>IPv6 address</b>, or an <b>IPv4-mapped IPv6 address</b>.
 *
 * When used as a domain literal in an email address, the IP address must be contained within square brackets [],
 * and IPv6 or IPv4-mapped IPv6 addresses must be preceded by a unquoted "IPv6:".
 *
 * <h5>IPv4 address</h5>
 *
 * An IPv4 address consists of four groups, separated by dots, each containing a decimal value between 0 and 255.
 *
 * <h5>IPv6 address</h5>
 *
 * An IPv6 address consists of eight groups, separated by colons, each containing a hexadecimal value between
 * 0 and FFFF.
 *
 * One or more consecutive groups of 0 value can be represented as a double colon; however, this may only occur once.
 *
 * <h5>IPv4-mapped IPv6 address</h5>
 *
 * An IPv4-mapped IPv6 address is an IPv6 address with the final two groups represented as an IPv4 address.
 *
 *
 * <h3>Comments + Folding Whitespaces</h3>
 *
 * Comments and folding white spaces are also allowed in an email address:
 *
 * - before and/or after the local-part
 * - before and/or after the domain
 * - before and/or after any dot in a local-part and/or domain
 *
 * Folding white space may also appear in a quoted string and/or in comments, and comments may nest.
 * A comment is almost identical to a quoted string except that it is opened and closed with a left and right
 * parentheses respectively and that parentheses are only allowed as part of a quoted-pair (or as further comments),
 * whereas double quotes may appear freely.
 *
 * Folding white spaces are occurrences of the space and/or horizontal tab character preceded by, optionally,
 * zero or more spaces and/or horizontal tabs followed by a carriage return and line feed pair.
 *
 * An obsolete form of folding white space is also allowed which is a carriage return and line feed pair followed
 * by a space or horizontal tab character.
 *
 * Folding white spaces, where allowed, are optional and may occur repeatedly.
 *
 * @property boolean $allowQuotedString Is a quoted string local part allowed and a dot-atom not? (default=TRUE)
 * @property boolean $allowObsolete Allow a obsolete version of the local-part. A mixture of atoms and quoted
 *           strings, separated by dots, is also allowed. An obsolete quoted string allows any US-ASCII character
 *           when part of a quoted-pair, and any US-ASCII character except the null, horizontal tab, new line,
 *           carriage return, backslash, and double quote characters when not. An obsolete local-part may only be
 *           empty if it is a single quoted string. The maximum length of an obsolete local-part, not including the
 *           double quotes enclosing a quoted string or the escaping backslash of a quoted-pair, is 64 characters.
 *           (default=FALSE)
 * @property boolean $requireBasicDomainname A basic domain name is required? (dots are optional) (default=TRUE)
 * @property boolean $allowIPAddresses A domain literal domain is allowed? It means IP-Addresses (default=FALSE)
 * @property boolean $allowCommentsFoldingWhithespace Allow comments + folding whitespaces? (default=FALSE)
 * @property boolean $checkForMX Check if a usable MX DNS entry exists by used mail address <b>domain-part</b>.
 *           If you enable this, remember this function does always DNS communication for getting the required
 *           Informations. This can slow down your application.
 * @property boolean $allowLocal Allow known local domains or IPs as <b>domain-part</b>?
 *           e.g.: localhost, etc. (default=FALSE)
 * @property boolean $allowReserved Allow known reserved hosts like example.* or *.test? (default=FALSE)
 * @property boolean $allowGeographic Allow the use of known Geographic TLD's like 'berlin'? (default=TRUE)
 * @property boolean $allowLocalized Allow the use of known localized unicode TLDs? (default=FALSE)
 * @property boolean $requireTLD Is a tld definition required to be valid? (default=TRUE)
 * @property boolean $requireKnownTLD The TLD must be defined and a known TLD? (default=TRUE)
 * @property boolean $allowDynamic Allow mail addresses from dynamic dns services?
 * @property array   $tLDBlacklist Here you can define a blacklist of TLD's. Mail addresses using this TLDs will
 *           result in a validation error. (default=array())
 * @property array   $domainBlacklist A list of not allowed mail address domains. Mai addresses using this Domains
 *           will result in a validation error. (default=array())
 * @since    v0.1
 * @link     http://squiloople.com/2009/12/20/email-address-validation/#more-1 Thanks to michael from
 *           http://squiloople.com/ for the regex and some code and documentation!
 * @copyright © 2012 Michael Rushton
 * @copyright © 2016 SagittariusX
 */
class MailAddressValidator extends Validator
{


   // <editor-fold desc="// = = = =   C L A S S   C O N S T A N T S   = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The RFC 5321 compatible check.
    */
   const RFC_5321 = 5321;

   /**
    * The RFC 5322 compatible check.
    */
   const RFC_5322 = 5322;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new instance.
    *
    * Usable options are:
    *
    * - <b>AllowQuotedString</b>: Is a quoted string local part allowed and a dot-atom not? (default=TRUE)
    * - <b>AllowObsolete</b>: Allow a obsolete version of the local-part. (default=FALSE)
    * - <b>RequireBasicDomainname</b>:  A basic domain name is required? (dots are optional) (default=TRUE)
    * - <b>AllowIPAddresses</b>: A domain literal domain is allowed? It means IP-Adresses (default=FALSE)
    * - <b>AllowCommentsFoldingWhithespace</b>: Allow comments + folding whitespaces? (default=FALSE)
    * - <b>CheckForMX</b>: Check if a usable MX DNS entry exists by used mail address <b>domain-part</b>.
    *   If you enable this, remember this function does always DNS communication for getting the required
    *   Informations. This can slow down your application.
    * - <b>AllowLocal</b>: Allow known local domains or IPs? e.g.: localhost, etc. (default=FALSE)
    * - <b>AllowReserved</b>: Allow known reserved hosts like example.* or *.test? (default=FALSE)
    * - <b>AllowGeographic</b>: Allow the use of known Geographic TLD's like 'berlin'? (default=TRUE)
    * - <b>AllowLocalized</b>: Allow the use of known localized unicode TLDs? (default=FALSE)
    * - <b>RequireTLD</b>: Is a tld definition required to be valid? (default=TRUE)
    * - <b>RequireKnownTLD</b>: The TLD must be defined and a known TLD?
    * - <b>AllowDynamic</b>: Allow mail addresses from dynamic dns services?
    * - <b>TLDBlacklist</b>: Here you can define a blacklist of TLD's.
    * - <b>DomainBlacklist</b>: A list of not allowed mail address domains.
    *
    * @param  array   $options  All validator options
    * @param  integer $standard The address standard to use (see self::RFC_532* class constants)
    * @throws \Throwable        The exception is thrown if a unknown stantard is used.
    */
   public function __construct( array $options = [], $standard = null )
   {

      static::initTranslator();

      static::setBooleanOption( 'AllowQuotedString',               $options, true );
      static::setBooleanOption( 'AllowObsolete',                   $options );
      static::setBooleanOption( 'RequireBasicDomainname',          $options, true );
      static::setBooleanOption( 'AllowIPAddresses',                $options );
      static::setBooleanOption( 'AllowCommentsFoldingWhithespace', $options );
      static::setBooleanOption( 'CheckForMX',                      $options );
      static::setBooleanOption( 'AllowLocal',                      $options );
      static::setBooleanOption( 'AllowReserved',                   $options );
      static::setBooleanOption( 'AllowGeographic',                 $options, true );
      static::setBooleanOption( 'AllowLocalized',                  $options );
      static::setBooleanOption( 'AllowWildcardTLD',                $options );
      static::setBooleanOption( 'RequireTLD',                      $options, true );
      static::setBooleanOption( 'RequireKnownTLD',                 $options, true );
      static::setBooleanOption( 'AllowDynamic',                    $options );
      static::setArrayOption  ( 'TLDBlacklist',                    $options );
      static::setArrayOption  ( 'DomainBlacklist',                 $options );
      static::setStringOption ( 'DisplayName',                     $options, static::$errors[ 13 ] );

      parent::__construct( $options );

      // Set the relevant standard or throw an exception if an unknown is requested
      switch ( $standard )
      {

         case null:
            // Do nothing if no standard requested
            break;

         case self::RFC_5321:
            // Otherwise if RFC 5321 requested
            $this->setStandard5321();
            break;

         // Otherwise if RFC 5322 requested
         case self::RFC_5322:
            $this->setStandard5322();
            break;

         // Otherwise throw an exception
         default:
            throw new ValidationError( 'Validation', 'Unknown RFC standard for email address validation.' );

      }

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Validate the email address using a basic standard.
    *
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setStandardBasic()
      : MailAddressValidator
   {

      // A quoted string local part is not allowed
      $this->_options[ 'AllowQuotedString' ] = false;

      // An obsolete local part is not allowed
      $this->_options[ 'AllowObsolete' ] = false;

      // A basic domain name is required
      $this->_options[ 'RequireBasicDomainname' ] = true;

      // A domain literal domain is not allowed
      $this->_options[ 'AllowIPAddresses' ] = false;

      // Comments and folding white spaces are not allowed
      $this->_options[ 'AllowCommentsFoldingWhithespace' ] = false;

      $this->LastResult = null;

      // Return the \Beluga\Validation\MailAddress object
      return $this;

   }

   /**
    * Validate the email address using RFC 5321.
    *
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setStandard5321()
      : MailAddressValidator
   {

      // A quoted string local part is allowed
      $this->_options[ 'AllowQuotedString' ] = true;

      // An obsolete local part is not allowed
      $this->_options[ 'AllowObsolete' ] = false;

      // Only a basic domain name is not required
      $this->_options[ 'RequireBasicDomainname' ] = false;

      // A domain literal domain is allowed
      $this->_options[ 'AllowIPAddresses' ] = true;

      // Comments and folding white spaces are not allowed
      $this->_options[ 'AllowCommentsFoldingWhithespace' ] = false;

      $this->LastResult = null;

      // Return the \Beluga\Validation\MailAddress object
      return $this;

   }

   /**
    * Validate the email address using RFC 5322
    *
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setStandard5322()
      : MailAddressValidator
   {

      // A quoted string local part is disallowed
      $this->_options[ 'AllowQuotedString' ] = false;

      // An obsolete local part is allowed
      $this->_options[ 'AllowObsolete' ] = true;

      // Only a basic domain name is not required
      $this->_options[ 'RequireBasicDomainname' ] = false;

      // A domain literal domain is allowed
      $this->_options[ 'AllowIPAddresses' ] = true;

      // Comments and folding white spaces are allowed
      $this->_options[ 'AllowCommentsFoldingWhithespace' ] = true;

      $this->LastResult = null;

      // Return the \Beluga\Validation\MailAddress object
      return $this;

   }

   /**
    * Sets a value of a option.
    *
    * @param string $optionName The name of the option
    * @param mixed  $value THe value to set. (The type is depending to the option it self)
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setOption( string $optionName, $value )
   {

      switch ( $optionName )
      {

         case 'AllowQuotedString':               return $this->setAllowQuotedString( $value );
         case 'AllowObsolete':                   return $this->setAllowObsolete( $value );
         case 'AllowCommentsFoldingWhithespace': return $this->setAllowCommentsFoldingWhithespace( $value );
         case 'CheckForMX':                      return $this->setCheckForMX( $value );
         case 'RequireTLD':                      return $this->setRequireTLD( $value );
         case 'RequireKnownTLD':                 return $this->setRequireKnownTLD( $value );
         case 'AllowDynamic':                    return $this->setAllowDynamic( $value );
         case 'RequireBasicDomainname':          return $this->setRequireBasicDomainname( $value );
         case 'AllowIPAddresses':                return $this->setAllowIPAddresses( $value );
         case 'AllowLocal':                      return $this->setAllowLocal( $value );
         case 'AllowReserved':                   return $this->setAllowReserved( $value );
         case 'AllowGeographic':                 return $this->setAllowGeographic( $value );
         case 'AllowLocalized':                  return $this->setAllowLocalized( $value );
         case 'TLDBlacklist':                    return $this->setTLDBlacklist( $value );
         case 'DomainBlacklist':                 return $this->setDomainBlacklist( $value );
         default:                                parent::setOption( $optionName, $value );
                                                 return $this;

      }

   }

   /**
    * Is a quoted string local part allowed and a dot-atom not?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setAllowQuotedString( bool $value )
      : MailAddressValidator
   {

      $this->__setBooleanOption( 'AllowQuotedString', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Allow a obsolete version of the local-part. A mixture of atoms and quoted strings, separated by dots, is also
    * allowed. An obsolete quoted string allows any US-ASCII character when part of a quoted-pair, and any US-ASCII
    * character except the null, horizontal tab, new line, carriage return, backslash, and double quote characters
    * when not. An obsolete local-part may only be empty if it is a single quoted string. The maximum length of an
    * obsolete local-part, not including the double quotes enclosing a quoted string or the escaping backslash of a
    * quoted-pair, is 64 characters.
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setAllowObsolete( bool $value )
      : MailAddressValidator
   {

      $this->__setBooleanOption( 'AllowObsolete', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Allow comments + folding whitespaces?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setAllowCommentsFoldingWhithespace( bool $value )
      : MailAddressValidator
   {

      $this->__setBooleanOption( 'AllowCommentsFoldingWhithespace', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Check if a usable MX DNS entry exists by used mail address <b>domain-part</b>. If you enable this, remember this
    * function does always DNS communication for getting the required Informations. This can slow down your application.
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setCheckForMX( bool $value )
      : MailAddressValidator
   {

      $this->__setBooleanOption( 'CheckForMX', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Is a tld definition required to be valid?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setRequireTLD( bool $value )
      : MailAddressValidator
   {

      $this->__setBooleanOption( 'RequireTLD', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * The TLD must be defined and a known TLD?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setRequireKnownTLD( bool $value )
      : MailAddressValidator
   {

      $this->__setBooleanOption( '', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Allow mail addresses from dynamic dns services?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setAllowDynamic( bool $value )
      : MailAddressValidator
   {

      $this->__setBooleanOption( 'AllowDynamic', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * A basic domain name is required? (dots are optional)
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setRequireBasicDomainname( bool $value )
      : MailAddressValidator
   {

      $this->__setBooleanOption( 'RequireBasicDomainname', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * A domain literal domain is allowed? It means IP-Adresses
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setAllowIPAddresses( bool $value )
      : MailAddressValidator
   {

      $this->__setBooleanOption( 'AllowIPAddresses', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Allow known local domains or IPs as <b>domain-part</b>? e.g.: localhost, etc.
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setAllowLocal( bool $value )
      : MailAddressValidator
   {

      $this->__setBooleanOption( 'AllowLocal', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Allow known reserved hosts like example.* or *.test?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setAllowReserved( bool $value )
      : MailAddressValidator
   {

      $this->__setBooleanOption( 'AllowReserved', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Allow the use of known Geographic TLD's like 'berlin'?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setAllowGeographic( bool $value )
      : MailAddressValidator
   {

      $this->__setBooleanOption( 'AllowGeographic', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Allow the use of known localized unicode TLDs?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setAllowLocalized( bool $value )
      : MailAddressValidator
   {

      $this->__setBooleanOption( 'AllowLocalized', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Here you can define a blacklist of TLD's. Mail addresses using this TLDs will result in a validation error.
    *
    * @param  array $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setTLDBlacklist( array $value )
      : MailAddressValidator
   {

      $this->__setArrayOption( 'TLDBlacklist', $value );

      $this->LastResult = null;

      return $this;

   }

   /**
    * A list of not allowed mail address domains. Mail addresses using this Domains will result in a validation error.
    *
    * @param  array $value
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setDomainBlacklist( $value )
      : MailAddressValidator
   {

      $this->__setArrayOption( 'DomainBlacklist', $value );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if empty values are allowed for a valid request?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\MailAddressValidator
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
    * @return \Beluga\Validation\MailAddressValidator
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
    * @return \Beluga\Validation\MailAddressValidator
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
    * @return \Beluga\Validation\MailAddressValidator
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
    * @return \Beluga\Validation\MailAddressValidator
    */
   public function setDisplayName( string $value )
   {

      parent::setDisplayName( $value );

      return $this;

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R I V A T E   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Returns the regular expression element, for a dot atom local part.
    *
    * @return string
    */
   private function getDotAtomRegexElement()
      : string
   {

      return '([!#-\'*+\/-9=?^-~-]+)(?>\.(?1))*';

   }

   /**
    * Returns the regular expression element of a quoted string local part
    *
    * @return string
    */
   private function getQuotedStringRegexElement()
      : string
   {
      return '"(?>[ !#-\[\]-~]|\\\[ -~])*"';
   }

   /**
    * Returns the regular expression element for an obsolete local part
    *
    * @return string
    */
   private function getObsoleteRegexElementRegexElement()
      : string
   {

      return '([!#-\'*+\/-9=?^-~-]+|"(?>'
      . $this->getFWSRegexElement()
      . '(?>[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\xFF]))*'
      . $this->getFWSRegexElement()
      . '")(?>'
      . $this->getCFWSRegexElement()
      . '\.'
      . $this->getCFWSRegexElement()
      . '(?1))*';

   }

   /**
    * Returns the regular expression element for a domain name domain
    *
    * @return string
    */
   private function getDomainNameRegexElement()
      : string
   {

      // Return the basic domain name format if required
      if ( $this->_options[ 'RequireBasicDomainname' ] )
      {

         return '(?>' . $this->getDomainNameLengthLimitRegexElement()
         . '[a-z\d](?>[a-z\d-]*[a-z\d])?'
         . $this->getCFWSRegexElement()
         . '\.'
         . $this->getCFWSRegexElement()
         . '){1,126}[a-z]{2,6}';

      }

      // Otherwise return the full domain name format
      return $this->getDomainNameLengthLimitRegexElement()
      . '([a-z\d](?>[a-z\d-]*[a-z\d])?)(?>'
      . $this->getCFWSRegexElement()
      . '\.'
      . $this->getDomainNameLengthLimitRegexElement()
      . $this->getCFWSRegexElement()
      . '(?2)){0,126}';

   }

   /**
    * Returns the regular expression element of an IPv6 address
    *
    * @return string
    */
   private function getIPv6RegexElement()
      : string
   {

      return '([a-f\d]{1,4})(?>:(?3)){7}|(?!(?:.*[a-f\d][:\]]){8,})((?3)(?>:(?3)){0,6})?::(?4)?';

   }

   /**
    * Returns the regular expression element for an IPv4-mapped IPv6 address
    *
    * @return string
    */
   private function getIPv4MappedIPv6RegexElement()
      : string
   {

      return '(?3)(?>:(?3)){5}:|(?!(?:.*[a-f\d]:){6,})(?5)?::(?>((?3)(?>:(?3)){0,4}):)?';

   }

   /**
    * Returns the regular expression element of an IPv4 address
    *
    * @return string
    */
   private function getIPv4RegexElement()
      : string
   {

      return '(25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d)(?>\.(?6)){3}';

   }

   /**
    * Returns the regular expression element for a domain literal domain
    *
    * @return string
    */
   private function getDomainLiteralRegexElement()
      : string
   {

      return '\[(?:(?>IPv6:(?>'
      . $this->getIPv6RegexElement()
      . '))|(?>(?>IPv6:(?>'
      . $this->getIPv4MappedIPv6RegexElement()
      . '))?'
      . $this->getIPv4RegexElement()
      . '))\]';

   }

   /**
    * Returns either the regular expression element for folding white spaces or its backreference.
    *
    * @param  boolean $define
    * @return string
    */
   private function getFWSRegexElement( $define = false )
   {

      // Return the back reference if $define is set to FALSE otherwise return the regular expression
      if ( $this->_options[ 'AllowCommentsFoldingWhithespace' ] )
      {
         return ! $define ? '(?P>fws)' : '(?<fws>(?>(?>(?>\x0D\x0A)?[\t ])+|(?>[\t ]*\x0D\x0A)?[\t ]+)?)';
      }

      return false;

   }

   /**
    * Returns the regular expression element for comments
    *
    * @return string
    */
   private function getCommentsRegexElement()
      : string
   {

      return '(?<comment>\((?>'
           . $this->getFWSRegexElement()
           . '(?>[\x01-\x08\x0B\x0C\x0E-\'*-\[\]-\x7F]|\\\[\x00-\x7F]|(?P>comment)))*'
           . $this->getFWSRegexElement()
           . '\))';

   }

   /**
    * Returns either the regular expression element for comments and folding white spaces or its backreference
    *
    * @param  boolean $define
    * @return string
    */
   private function getCFWSRegexElement( $define = false )
      : string
   {

      // Return the back reference if $define is set to FALSE
      if ( $this->_options[ 'AllowCommentsFoldingWhithespace' ] && ! $define )
      {
         return '(?P>cfws)';
      }

      // Otherwise return the regular expression
      if ( $this->_options[ 'AllowCommentsFoldingWhithespace' ] )
      {

         return '(?<cfws>(?>(?>(?>'
         . $this->getFWSRegexElement( true )
         . $this->getCommentsRegexElement()
         . ')+'
         . $this->getFWSRegexElement()
         . ')|'
         . $this->getFWSRegexElement()
         . ')?)';

      }

      return '';

   }

   /**
    * Establish and return the valid format for the local part
    *
    * @return string
    */
   private function getLocalPartRegexElement()
      : string
   {

      // The local part may be obsolete if allowed
      if ( $this->_options[ 'AllowObsolete' ] )
      {
         return $this->getObsoleteRegexElementRegexElement();
      }

      // Otherwise the local part must be either a dot atom or a quoted string if the latter is allowed
      if ( $this->_options[ 'AllowQuotedString' ] )
      {
         return '(?>' . $this->getDotAtomRegexElement() . '|' . $this->getQuotedStringRegexElement() . ')';
      }

      // Otherwise the local part must be a dot atom
      return $this->getDotAtomRegexElement();

   }

   /**
    * Establish and return the valid format for the domain
    *
    * @return string
    */
   private function getDomainRegexElement()
      : string
   {

      // The domain must be either a domain name or a domain literal if the latter is allowed
      if ( $this->_options[ 'AllowDomainLiteral' ] )
      {
         return '(?>' . $this->getDomainNameRegexElement() . '|' . $this->getDomainLiteralRegexElement() . ')';
      }

      // Otherwise the domain must be a domain name
      return $this->getDomainNameRegexElement();

   }

   /**
    * Return the email address length limit
    *
    * @return string
    */
   private function getEmailAddressLengthLimit()
      : string
   {

      return '(?!(?>' . $this->getCFWSRegexElement() . '"?(?>\\\[ -~]|[^"])"?' . $this->getCFWSRegexElement() . '){255,})';

   }

   /**
    * Return the local part length limit
    *
    * @return string
    */
   private function getLocalPartLengthLimit()
      : string
   {

      return '(?!(?>' . $this->getCFWSRegexElement() . '"?(?>\\\[ -~]|[^"])"?' . $this->getCFWSRegexElement() . '){65,}@)';

   }

   /**
    * Establish and return the domain name length limit
    *
    * @return string
    */
   private function getDomainNameLengthLimitRegexElement()
      : string
   {

      return '(?!' . $this->getCFWSRegexElement() . '[a-z\d-]{64,})';

   }

   /**
    * Check to see if the domain can be resolved to MX RRs
    *
    * @param  array $domain
    * @return integer|boolean
    */
   private function verifyDomain( $domain ) : bool
   {

      return \checkdnsrr( \end( $domain ), 'MX' );

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * @return boolean
    */
   protected function __validate()
   {

      // Build the required regular expression
      // Thanks to http://squiloople.com/2009/12/20/email-address-validation/#more-1
      $regex =
         '/^'
         . $this->getEmailAddressLengthLimit()
         . $this->getLocalPartLengthLimit()
         . $this->getCFWSRegexElement()
         . $this->getLocalPartRegexElement()
         . $this->getCFWSRegexElement()
         . '@'
         . $this->getCFWSRegexElement()
         . $this->getDomainRegexElement()
         . $this->getCFWSRegexElement( true )
         . '$/isD';

      $this->_message = '';

      if ( ! \preg_match( $regex, $this->_value ) )
      {

         $this->_message = \sprintf(
            static::$errors[ 3 ],
            static::$errors[ 13 ]
         );

         return $this->__setLastResult( false );

      }

      list( , $domainPart ) = explode( '@', $this->_value, 2 );

      if ( false === ( $domain = Domain::Parse( $domainPart, $this->_options[ 'RequireKnownTLD' ] ) ) )
      {

         // Mail address without a valid domain part!
         $this->_message = static::$errors[ 14 ];

         return $this->__setLastResult( false );

      }

      if ( ! $this->_options[ 'AllowLocal' ] && $domain->IsLocal )
      {

         // Locally reserved mail address domain-part!
         $this->_message = static::$errors[ 15 ];

         return $this->__setLastResult( false );

      }

      if ( ! $this->_options[ 'AllowReserved' ] && $domain->IsReserved )
      {

         // Reserved mail address domain-part!
         $this->_message = static::$errors[ 16 ];

         return $this->__setLastResult( false );

      }

      if ( ! $this->_options[ 'AllowGeographic' ] && $domain->IsGeographic )
      {

         // Mail address, pointing to geographic TLDs!
         $this->_message = static::$errors[ 17 ];

         return $this->__setLastResult( false );

      }

      if ( ! $this->_options[ 'AllowLocalized' ] && $domain->IsLocalized )
      {

         // Localized unicode mail address domain parts!
         $this->_message = static::$errors[ 18 ];

         return $this->__setLastResult( false );

      }

      if ( $this->_options[ 'RequireTLD' ] && ! $domain->HasTLD )
      {

         // Domain part without TLD definition!
         $this->_message = static::$errors[ 19 ];

         return $this->__setLastResult( false );

      }

      if ( $this->_options[ 'RequireKnownTLD' ] && ! $domain->HasKnownTLD )
      {

         // Domain part with unknown TLD!
         $this->_message = static::$errors[ 20 ];

         return $this->__setLastResult( false );

      }

      if ( ! $this->_options[ 'AllowDynamic' ] && $domain->IsDynamic )
      {

         // Domain part is a known dynamic DNS service!
         $this->_message = static::$errors[ 21 ];

         return $this->__setLastResult( false );

      }

      if ( $domain->HasTLD && \count( $this->_options[ 'TLDBlacklist' ] ) > 0 )
      {

         if ( \in_array( $domain->SLD->TLD->toString(), $this->_options[ 'TLDBlacklist' ] ) )
         {

            // Mail address with forbidden TLD part!
            $this->_message = static::$errors[ 22 ];

            return $this->__setLastResult( false );

         }

      }

      if ( \count( $this->_options[ 'DomainBlacklist' ] ) > 0 )
      {

         if ( \in_array( $domain->SLD->toString(), $this->_options[ 'DomainBlacklist' ] ) ||
              \in_array( $domain->toString(), $this->_options[ 'DomainBlacklist' ] ))
         {

            // Mail address with forbidden domain part!
            $this->_message = static::$errors[ 23 ];

            return $this->__setLastResult( false );

         }

      }

      if ( $this->_options[ 'CheckForMX' ] )
      {

         if ( ! $this->verifyDomain( $domainPart ) )
         {

            // Mail address with invalid domain-part! (No MX host)
            $this->_message = static::$errors[ 24 ];

            return $this->__setLastResult( false );

         }

      }

      return $this->__setLastResult( true );

   }

   // </editor-fold>
   

}

