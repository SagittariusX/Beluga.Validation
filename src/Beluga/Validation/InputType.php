<?php
/**
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga\Validation
 * @since          2016-08-25
 * @subpackage     …
 * @version        0.1.0
 */

namespace Beluga\Validation;


/**
 * The Beluga\Validation\InputType class.
 *
 * @since v0.1.0
 */
interface InputType
{

   /**
    * Use data from POST requests.
    */
   const POST = \INPUT_POST;

   /**
    * Use data from GET requests.
    */
   const GET = \INPUT_GET;

   /**
    * Use data from cookies.
    */
   const COOKIE = \INPUT_COOKIE;

   /**
    * Use data, defined by environment.
    */
   const ENV = \INPUT_ENV;

   /**
    * Use data defined by server.
    */
   const SERVER = \INPUT_SERVER;

   /**
    *  Use SESSION data.
    */
   const SESSION = \INPUT_SESSION;

   /**
    * Use data from GET or POST requests.
    */
   const REQUEST = \INPUT_REQUEST;

   /**
    * Use custom user data.
    */
   const CUSTOM  = 999;


}

