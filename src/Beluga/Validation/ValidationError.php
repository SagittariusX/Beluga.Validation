<?php
/**
 * In this file the exception class {@see \Beluga\Validation\Throwable} is defined.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga\Validation
 * @since          2016-08-25
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga\Validation;


use \Beluga\BelugaError;


/**
 * This class defines a exception, used as base exception of all Validator problems.
 *
 * @since v0.1
 */
class ValidationError extends BelugaError
{


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Inits a new instance.
    *
    * @param string     $message  A optional error message.
    * @param int        $code     The optional error code (Defaults to \E_USER_WARNING)
    * @param \Throwable $previous A optional previous exception
    */
   public function __construct( string $package, string $message, int $code = 256, \Throwable $previous = null )
   {

      parent::__construct(
         $package,
         'Validation-Exception:' . static::appendMessage( $message ),
         $code,
         $previous
      );

   }

   // </editor-fold>


}

