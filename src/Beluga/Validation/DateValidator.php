<?php
/**
 * In this file the class {@see \Beluga\Validation\DateValidator} is defined.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga\Validation
 * @since          2016-08-25
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga\Validation;


use \Beluga\Date\DateTime;


/**
 * This class defines a validator that allows only valid date values. THe time part is ignored.
 *
 * @property mixed   $MinValue      The minimal allowed date value or NULL if no min value should be used (default=null)
 * @property mixed   $MaxValue      The max allowed date value or NULL if no max should used (default=null)
 * @property boolean $AllowEmpty    Are empty values allowed for a valid request? (default=FALSE)
 * @property boolean $Required      Defines if the associated value must be defined, to say a valid request exists. (default=TRUE)
 * @property string  $DefaultValue  This value is used if there is no require for the field to be defined for a
 *                                  request, and the field does not exist. (default=undefined)
 * @property string  $RequiredValue If the field value can only use one single value to say its a usable request,
 *                                  you can define here the required string value. (default=undefined)
 * @property string  $DisplayName   The (localized) display name of the field.
 * @since    v0.1
 */
class DateValidator extends Validator
{


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new instance.
    *
    * Usable options are:
    *
    * - <b>MinValue</b>: The minimal allowed date value or NULL if no min value should be used (default=null)
    * - <b>MaxValue</b>: The maximal allowed date value or NULL if no max value should be used (default=null)
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
    * @param  array   $options  All validator options
    */
   public function __construct( array $options = [] )
   {

      static::initTranslator();

      static::setDateOption   ( 'MinValue',    $options );
      static::setDateOption   ( 'MaxValue',    $options );
      static::setStringOption ( 'DisplayName', $options, static::$errors[ 11 ] );

      $options[ 'Result' ] = null;

      parent::__construct( $options );

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Sets a option value.
    *
    * Usable options are:
    *
    * - <b>MinValue</b>: The minimal allowed date value or NULL if no min value should be used (default=null)
    * - <b>MaxValue</b>: The maximal allowed date value or NULL if no max value should be used (default=null)
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
    * @param  string $optionName The name of the option to set the value at.
    * @param  mixed  $value      The new value. (the value type depends to the option)
    * @return \Beluga\Validation\DateValidator
    */
   public function setOption( string $optionName, $value )
      : DateValidator
   {

      switch ( $optionName )
      {

         case 'MinValue':   return $this->setMinValue( $value );
         case 'MaxValue':   return $this->setMaxValue( $value );
         default:           parent::setOption( $optionName, $value );

      }

      return $this;

   }

   /**
    * @param  \Beluga\Date\DateTime|\DateTimeInterface|string|null $value
    * @return \Beluga\Validation\DateValidator
    */
   public function setMinValue( $value )
      : DateValidator
   {

      $this->__setDateOption( 'MinValue', $value, null );

      $this->LastResult = null;

      return $this;

   }

   /**
    * @param  \Beluga\Date\DateTime|\DateTimeInterface|string|null $value
    * @return \Beluga\Validation\DateValidator
    */
   public function setMaxValue( $value )
      : DateValidator
   {

      $this->__setDateOption( 'MaxValue', $value, null );

      $this->LastResult = null;

      return $this;

   }

   /**
    * Sets, if empty values are allowed for a valid request?
    *
    * @param  boolean $value
    * @return \Beluga\Validation\DateValidator
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
    * @return \Beluga\Validation\DateValidator
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
    * @return \Beluga\Validation\DateValidator
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
    * @return \Beluga\Validation\DateValidator
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
    * @return \Beluga\Validation\DateValidator
    */
   public function setDisplayName( string $value )
   {

      parent::setDisplayName( $value );

      return $this;

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = =">

   protected function __validate()
   {

      $this->_message = '';

      if ( false === ( $dt = DateTime::Parse( $this->_value ) ) )
      {

         $this->_message = \sprintf(
            static::$errors[ 3 ],
            static::$errors[ 11 ]
         );

         return $this->__setLastResult( false );

      }

      $this->_options[ 'Result' ] = $dt;

      if ( isset( $this->_options[ 'MinValue' ] ) &&
           $this->_options[ 'Result' ] < $this->_options[ 'MinValue' ] )
      {

         /** @noinspection PhpUndefinedMethodInspection */
         $this->_message = \sprintf(
            static::$errors[ 4 ],
            $this->_options[ 'MinValue' ]->formatSqlDate()
         );

         return $this->__setLastResult( false );

      }

      if ( isset( $this->_options[ 'MaxValue' ] ) &&
           $this->_options[ 'Result' ] > $this->_options[ 'MaxValue' ] )
      {

         /** @noinspection PhpUndefinedMethodInspection */
         $this->_message = \sprintf(
            static::$errors[ 5 ],
            $this->_options[ 'MaxValue' ]->formatSqlDate()
         );

         return $this->__setLastResult( false );

      }

      return $this->__setLastResult( true );

   }

   // </editor-fold>
   

}

