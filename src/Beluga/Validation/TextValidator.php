<?php
/**
 * In this file the class {@see \Beluga\Validation\TextValidator} is defined.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga\Validation
 * @since          2016-08-25
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga\Validation;


use \Beluga\Converters\HTML\IHtmlToX;
use \Beluga\TypeTool;
use \Beluga\Web\{MailAddress,Url};


/**
 * This class defines a validator that allows only valid integer values
 *
 * @property integer|NULL $MinLength      The min allowed length or NULL if no min length should be used (default=1)
 * @property integer|NULL $MaxLength      The max allowed length or NULL if no max should used (default=\PHP_INT_MAX)
 * @property boolean      $AllowUrls      Can the text contain URLs to be valid? (default=TRUE)
 * @property integer      $MaxUrlCount    The max. count of allowed unknown URLs inside the text to validate.
 *                                        The default value -1 means, no limit.
 * @property array        $HostWhiteList  Defining Host names, allowed to be used uncounted in URLs and mail addresses.
 * @property boolean      $AllowMailAddresses Can the text contain mail addresses to be valid? (default=TRUE)
 * @property boolean      $AllowNewLines  Can the text contain some line-breaks to be valid (default=TRUE)
 * @property integer      $MaxLineCount   How many lines can be defined by the value to check, to be valid?
 *                                        (Default = -1 means no limit => 0 is equal to 1 cause 0 lines have no sense)
 * @property boolean      $AllowHtml      Can the value to check contains some HTML content? (default=TRUE)
 * @property \Beluga\Converters\HTML\IHtmlToX $HtmlConverter If HTML is allowed here you can define the Converter
 * @property boolean      $CheckForSpam   Should the text be checked if it contains some spam? (default=FALSE)
 *                                        It checks for some buzzwords and also checks for some spam hiding spellings
 *                                        like: V-I-A-G-R-A or something else.
 * @property-read string  $Result         Contains the resulting text string, after the call of ::validate(…)
 * @property-read boolean $AllowEmpty     Are empty values allowed for a valid request? (default=FALSE)
 * @property-read boolean $Required       Defines if the associated value must be defined, to say a valid request exists.
 *                                        (default=TRUE)
 * @property-read string  $DefaultValue   This value is used if there is no require for the field to be defined for a
 *                                        request, and the field does not exist. (default=undefined)
 * @property-read string  $RequiredValue  If the field value can only use one single value to say its a usable request,
 *                                        you can define here the required string value. (default=undefined)
 * @property-read string  $DisplayName    The (localized) display name of the field.
 * @property-read array   $AcceptedValues An numeric indicated array with accepted values, or empty array for ignore
 *                                        this option. If an usable array is defined this test is executed after the
 *                                        is empty test.
 * @since    v0.1.0
 * @uses     \Beluga\Validation\Validator    It extends from this class
 */
class TextValidator extends Validator
{


   // <editor-fold desc="// = = = =   C L A S S   C O N S T A N T S   = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * This regular expression matches if the testing string contains same bad or immoral words.
    */
   const REGEX_BADWORDS = '~\b(dumm|fick|arsch|nazi|hoden|votze|fotze|vagina|viagra|anal|dollar|valium|sex|dumb|idiot|penner|ausl(ä|a|ae)nder|pussy|hitler|fuck|casino|money|penis|cash|money|pharma|rabat|health|mall|adult|dollar|buss?ines|\$\d+|gina|winner|sweepstakes|lottery|payment|medicine|pharma|hospital|jewel|shopping|jude|himmler|bonus|islam)~i';

   /**
    * This regular expression matches if the testing string contains some HTML entities.
    */
   const REGEX_HTML_ENTITY = '~&([A-Za-z]{2,7}|#[\dA-Fa-f]{2,4});~';

   /**
    * This regular expression matches if the testing string contains some text, written with some unusable spacings
    * to hide spam word from filters (e.g.: 'V i a  g r a'   or   'V--A--L--i--u--M')
    */
   const REGEX_INCONSISTENT_TEXT = '~[\pL\pN]([ \t#+,;-]+[\pL\pN]){4,}~';

