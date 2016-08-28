<?php
/**
 * In this file the class {@see \Beluga\Validation\Validator} is defined.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga\Validation
 * @since          2016-08-25
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga\Validation;


use \Beluga\{ArgumentError,Type,TypeTool};
use \Beluga\Translation\{Locale,Translator};
use \Beluga\Translation\Source\ArraySource;
use \Beluga\Date\DateTime;


/**
 * Each validator must implement this abstract class, to be usable as a validator (no sanitizer).
 *
 * You have only to implement the __validate() method.
 *
 * @property boolean $allowEmpty    Are empty values allowed for a valid request? (default=FALSE)
 * @property boolean $required      Defines if the associated value must be defined, to say a valid request exists. (default=TRUE)
 * @property string  $defaultValue  This value is used if there is no require for the field to be defined for a
 *                                  request, and the field does not exist. (default=undefined)
 * @property string  $requiredValue If the field value can only use one single value to say its a usable request,
 *                                  you can define here the required string value. (default=undefined)
 * @property string  $displayName   The (localized) display name of the field.
 * @since    v0.1
 */
abstract class Validator implements \Countable
{


   // <editor-fold desc="// = = = =   P R O T E C T E D   S T A T I C   F I E L D S   = = = = = = = = = = = = = = = =">

   /**
    * The …
    *
    * @type \Beluga\Translation\Translator
    */
   protected static $translator = null;

   protected static $errors = [
      'NO REQUEST: The required %s value "%s" from %s source is undefined.',
      'The %s field can not use a empty value!',
      'NO REQUEST: The required %s value "%s" from %s source do no match the required value.'
   ];

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * This array defines all named options of the validator.
    *
    * @var array
    */
   protected $_options;

   /**
    * The value to validate or NULL if no value exists
    *
    * @var string|NULL
    */
   protected $_value;
   protected $_trimValue;

   /**
    * It contains the type of the used input source to getting the validated value, after validate(…) is finished.
    *
    * It can use a value defined by the following constants:
    *
    * - {@see \INPUT_GET}: Using data from GET requests.
    * - {@see \INPUT_POST}: Using data from POST requests.
    * - {@see \INPUT_COOKIE}: Using data from cookies.
    * - {@see \INPUT_SESSION}: Using SESSION data.
    * - {@see \INPUT_SERVER}: Using data defined by serv…
    * - {@see \INPUT_ENV}: Using data, defined by environment.
    * - {@see \INPUT_REQUEST}: Using data from GET or POST requests.
    * - {@see \INPUT_CUSTOM}: Using data, defined by array, passed to the {@see ::setCustomData()} method.
    *
    * @var integer
    */
   protected $_inputType;

   /**
    * The name of the validated value. It's only defined after a validate(…) call.
    *
    * @var string
    */
   protected $_key;

   /**
    * The custom source defines a source array for getting the value to validate. The value can be
    * defined from outside the class by using {@see ::setCustomData()}. Its only used if {@see ::$_inputType}
    * uses the {@see \INPUT_CUSTOM} value (defined by {@see ::validate()}.
    *
    * @var array
    */
   protected $_data;

   /**
    * Defines, if after the call of ::validate() the value can be used to say, this is a usable request.
    *
    * @var boolean
    */
   protected $_isRequest;

   /**
    * This field contains the string, defining the last error message after calling the ::validate() method.
    *
    * @var string|NULL
    */
   protected $_message;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The result of the last executed validation or NULL of {@see ::validate()} was not called.
    *
    * @var boolean|NULL
    */
   public $LastResult = false;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = =">