   /**
    * This regular expression matches if the testing string contains some URL encoded elements.
    */
   const REGEX_URL_ENCODING = '~%[\dA-Fa-f]{2}~';

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new instance.
    *
    * Usable options are:
    *
    * - <b>MinLength</b>         : The minimal allowed length or NULL if no min length should be used (default=1)
    * - <b>MaxLength</b>         : The max allowed length or NULL if no max length should used (default=\PHP_INT_MAX)
    * - <b>AllowUrls</b>         : Can the text contain URLs to be valid? (default=TRUE)
    * - <b>MaxUrlCount</b>       : The max. count of allowed unknown URLs inside the text to validate.
    *                              The default value -1 means, no limit.
    * - <b>HostWhiteList</b>     : Defining Host names, allowed to be used uncounted in URLs and mail addresses. array
    * - <b>AllowMailAddresses</b>: Can the text contain mail addresses to be valid? (default=TRUE)
    * - <b>CheckForSpam</b>      : Should the text be checked if it contains some spam? (default=FALSE)
    *                              It checks for some buzzwords and also checks for some spam hiding spellings
    *                              like: V-I-A-G-R-A or something else.
    * - <b>AllowNewLines</b>     : Can the text contain some line-breaks to be valid (default=TRUE)
    * - <b>MaxLineCount</b>      : How many lines can be defined by the value to check, to be valid?
    *                              (Default = -1 means no limit and 0 is equal to 1 because 0 lines have no sense)
    * - <b>AllowHtml</b>         : Can the value to check contains some HTML content? (default=TRUE)
    * - <b>HtmlConverter</b>     : If HTML is allowed here you can define the Converter {@see \Beluga\Converter\IHtmlToX}
    * - <b>AcceptedValues</b>    : An numeric indicated array with accepted values, or empty array for ignore this
    *                              option. If an usable array is defined this test is executed after the is empty test.
    *
    * @param  array   $options  All validator options
    */
   public function __construct( array $options = [] )
   {

      // Setting the default option values, if they are currently not defined
      static::setIntegerOption( 'MinLength',          $options,   1,              true );
      static::setIntegerOption( 'MaxLength',          $options,   \PHP_INT_MAX,   true );
      static::setIntegerOption( 'MaxUrlCount',        $options,   -1 );
      static::setBooleanOption( 'AllowUrls',          $options,   true );
      static::setBooleanOption( 'AllowMailAddresses', $options,   true );
      static::setBooleanOption( 'CheckForSpam',       $options,   false );
      static::setArrayOption  ( 'HostWhiteList',      $options );
      static::setBooleanOption( 'AllowNewLines',      $options,   true );
      static::setBooleanOption( 'AllowHtml',          $options,   true );
      static::setIntegerOption( 'MaxLineCount',       $options,   -1 );
      static::setBooleanOption( 'HtmlConverter',      $options,   null,           true );
      static::setArrayOption  ( 'AcceptedValues',     $options );

      $options[ 'Result' ] = '';

      // Calling the parent constructor
      parent::__construct( $options );

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Sets a option value.
    *
    * Known options are:
    *
    * - <b>MinLength</b> (integer|null):
    *       The minimal allowed length or NULL if no min length should be used (default=1)
    * - <b>MaxLength</b> (integer|null):
    *       The max allowed length or NULL if no max should used (default=\PHP_INT_MAX)
    * - <b>MaxUrlCount</b> (integer):
    *       The max. count of allowed unknown URLs inside the text to validate.
    *       The default value -1 means, no limit.
    * - <b>MaxLineCount</b> (integer):
    *       How many lines can be defined by the value to check, to be valid?
    *       (Default = -1 means no limit => 0 is equal to 1 cause 0 lines have no sense)
    * - <b>AllowUrls</b> (boolean):
    *       Can the text contain URLs to be valid? (default=TRUE)
    * - <b>AllowMailAddresses</b> (boolean):
    *       Can the text contain mail addresses to be valid? (default=TRUE)
    * - <b>AllowNewLines</b> (boolean):
    *       Can the text contain some line-breaks to be valid? (default=TRUE)
    * - <b>AllowHtml</b> (boolean):
    *       Can the value to check contains some HTML/XML markup content to be valid? (default=TRUE)
    * - <b>CheckForSpam</b> (boolean):
    *       Should the text be checked if it contains some spam? (default=FALSE)
    *       It checks for some buzzwords and also checks for some spam hiding spellings
    *       like: V-I-A-G-R-A or something else.
    * - <b>HostWhiteList</b> (array):
    *       Defining Host names, allowed to be used uncounted in URLs and mail addresses.
    * - <b>HtmlConverter</b> (\Beluga\Converter\IHtmlToX):
    *       If HTML is allowed here you can define the Converter to remove unwanted elements
    * - <b>AllowEmpty</b> (boolean):
    *       Are empty values allowed for a valid request? (default=FALSE)
    * - <b>Required</b> (boolean):
    *       Defines if the associated value must be defined, to say a valid request exists. (default=TRUE)
    * - <b>RequiredValue</b> (string):
    *       If the field value can only use one single value to say its a usable request, you can define
    *       here the required string value. (default=undefined)
    * - <b>AcceptedValues</b> (array):
    *      An numeric indicated array with accepted values, or empty array for ignore this option. If an
    *      usable array is defined this test is executed after the is empty test.
    *
    * @param string $optionName The name of the option to set the value at.
    * @param mixed  $value      The new value. (the value type depends to the option)
    * @return \Beluga\Validation\TextValidator
    */
   public function setOption( string $optionName, $value )
   {

      $resultValue = null;

      switch ( $optionName )
      {

         case 'MinLength':          return $this->setMinLength( $value );
         case 'MaxLength':          return $this->setMaxLength( $value );
         case 'MaxUrlCount':        return $this->setMaxUrlCount( $value );
         case 'MaxLineCount':       return $this->setMaxLineCount( $value );
         case 'AllowUrls':          return $this->setAllowUrls( $value );
         case 'AllowMailAddresses': return $this->setAllowMailAddresses( $value );
         case 'AllowNewLines':      return $this->setAllowNewLines( $value );
         case 'AllowHtml':          return $this->setAllowHtml( $value );
         case 'CheckForSpam':       return $this->setCheckForSpam( $value );
         case 'HostWhiteList':      return $this->setHostWhiteList( $value );
         case 'HtmlConverter':      return $this->setHtmlConverter( $value );
         case 'AcceptedValues':     return $this->setAcceptedValues( $value );
         default:                   parent::setOption( $optionName, $value );
                                    return $this;

      }

   }

   /**
    * Set the minimal allowed length or NULL if no min length should be used.
    *
    * @param  integer $value
    * @return \Beluga\Validation\TextValidator
    */
   public function setMinLength( $value )
      : TextValidator
   {

      if ( \is_integer( $value ) && $value <= 0 )
      {
         $value = null;
      }

      $this->__setIntegerOption( 'MinLength', $value, null );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Set the max allowed length or NULL if no max should used.
    *
    * @param  integer $value
    * @return \Beluga\Validation\TextValidator
    */
   public function setMaxLength( $value )
      : TextValidator
   {

      if ( \is_integer( $value ) && $value <= 0 )
      {
         $value = null;
      }

      $this->__setIntegerOption( 'MaxLength', $value, null );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Set the max. count of allowed unknown URLs inside the text to validate. The default value -1 means, no limit.
    *
    * @param  integer $value
    * @return \Beluga\Validation\TextValidator
    */
   public function setMaxUrlCount( int $value )
      : TextValidator
   {

      $this->__setIntegerOption( 'MaxUrlCount', $value, -1 );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Set how many lines can be defined by the value to check, to be valid?
    * (-1 means no limit, => 0 is equal to 1 cause 0 lines have no sense)
    *
    * @param  integer $value
    * @return \Beluga\Validation\TextValidator
    */
   public function setMaxLineCount( int $value )
      : TextValidator
   {

      $this->__setIntegerOption( 'MaxLineCount', $value, -1 );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Can the text contain URLs to be valid?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\TextValidator
    */
   public function setAllowUrls( bool $value )
      : TextValidator
   {

      $this->__setBooleanOption( 'AllowUrls', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Can the text contain mail addresses to be valid?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\TextValidator
    */
   public function setAllowMailAddresses( bool $value )
      : TextValidator
   {

      $this->__setBooleanOption( 'AllowMailAddresses', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Can the text contain some line-breaks to be valid?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\TextValidator
    */
   public function setAllowNewLines( bool $value )
      : TextValidator
   {

      $this->__setBooleanOption( 'AllowNewLines', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Can the value to check contains some HTML/XML markup content to be valid?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\TextValidator
    */
   public function setAllowHtml( bool $value )
      : TextValidator
   {

      $this->__setBooleanOption( 'AllowHtml', $value, true );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Should the text be checked if it contains some spam? (default=FALSE) It checks for some buzzwords and
    * also checks for some spam hiding spellings like: V-I-A-G-R-A or something else.
    *
    * @param  boolean $value
    * @return \Beluga\Validation\TextValidator
    */
   public function setCheckForSpam( bool $value )
      : TextValidator
   {

      $this->__setBooleanOption( 'CheckForSpam', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Defining Host names, allowed to be used uncounted in URLs and mail addresses.
    *
    * @param  array $value
    * @return \Beluga\Validation\TextValidator
    */
   public function setHostWhiteList( array $value )
      : TextValidator
   {

      $this->__setArrayOption( 'HostWhiteList', $value );

      $this->LastResult = null;

      return $this;

   }

   /**
    * If HTML is allowed here you can define the Converter to remove unwanted elements.
    *
    * @param \Beluga\Converters\HTML\IHtmlToX $value
    * @return \Beluga\Validation\TextValidator
    */
   public function setHtmlConverter( IHtmlToX $value ) : TextValidator
   {

      $this->_options[ 'HtmlConverter' ] = $value;

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if empty values are allowed for a valid request?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\TextValidator
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
    * @return \Beluga\Validation\TextValidator
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
    * @return \Beluga\Validation\TextValidator
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
    * @return \Beluga\Validation\TextValidator
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
    * @return \Beluga\Validation\TextValidator
    */
   public function setDisplayName( string $value )
   {

      parent::setDisplayName( $value );

      return $this;

   }

   /**
    * Defining Host names, allowed to be used uncounted in URLs and mail addresses.
    *
    * @param  array $value
    * @return \Beluga\Validation\TextValidator
    */
   public function setAcceptedValues( array $value )
      : TextValidator
   {

      $this->__setArrayOption( 'AcceptedValues', $value );

      $this->LastResult = null;

      return $this;

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = =">

   protected function __validate()
   {

      $this->_message = '';

      $resultString = null;

      if ( ! TypeTool::IsStringConvertible( $this->_value, $resultString ) )
      {
         // This %s value is not a valid string!
         $this->_message = \sprintf(
            static::$errors[ 6 ],
            $this->_options[ 'DisplayName' ]
         );
         return $this->__setLastResult( false );
      }

      $this->_options[ 'Result' ] = $resultString;

      if ( isset( $this->_options[ 'AcceptedValues' ] ) &&
           \is_array( $this->_options[ 'AcceptedValues' ] ) &&
           \count( $this->_options[ 'AcceptedValues' ] ) > 0 )
      {
         if ( \in_array( $this->_options[ 'Result' ], $this->_options[ 'AcceptedValues' ] ) )
         {
            return $this->__setLastResult( true );
         }
         // The %s value is wrong/invalid!
         $this->_message = \sprintf(
            static::$errors[ 10 ],
            $this->_options[ 'DisplayName' ]
         );
         return $this->__setLastResult( false );
      }

      if ( isset( $this->_options[ 'MinLength' ] ) &&
           \Beluga\strLen( $this->_options[ 'Result' ] ) < $this->_options[ 'MinLength' ] )
      {
         // The %s value is shorter than allowed! (Allowed minimal length is %d)
         $this->_message = \sprintf(
            static::$errors[ 7 ],
            $this->_options[ 'DisplayName' ],
            $this->_options[ 'MinLength' ]
         );
         return $this->__setLastResult( false );
      }

      if ( isset( $this->_options[ 'MaxLength' ] ) &&
           \Beluga\strLen( $this->_options[ 'Result' ] ) > $this->_options[ 'MaxLength' ] )
      {
         // The %s value is longer than allowed! (Allowed maximal length is %d)
         $this->_message = \sprintf(
            static::$errors[ 8 ],
            $this->_options[ 'DisplayName' ],
            $this->_options[ 'MaxLength' ]
         );
         return $this->__setLastResult( false );
      }

      if ( ! $this->_options[ 'AllowNewLines' ] || $this->_options[ 'MaxLineCount' ] > 0 )
      {
         $lines = \Beluga\splitLines( $this->_options[ 'Result' ] );
         $lineCount = \count( $lines );
         if ( ! $this->_options[ 'AllowNewLines' ] && $lineCount > 1 )
         {
            // The %s value contains line breaks.
            $this->_message = \sprintf(
               static::$errors[ 44 ],
               $this->_options[ 'DisplayName' ]
            );
            return $this->__setLastResult( false );
         }
         if ( $this->_options[ 'MaxLineCount' ] > 0 && $lineCount > $this->_options[ 'MaxLineCount' ] )
         {
            // The %s value contains %d line breaks. (Allowed are max. %d!)
            $this->_message = \sprintf(
               static::$errors[ 45 ],
               $this->_options[ 'DisplayName' ],
               $lineCount,
               $this->_options[ 'MaxLineCount' ]
            );
            return $this->__setLastResult( false );
         }
      }

      $diff = \strip_tags( $this->_options[ 'Result' ] );

      if ( $this->_options[ 'Result' ] !== $diff )
      {
         if ( ! $this->_options[ 'AllowHtml' ] )
         {
            // The %s value contains HTML markup.
            $this->_message = \sprintf(
               static::$errors[ 46 ],
               $this->_options[ 'DisplayName' ]
            );
            return $this->__setLastResult( false );
         }
         if ( isset( $this->_options[ 'HtmlConverter' ] ) &&
              ( $this->_options[ 'HtmlConverter' ] instanceof IHtmlToX ) )
         {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->_options[ 'HtmlConverter' ]->setHTML( $this->_options[ 'Result' ] );
            /** @noinspection PhpUndefinedMethodInspection */
            $this->_options[ 'Result' ] = $this->_options[ 'HtmlConverter' ]->getResult();
         }
      }

      if ( ! $this->_options[ 'AllowUrls' ] || $this->_options[ 'MaxUrlCount' ] > -1 )
      {
         // Check for urls inside the value (text)
         $urls = Url::FindAllUrls( $this->_options[ 'Result' ], $this->_options[ 'HostWhiteList' ] );
         if ( ( ! $this->_options[ 'AllowUrls' ] && \count( $urls ) > 0 )
           || ( $this->_options[ 'MaxUrlCount' ] > -1 && \count( $urls ) > $this->_options[ 'MaxUrlCount' ] ) )
         {
            if ( $this->_options[ 'MaxUrlCount' ] < 1 )
            {
               // The %s value contains some URLs.
               $this->_message = \sprintf(
                  static::$errors[ 47 ],
                  $this->_options[ 'DisplayName' ]
               );
            }
            else
            {
               // The %s value contains more than %d URLs (web addresses)!
               $this->_message = \sprintf(
                  static::$errors[ 48 ],
                  $this->_options[ 'DisplayName' ],
                  $this->_options[ 'MaxUrlCount' ]
               );
            }
            return $this->__setLastResult( false );
         }
      }

      $mailAddresses = MailAddress::ExtractAllFromString( $this->_options[ 'Result' ] );

      if ( ! $this->_options[ 'AllowMailAddresses' ] && \count( $mailAddresses ) > 0 )
      {
         // 175 = The %s value contains some mail addresses.
         $this->_message = \sprintf(
            static::$errors[ 49 ],
            $this->_options[ 'DisplayName' ]
         );
         return $this->__setLastResult( false );
      }

      if ( $this->_options[ 'CheckForSpam' ] )
      {

         if ( \preg_match( static::REGEX_BADWORDS, $this->_options[ 'Result' ] ) )
         {
            // 176 = 'The %s value contains spam content.'
            $this->_message = \sprintf(
               static::$errors[ 50 ],
               $this->_options[ 'DisplayName' ]
            );
            return $this->__setLastResult( false );
         }

         if ( ! $this->_options[ 'AllowHtml' ] &&
              \preg_match( static::REGEX_HTML_ENTITY, $this->_options[ 'Result' ] ) )
         {
            // 177 = The %s value contains spam content. (entities)
            $this->_message = \sprintf(
               static::$errors[ 51 ],
               $this->_options[ 'DisplayName' ]
            );
            return $this->__setLastResult( false );
         }

         if ( \preg_match( static::REGEX_INCONSISTENT_TEXT, $this->_options[ 'Result' ] ) )
         {
            // 178 = The %s value contains content that looks like spam.
            $this->_message = \sprintf(
               static::$errors[ 52 ],
               $this->_options[ 'DisplayName' ]
            );
            return $this->__setLastResult( false );
         }

         if ( \preg_match( static::REGEX_URL_ENCODING, $this->_options[ 'Result' ] ) )
         {
            // 179 = The %s value contains encoded content
            $this->_message = \sprintf(
               static::$errors[ 53 ],
               $this->_options[ 'DisplayName' ]
            );
            return $this->__setLastResult( false );
         }

      }

      return $this->__setLastResult( true );

   }

   // </editor-fold>


}