   /**
    * Inits a new instance.
    *
    * @param array $options Associative options array. Known options are 'AllowEmpty', 'Required', 'DisplayName'
    *                       and 'RequiredValue'
    */
   protected function __construct( array $options = [] )
   {

      $this->_options   = [ ];
      $this->_isRequest = false;

      self::setBooleanOption( 'AllowEmpty',    $options, false );
      self::setBooleanOption( 'Required',      $options, true );
      self::setStringOption ( 'DisplayName',   $options, 'Undefined' );
      self::setStringOption ( 'RequiredValue', $options, null, true );

      $this->_options = $options;

      $this->_data = [ ];

      static::initTranslator();

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * It does the validation and sanitize the checked value.
    *
    * Usable $inputType values are:
    *
    * - {@see \Beluga\Validation\InputType::GET}: Using data from GET requests.
    * - {@see \Beluga\Validation\InputType::POST}: Using data from POST requests.
    * - {@see \Beluga\Validation\InputType::COOKIE}: Using data from cookies.
    * - {@see \Beluga\Validation\InputType::SESSION}: Using SESSION data.
    * - {@see \Beluga\Validation\InputType::SERVER}: Using data defined by serv…
    * - {@see \Beluga\Validation\InputType::ENV}: Using data, defined by environment.
    * - {@see \Beluga\Validation\InputType::REQUEST}: Using data from GET or POST requests.
    * - {@see \Beluga\Validation\InputType::CUSTOM}: Using data, defined by array, passed to the {@see ::setCustomData()} method.
    *
    * After the validation you can find the not sanitized value by getValue() method.
    *
    * @param  integer        $inputType   Where to get the input data array from, containing the value to check.
    *                                     Usable values are defined by
    * @param  string|integer $key         The key/index to get
    * @param  string|null    $displayName Returns TRUE if validation is successful, FALSE otherwise.
    * @return boolean
    * @throws \Beluga\ArgumentError
    */
   public function validate( int $inputType, $key, $displayName = null )
      : bool
   {

      // Initialize the method depending instance fields.
      $this->_inputType   = $inputType;

      if ( ! empty( $displayName ) )
      {
         $this->_options[ 'DisplayName' ] = $displayName;
      }
      if ( empty( $this->_options[ 'DisplayName' ] ) )
      {
         $this->_options[ 'DisplayName' ] = $key;
      }

      $this->_key         = $key;
      $this->_value       = null;
      $this->_isRequest   = false;
      $this->_message     = null;

      // Getting the value
      switch ( $inputType )
      {

         case InputType::POST:
         case InputType::GET:
         case InputType::COOKIE:
         case InputType::ENV:
         case InputType::SERVER:
            if ( \filter_has_var( $inputType, $key ) )
            {
               $this->_value = \filter_input( $inputType, $key, \FILTER_DEFAULT );
            }
            break;

         case InputType::CUSTOM:
            if ( isset( $this->_data[ $key ] ) )
            {
               if ( \is_array( $this->_data ) )
               {
                  $this->_value = $this->_data[ $key ];
               }
               else
               {
                  $this->_value = \strval( $this->_data );
               }
            }
            break;

         case InputType::SESSION:
            if ( isset( $_SESSION[ $key ] ) )
            {
               $this->_value = $_SESSION[ $key ];
            }
            break;

         case InputType::REQUEST:
            if ( isset( $_REQUEST[ $key ] ) )
            {
               $this->_value = $_REQUEST[ $key ];
            }
            break;

         default:
            if ( ! $this->_options[ 'Required' ] && isset( $this->_options[ 'DefaultValue' ] ) )
            {
               $this->_value = \strval( $this->_options[ 'DefaultValue' ] );
               break;
            }
            throw new ArgumentError(
               'inputType', $inputType, 'Validation',
               'A unknown input source type is used. Please use one of the INPUT_* constants to solve this error.'
            );

      }

      // First we need to handle NULL values. It means the field is undefined.
      if ( \is_null( $this->_value ) )
      {

         if ( $this->_options[ 'Required' ] )
         {

            // Field is undefined, and undefined fields results in a "This is not a Request"
            $this->_message   = \sprintf(
               static::$errors[ 0 ],
               $this->_options[ 'DisplayName' ],
               $this->_key,
               self::GetInputTypeName( $inputType )
            );

            // In this case we left  $this->_isRequest = false;  unchanged and return FALSE
            return $this->__setLastResult( false );

         }

         // Now (undefined field but valid request) we assign the initial required options, if they are undefined

         if ( isset( $this->_options[ 'DefaultValue' ] ) )
         {
            // A default value is defined for this case. Use it as current value and make sure it is a string
            $this->_value     = \strval( $this->_options[ 'DefaultValue' ] );
            $this->_trimValue = \trim( $this->_value );
         }
         else
         {
            // We should use a last fallback: empty string.
            $this->_value     = '';
            $this->_trimValue = $this->_value;
         }

      }
      else
      {

         $this->_trimValue = \trim( $this->_value );

      }

      // Now its sure its a request depending to this field only.
      $this->_isRequest = true;

      if ( \strlen( $this->_trimValue ) < 1 )
      {

         // The value is EMPTY.

         if ( isset( $this->_options[ 'RequiredValue' ] ) &&
              ( $this->_options[ 'RequiredValue' ] !== $this->_value ) )
         {

            // A special value is the exclusive allowed value, to say it is a usable request
            // But this empty string is not the allowed value.
            $this->_isRequest = false;
            $this->_message   = \sprintf(
               static::$errors[ 2 ],
               $this->_options[ 'DisplayName' ],
               $this->_key,
               self::GetInputTypeName( $inputType )
            );
            return $this->__setLastResult( false );

         }

         if ( ! $this->_options[ 'AllowEmpty' ] )
         {

            // Empty values will result in a invalid request.
            // Yes the field uses a empty value (Not allowed)
            $this->_message   = \sprintf(
               static::$errors[ 1 ],
               $this->_options[ 'DisplayName' ]
            );

            // We simple return FALSE here
            return $this->__setLastResult( false );

         }

         // Value is EMPTY and its not required to be valid only if it's not empty. No other checks are required!
         return $this->__setLastResult( true );

      }

      if ( isset( $this->_options[ 'RequiredValue' ] ) &&
           ( $this->_options[ 'RequiredValue' ] !== $this->_value ) )
      {

         // A special value is the exclusive allowed value, to say it is a usable request
         // But this empty string is not the allowed value.
         $this->_isRequest = false;
         $this->_message   = \sprintf(
            static::$errors[ 2 ],
            $this->_options[ 'DisplayName' ],
            $this->_key,
            self::GetInputTypeName( $inputType )
         );
         return $this->__setLastResult( false );

      }

      // We are sure, the value is not empty. We are doing the implementation depending validation.
      return $this->__validate();

   }

   /**
    * The magic getter for read accessing the validator options by dynamic properties.
    *
    * @param  string $name The name of the required validator option (NOT caseless!)
    * @return mixed        Returns the requested Option, boolean FALSE if the option is unknown.
    */
   public final function __get( $name )
   {

      return $this->getOption( $name );

   }

   /**
    * Let you set all validator options by the dynamic property way.
    *
    * @param  string $name  The name of the property to set.
    * @param  mixed  $value The value to set.
    */
   public final function __set( $name, $value )
   {

      $this->setOption( $name, $value );

   }

   /**
    * Returns if a option with defined name has a usable value (!== NULL)
    *
    * @param  string $name The Option name
    * @return boolean
    */
   public final function __isset( $name )
   {

      return isset( $this->_options[ $name ] );

   }

   /**
    * Returns if a option with defined name is usable with this validator.
    *
    * @param  string $name The Option name
    * @return boolean.
    */
   public final function hasOption( string $name )
      : bool
   {

      return \array_key_exists( $name, $this->_options );

   }

   /**
    * Returns the value of the validator option. If the option is undefined the defined default value is set
    * for this options, and is also returned.
    *
    * @param string $optionName   The name of the required validator option (NOT caseless!)
    * @param mixed  $defaultValue This value is used if the option dont exists. If so, the option becomes this value
    *                             and is returned.
    * @return mixed               Returns the value, or $defaultValue if the options dont exists on call.
    */
   public function getOption( string $optionName, $defaultValue = false )
   {

      if ( empty( $optionName ) || ! \is_string( $optionName ) )
      {
         return $defaultValue;
      }

      if ( ! \array_key_exists( $optionName, $this->_options ) )
      {
         $this->_options[ $optionName ] = $defaultValue;
      }

      return $this->_options[ $optionName ];

   }

   /**
    * Returns the unsanitized value after a successed or failed validation.
    *
    * @return string Returns unsanitized value.
    */
   public function getValue()
   {

      return $this->_value;

   }

   /**
    * Sets the value of a validator option. If you want to check the option values, changed here, you must overwrite
    * this method. But do not forget to call parent::setOption(…), if you're option name(s) do'nt match!
    *
    * All options, used here, are also available by array accessor $validator[ 'OptionName' ] and as dynamic
    * properties like: $validator->OptionName.
    *
    * @param  string $optionName The name of the option to set.
    * @param  mixed  $value      The option value to set.
    * @return \Beluga\Validation\Validator
    */
   public function setOption( string $optionName, $value )
   {

      $tmp = null;

      switch ( strtolower( $optionName ) )
      {

         case 'allowempty':
         case 'required':
            if ( TypeTool::IsBoolConvertible( $value, $tmp ) )
            {
               $this->_options[ $optionName ] = $tmp;
            }
            $this->LastResult = null;
            break;

         case 'defaultvalue':
         case 'requiredvalue':
            if ( TypeTool::IsStringConvertible( $value, $tmp ) )
            {
               $this->_options[ $optionName ] = $tmp;
            }
            $this->LastResult = null;
            break;

         case 'displayname':
            if ( TypeTool::IsStringConvertible( $value, $tmp ) )
            {
               if ( \strlen( \trim( $tmp  ) ) > 0 )
               {
                  $this->_options[ $optionName ] = $tmp;
               }
            }
            $this->LastResult = null;
            break;

         default:
            $this->_options[ $optionName ] = $value;
            $this->LastResult = null;
            break;

      }

      return $this;

   }

   /**
    * Sets the custom data array, used as source if validate() is called with a input type INPUT_CUSTOM.
    *
    * @param array $customData
    * @return \Beluga\Validation\Validator
    */
   public function setCustomData( array $customData = [] )
   {

      $this->_data = $customData;

      return $this;

   }

   /**
    * Sets, if empty values are allowed for a valid request?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\Validator
    */
   public function setAllowEmpty( bool $value )
   {

      $this->__setBooleanOption( 'AllowEmpty', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if the associated value must be defined, to say a valid request exists.
    *
    * @param  boolean $value
    * @return \Beluga\Validation\Validator
    */
   public function setRequired( bool $value )
   {

      $this->__setBooleanOption( 'Required', $value, false );

      $this->LastResult = null;

      return $this;

   }

   /**
    * This value is used if there is no require for the field to be defined for a request, and the field does not
    * exist.
    *
    * @param  string $value
    * @return \Beluga\Validation\Validator
    */
   public function setDefaultValue( $value )
   {

      if ( \is_string( $value ) && \strlen( $value ) > 0 )
      {
         $this->_options[ 'DefaultValue' ] = $value;
      }
      else
      {
         $this->__setStringOption( 'DefaultValue', $value, '' );
      }

      $this->LastResult = null;

      return $this;

   }

   /**
    * If the field value can only use one single value to say its a usable request, you can define here the required
    * string value.
    *
    * @param  string $value
    * @return \Beluga\Validation\Validator
    */
   public function setRequiredValue( $value )
   {

      if ( \is_string( $value ) )
      {
         $this->_options[ 'RequiredValue' ] = $value;
      }
      else
      {
         $this->__setStringOption( 'RequiredValue', $value, null );
      }

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets the (localized) display name of the field.
    *
    * @param  string $value
    * @return \Beluga\Validation\Validator
    */
   public function setDisplayName( string $value )
   {

      if ( \is_string( $value ) && \strlen( $value ) > 0 )
      {
         $this->_options[ 'DisplayName' ] = $value;
      }
      else
      {
         $this->__setStringOption( 'DisplayName', $value, 'Undefined' );
      }

      $this->LastResult = null;

      return $this;

   }

   /**
    * Count elements of an object.
    *
    * @return integer The custom count as an integer.
    * @link           http://php.net/manual/en/countable.count.php
    */
   public function count()
   {

      return \count( $this->_options );

   }

   /**
    * Returns, after the call of ::validate(), if the checked value is defined like, to say its a usable request
    * for defined field, or not.
    *
    * @return boolean
    */
   public function isRequest()
   {

      return $this->_isRequest;

   }

   /**
    * Returns the last thrown error message while executing ::validate(…).
    *
    * @return string
    */
   public function getMessage()
   {

      return $this->_message ?? '';

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Extending classes have to define this method for doing the validation.
    *
    * Required data are defined by $this->_options, $this->_value, $this->_inputType, $this->_key
    *
    * The defined $this->_value is always more than a empty string. If its empty the depending checks are run
    * inside ::validate() before this method is called.
    *
    * @return boolean It must return TRUE if validation is successful, FALSE if validation fails, or if the field is
    *                 undefined, what results in "The is no request here". If so, ::isRequest() informs you.
    */
   protected abstract function __validate();

   /**
    * Sets the last validation result (boolean) and returns it.
    *
    * @param  boolean $result
    * @return boolean
    */
   protected function __setLastResult( $result )
   {

      $this->LastResult = $result;
      return $this->LastResult;

   }

   protected function __setStringOption( $field, $value, $defaultValue )
   {

      $type = new Type( $value );

      if ( $type->hasAssociatedString() )
      {
         $this->_options[ $field ] = $type->getStringValue();
      }
      else
      {
         $this->_options[ $field ] = $defaultValue;
      }

   }

   protected function __setBooleanOption( $field, $value, $defaultValue = false )
   {

      if ( TypeTool::IsBoolConvertible( $value, $tmp ) )
      {
         $this->_options[ $field ] = $tmp;
      }
      else if ( \is_null( $value ) )
      {
         $this->_options[ $field ] = $defaultValue;
      }

   }

   protected function __setArrayOption( $field, $value, array $defaultValue = [] )
   {

      if ( \is_array( $value ) )
      {
         $this->_options[ $field ] = $value;
      }
      else
      {
         $this->_options[ $field ] = $defaultValue;
      }

   }

   protected function __setIntegerOption( $field, $value, $defaultValue = 0 )
   {

      if ( TypeTool::IsInteger( $value ) )
      {
         $this->_options[ $field ] = \intval( $value );
      }
      else if ( \is_null( $value ) )
      {
         $this->_options[ $field ] = $defaultValue;
      }

   }

   protected function __setDateOption( $field, $value, $defaultValue = null )
   {

      if ( $value instanceof DateTime )
      {
         $this->_options[ $field ] = $value->getDate();
      }
      else if ( $value instanceof \DateTimeInterface )
      {
         $this->_options[ $field ] = DateTime::Parse( $value->format( 'Y-m-d' ) )->getDate();
      }
      else if ( \is_string( $value ) )
      {
         if ( false !== ( $dt = DateTime::Parse( $value ) ) )
         {
            $this->_options[ $field ] = $dt->getDate();
         }
         else
         {
            $this->_options[ $field ] = $defaultValue;
         }
      }
      else
      {
         $this->_options[ $field ] = $defaultValue;
      }

   }

   protected function __setDateTimeOption( $field, $value, $defaultValue = null )
   {

      if ( $value instanceof DateTime )
      {
         $this->_options[ $field ] = $value;
      }
      else if ( $value instanceof \DateTimeInterface )
      {
         $this->_options[ $field ] = DateTime::Parse( $value->format( 'Y-m-d H:i:s' ) );
      }
      else if ( \is_string( $value ) )
      {
         if ( false !== ( $dt = DateTime::Parse( $value ) ) )
         {
            $this->_options[ $field ] = $dt;
         }
         else
         {
            $this->_options[ $field ] = $defaultValue;
         }
      }
      else
      {
         $this->_options[ $field ] = $defaultValue;
      }

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * Returns the Name, associated with the defined input type, defined by the global INPUT_* constants.
    *
    * @param  integer $inputType INPUT_GET, INPUT_POST, INPUT_COOKIE, etc.
    * @return string             Returns the Name like 'GET', 'POST', 'COOKIE', etc.
    */
   public static function GetInputTypeName( $inputType )
   {

      switch ( $inputType )
      {

         case InputType::POST:
            return 'POST';

         case InputType::GET:
            return 'GET';

         case InputType::COOKIE:
            return 'COOKIE';

         case InputType::SESSION:
            return 'SESSION';

         case InputType::SERVER:
            return 'SERVER';

         case InputType::ENV:
            return 'ENVIRONMENT';

         case InputType::REQUEST:
            return 'REQUEST';

         case InputType::CUSTOM:
            return 'CUSTOM';

         default:
            break;

      }

      return 'UNKNOWN';

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = =">

   /**
    * Sets a boolean Option with defined name, if its currently undefined by $options.
    *
    * @param  string  $name     The Name of the option
    * @param  array   $options  The options array reference. Its contains after the method call the required option.
    * @param  boolean $defaultValue The default value to set, if the option does not exist. (default=FALSE)
    * @param  boolean $nullable Is NULL a usable value of this option? (default=FALSE)
    * @return void
    */
   protected static function setBooleanOption( $name, array &$options, $defaultValue = false, $nullable = false )
   {

      if ( ! \array_key_exists( $name, $options ) )
      {
         $options[ $name ] = $defaultValue;
      }
      else
      {
         if ( $nullable && \is_null( $options[ $name ] ) )
         {
            return;
         }
         $tmpRef = null;
         if ( ! TypeTool::IsBoolConvertible( $options[ $name ], $tmpRef ) )
         {
            $options[ $name ] = $defaultValue;
         }
         else
         {
            $options[ $name ] = $tmpRef;
         }
      }

   }

   /**
    * Sets a array Option with defined name, if its currently undefined by $options.
    *
    * @param  string  $name     The Name of the option
    * @param  array   $options  The options array reference. Its contains after the method call the required option.
    * @param  array   $defaultValue The default value to set, if the option does not exist. (default=array())
    * @return void
    */
   protected static function setArrayOption( $name, array &$options, array $defaultValue = [] )
   {

      if ( ! isset( $options[ $name ] ) || ! \is_array( $options[ $name ] ) )
      {
         $options[ $name ] = $defaultValue;
      }

   }

   /**
    * Sets a string Option with defined name, if its currently undefined by $options.
    *
    * @param  string  $name     The Name of the option
    * @param  array   $options  The options array reference. Its contains after the method call the required option.
    * @param  string  $defaultValue The default value to set, if the option does not exist. (default='')
    * @param  boolean $nullable Is NULL a usable value of this option? (default=FALSE)
    * @return void
    */
   protected static function setStringOption( $name, array &$options, $defaultValue = '', $nullable = false )
   {

      $tmpRef = null;

      if ( ! \array_key_exists( $name, $options ) )
      {
         $options[ $name ] = $defaultValue;
      }
      else
      {

         if ( $nullable && \is_null( $options[ $name ] ) )
         {
            return;
         }

         if ( ! TypeTool::IsStringConvertible( $options[ $name ], $tmpRef ) )
         {
            $options[ $name ] = $defaultValue;
         }
         else
         {
            $options[ $name ] = $tmpRef;
         }

      }

   }

   /**
    * Sets a integer Option with defined name, if its currently undefined by $options.
    *
    * @param  string  $name     The Name of the option
    * @param  array   $options  The options array reference. Its contains after the method call the required option.
    * @param  integer $defaultValue The default value to set, if the option does not exist. (default=0)
    * @param  boolean $nullable Is NULL a usable value of this option? (default=FALSE)
    */
   protected static function setIntegerOption( $name, array &$options, $defaultValue = 0, $nullable = false )
   {

      if ( ! \array_key_exists( $name, $options ) )
      {
         $options[ $name ] = $defaultValue;
      }
      else
      {

         if ( $nullable && \is_null( $options[ $name ] ) )
         {
            return;
         }

         if ( ! TypeTool::IsInteger( $options[ $name ] ) )
         {
            $options[ $name ] = $defaultValue;
         }
         else
         {
            $options[ $name ] = \intval( $options[ $name ] );
         }

      }

   }

   /**
    * Sets a Date option with defined name, if its currently undefined or with wrong format by $options.
    *
    * @param  string  $name              The Name of the option to set.
    * @param  array   $options           The options array reference. Its contains after the method call the required option.
    * @param  \Beluga\Date\DateTime $defaultValue The default value to set, if the option does not exist. (default=null)
    */
   protected static function setDateOption( $name, array &$options, $defaultValue = null )
   {

      // Bring the default value to required format

      if ( $defaultValue instanceof DateTime )
      {
         // Wee already have the required format
         $defaultValue = $defaultValue->getDate();
      }
      else if ( $defaultValue instanceof \DateTimeInterface )
      {
         $defaultValue = DateTime::Parse( $defaultValue->format( 'Y-m-d' ) )->getDate();
      }
      else if ( \is_string( $defaultValue ) )
      {
         if ( false !== ( $dt = DateTime::Parse( $defaultValue ) ) )
         {
            $defaultValue = $dt->getDate();
         }
         else
         {
            $defaultValue = null;
         }
      }
      else if ( ! \is_null( $defaultValue ) )
      {
         $defaultValue = null;
      }

      if ( ! \array_key_exists( $name, $options ) )
      {

         // The option is Undefined, so its uses the default value
         $options[ $name ] = $defaultValue;

      }
      else
      {

         // The option is defined

         if ( \is_null( $options[ $name ] ) )
         {
            return;
         }

         if ( ! ( $options[ $name ] instanceof DateTime ) )
         {

            if ( $options[ $name ] instanceof \DateTimeInterface )
            {
               $options[ $name ] = DateTime::Parse( $options[ $name ]->format( 'Y-m-d' ) )->getDate();
            }
            else if ( \is_string( $options[ $name ] ) )
            {
               if ( false !== ( $dt = DateTime::Parse( $options[ $name ] ) ) )
               {
                  $options[ $name ] = $dt->getDate();
               }
               else
               {
                  $options[ $name ] = null;
               }
            }
            else if ( ! \is_null( $options[ $name ] ) )
            {
               $options[ $name ] = null;
            }
            return;
         }

      }

   }

   /**
    * Sets a Date option with defined name, if its currently undefined or with wrong format by $options.
    *
    * @param  string  $name              The Name of the option to set.
    * @param  array   $options           The options array reference. Its contains after the method call the required option.
    * @param  \Beluga\Date\DateTime $defaultValue The default value to set, if the option does not exist. (default=null)
    */
   protected static function setDateTimeOption( $name, array &$options, $defaultValue = null )
   {

      // Bring the default value to required format

      if ( ! ( $defaultValue instanceof DateTime ) )
      {

         if ( $defaultValue instanceof \DateTimeInterface )
         {
            $defaultValue = DateTime::Parse( $defaultValue->format( 'Y-m-d' ) );
         }
         else if ( \is_string( $defaultValue ) )
         {
            if ( false !== ( $dt = DateTime::Parse( $defaultValue ) ) )
            {
               $defaultValue = $dt;
            }
            else
            {
               $defaultValue = null;
            }
         }
         else if ( ! \is_null( $defaultValue ) )
         {
            $defaultValue = null;
         }

      }

      if ( ! \array_key_exists( $name, $options ) )
      {

         // The option is Undefined, so its uses the default value
         $options[ $name ] = $defaultValue;

      }
      else
      {

         // The option is defined

         if ( \is_null( $options[ $name ] ) )
         {
            return;
         }

         if ( ! ( $options[ $name ] instanceof DateTime ) )
         {

            if ( $options[ $name ] instanceof \DateTimeInterface )
            {
               $options[ $name ] = DateTime::Parse( $options[ $name ]->format( 'Y-m-d' ) );
            }
            else if ( \is_string( $options[ $name ] ) )
            {
               if ( false !== ( $dt = DateTime::Parse( $options[ $name ] ) ) )
               {
                  $options[ $name ] = $dt;
               }
               else
               {
                  $options[ $name ] = null;
               }
            }
            else if ( ! \is_null( $options[ $name ] ) )
            {
               $options[ $name ] = null;
            }
            return;
         }

      }

   }

   protected static function initTranslator()
   {

      if ( ! \is_null( static::$translator ) )
      {

         return;

      }

      if ( Locale::HasGlobalInstance() )
      {
         $_locale = Locale::GetGlobalInstance();
      }
      else
      {
         $_locale = Locale::Create( new Locale( 'en' ) );
      }

      $source = ArraySource::LoadFromFolder( __DIR__ . '/l18n', $_locale, true );

      static::$translator = new Translator( $source );

      $tmp = $source->getTranslations();

      if ( \count( $tmp ) > 0 )
      {
         static::$errors = $tmp;
      }

   }

   // </editor-fold>


}

