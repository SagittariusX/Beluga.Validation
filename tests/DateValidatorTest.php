<?php


use \Beluga\Validation\{DateValidator,InputType};
use \Beluga\Date\{DateTime,DateTimeFormat};



/**
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @since          2016-08-27
 * @version        0.1.0
 */
class DateValidatorTest extends PHPUnit_Framework_TestCase
{

   private $validators = [];
   private $defaultValue;

   public function setUp()
   {

      $this->defaultValue = DateTime::Now()->format( DateTimeFormat::SQL );

      $this->validators = [
         new DateValidator(
            [
               'AllowEmpty'    => true,
               'MinValue'      => DateTime::Now()->addDays( -1 )->setTime( 0, 0, 0 ), // Yesterday 00:00:00
               'MaxValue'      => DateTime::Now()->moveToEndOfDay(),                  // Today     23:59:59
               'Required'      => true,
               'DefaultValue'  => $this->defaultValue,
               'DisplayName'   => 'Field 1'
            ]
         ),
         new DateValidator(
            [
               'AllowEmpty'    => false,
               'Required'      => true,
               'RequiredValue' => '2016-08-27 14:00:00',
               'DisplayName'   => 'Field 2'
            ]
         ),
         new DateValidator(
            [
               'AllowEmpty'    => false,
               'MinValue'      => DateTime::Now()->addDays( -1 )->setTime( 0, 0, 0 ), // Yesterday 00:00:00
               'MaxValue'      => DateTime::Now()->moveToEndOfDay(),                  // Today     23:59:59
               'Required'      => true,
               'DefaultValue'  => $this->defaultValue,
               'DisplayName'   => 'Field 3'
            ]
         ),
         new DateValidator(
            [
               'AllowEmpty'    => false,
               'Required'      => false,
               'DefaultValue'  => $this->defaultValue,
               'DisplayName'   => 'Field 4'
            ]
         ),
         new DateValidator(
            [
               'AllowEmpty'    => true,
               'Required'      => true,
               'DisplayName'   => 'Field 5'
            ]
         )
      ];

   }

   #__setLastResult
   public function testValidate()
   {

      # validate( int $inputType, $key, $displayName = null ) : bool

      // 0: AllowEmpty, MinValue, MaxValue, Required, DefaultValue

      $this->assertTrue (
         $this->validators[ 0 ]
            ->setCustomData( [ 'x' => '' ] )
            ->validate( InputType::CUSTOM, 'x' ),
         'Validation test 1-1-1 fails.'
      );
      $this->assertTrue (
         \strlen( $this->validators[ 0 ]->getMessage() ) < 1,
         'Validation test 1-1-2 fails.'
      );
      $this->assertTrue (
         $this->validators[ 0 ]->isRequest(),
         'Validation test 1-1-3 fails.'
      );

      $this->assertTrue (
         $this->validators[ 0 ]
            ->setCustomData( [ 'x' => DateTime::Now()->addDays( -1 )->setTime( 0, 0, 0 )->format( 'Y-m-d H:i:s' ) ] )
            ->validate( InputType::CUSTOM, 'x' ),
         'Validation test 1-2-1 fails.'
      );
      $this->assertTrue (
         \strlen( $this->validators[ 0 ]->getMessage() ) < 1,
         'Validation test 1-2-2 fails.'
      );
      $this->assertTrue (
         $this->validators[ 0 ]->isRequest(),
         'Validation test 1-2-3 fails.'
      );

      $this->assertFalse(
         $this->validators[ 0 ]
            ->setCustomData( [ 'x' => DateTime::Now()->addDays( -2 )->getDate()->format( 'Y-m-d H:i:s' ) ] )
            ->validate( InputType::CUSTOM, 'x' ),
         'Validation test 1-3-1 fails.'
      );
      $this->assertTrue (
         \strlen( $this->validators[ 0 ]->getMessage() ) > 0,
         'Validation test 1-3-2 fails.'
      );
      $this->assertTrue (
         $this->validators[ 0 ]->isRequest(),
         'Validation test 1-3-3 fails.'
      );

      $this->assertFalse(
         $this->validators[ 0 ]
            ->setCustomData( [ 'x' => DateTime::Now()->addDays( -2 )->getDate()->format( 'Y-m-d H:i:s' ) ] )
            ->validate( InputType::CUSTOM, 'y' ),
         'Validation test 1-4-1 fails.'
      );
      $this->assertTrue (
         \strlen( $this->validators[ 0 ]->getMessage() ) > 0,
         'Validation test 1-4-2 fails.'
      );
      $this->assertFalse(
         $this->validators[ 0 ]->isRequest(),
         'Validation test 1-4-3 fails.'
      );

      // 1: !AllowEmpty, Required, RequiredValue(2016-08-27 14:00:00),

      $this->assertTrue (
         $this->validators[ 1 ]
            ->setCustomData( [ 'x' => '2016-08-27 14:00:00' ] )
            ->validate( InputType::CUSTOM, 'x' ),
         'Validation test 1-5-1 fails.'
      );
      $this->assertTrue (
         \strlen( $this->validators[ 1 ]->getMessage() ) < 1,
         'Validation test 1-5-2 fails.'
      );
      $this->assertTrue (
         $this->validators[ 1 ]->isRequest(),
         'Validation test 1-5-3 fails.'
      );

      $this->assertFalse(
         $this->validators[ 1 ]
            ->setCustomData( [ 'x' => DateTime::Now()->addDays( -2 )->getDate()->format( 'Y-m-d H:i:s' ) ] )
            ->validate( InputType::CUSTOM, 'x' ),
         'Validation test 1-6-1 fails.'
      );
      $this->assertTrue (
         \strlen( $this->validators[ 1 ]->getMessage() ) > 0,
         'Validation test 1-6-2 fails.'
      );
      $this->assertFalse(
         $this->validators[ 1 ]->isRequest(),
         'Validation test 1-6-3 fails.'
      );

      $this->assertFalse(
         $this->validators[ 1 ]
            ->setCustomData( [ 'x' => '' ] )
            ->validate( InputType::CUSTOM, 'x' ),
         'Validation test 1-7-1 fails.'
      );
      $this->assertTrue (
         \strlen( $this->validators[ 1 ]->getMessage() ) > 0,
         'Validation test 1-7-2 fails.'
      );
      $this->assertFalse(
         $this->validators[ 1 ]->isRequest(),
         'Validation test 1-7-3 fails.'
      );

      // 2: !AllowEmpty, MinValue(yesterday00:00:00), MaxValue(today23:59:59)
      //    Required, DefaultValue(now)

      $this->assertFalse(
         $this->validators[ 2 ]
            ->setCustomData( [ 'x' => '' ] )
            ->validate( InputType::CUSTOM, 'x' ),
         'Validation test 1-8-1 fails.'
      );
      $this->assertTrue (
         \strlen( $this->validators[ 2 ]->getMessage() ) > 0,
         'Validation test 1-8-2 fails.'
      );
      $this->assertTrue (
         $this->validators[ 2 ]->isRequest(),
         'Validation test 1-8-3 fails.'
      );

      $this->assertFalse(
         $this->validators[ 2 ]
            ->setCustomData( [ 'x' => '' ] )
            ->validate( InputType::CUSTOM, 'y' ),
         'Validation test 1-9-1 fails.'
      );
      $this->assertTrue (
         \strlen( $this->validators[ 2 ]->getMessage() ) > 0,
         'Validation test 1-9-2 fails.'
      );
      $this->assertFalse(
         $this->validators[ 2 ]->isRequest(),
         'Validation test 1-9-3 fails.'
      );

      // 3: !AllowEmpty, !Required, DefaultValue

      $this->assertFalse (
         $this->validators[ 3 ]
            ->setCustomData( [ 'x' => '' ] )
            ->validate( InputType::CUSTOM, 'x' ),
         'Validation test 1-10-1 fails.'
      );
      $this->assertTrue (
         \strlen( $this->validators[ 3 ]->getMessage() ) > 0,
         'Validation test 1-10-2 fails.'
      );
      $this->assertTrue (
         $this->validators[ 3 ]->isRequest(),
         'Validation test 1-10-3 fails.'
      );

      $this->assertTrue (
         $this->validators[ 3 ]
            ->setCustomData( [ 'x' => '' ] )
            ->validate( InputType::CUSTOM, 'y' ),
         'Validation test 1-11-1 fails.'
      );
      $this->assertTrue (
         \strlen( $this->validators[ 3 ]->getMessage() ) < 1,
         'Validation test 1-11-2 fails.'
      );
      $this->assertTrue(
         $this->validators[ 3 ]->isRequest(),
         'Validation test 1-11-3 fails.'
      );
      $this->assertEquals(
         $this->defaultValue,
         $this->validators[ 3 ]->getValue(),
         'Validation test 1-11-4 fails.'
      );


      $this->assertFalse(
         $this->validators[ 0 ]
            ->setCustomData( [ 'x' => 'invalid date string' ] )
            ->validate( InputType::CUSTOM, 'x' ),
         'Validation test 1-12-1 fails.'
      );
      $this->assertTrue (
         \strlen( $this->validators[ 0 ]->getMessage() ) > 0,
         'Validation test 1-12-2 fails.'
      );
      $this->assertTrue (
         $this->validators[ 0 ]->isRequest(),
         'Validation test 1-12-3 fails.'
      );

      $this->assertFalse(
         $this->validators[ 2 ]
            ->setCustomData( [ 'x' => DateTime::Now()->addDays( 3 )->formatSqlDate() ] )
            ->validate( InputType::CUSTOM, 'x' ),
         'Validation test 1-13-1 fails.'
      );
      $this->assertTrue (
         \strlen( $this->validators[ 2 ]->getMessage() ) > 0,
         'Validation test 1-13-2 fails.'
      );
      $this->assertTrue (
         $this->validators[ 2 ]->isRequest(),
         'Validation test 1-13-3 fails.'
      );

   }

   /**
    * @covers Beluga\Validation\Validator::__set
    * @covers Beluga\Validation\Validator::__get
    * @covers Beluga\Validation\DateValidator::getOption
    * @covers Beluga\Validation\DateValidator::setOption
    * @covers Beluga\Validation\DateValidator::setMinValue
    */
   public function testSetMinValue()
   {

      $minVal1 = DateTime::Now()->addDays( -2 );
      $minVal2 = new \DateTime();
      $minVal3 = '2016-06-24 13:10:00';

      $this->assertSame(
         null,
         $this->validators[ 4 ]
            ->setOption( 'MinValue', null )
            ->getOption( 'MinValue', null ),
         'testSetMinValue test 1'
      );
      $this->assertEquals(
         $minVal1->getDate(),
         $this->validators[ 4 ]
            ->setOption( 'MinValue', $minVal1 )
            ->getOption( 'MinValue', null ),
         'testSetMinValue test 2'
      );

      $this->validators[ 4 ]->MinValue = $minVal2;
      $this->assertEquals(
         DateTime::Parse( $minVal2 )->getDate(),
         $this->validators[ 4 ]->MinValue,
         'testSetMinValue test 3'
      );

      $this->validators[ 4 ]->setMinValue( $minVal3 );
      $this->assertEquals(
         DateTime::Parse( $minVal3 )->getDate(),
         $this->validators[ 4 ]->MinValue,
         'testSetMinValue test 4'
      );

   }

   /**
    * @covers Beluga\Validation\Validator::__set
    * @covers Beluga\Validation\Validator::__get
    * @covers Beluga\Validation\DateValidator::getOption
    * @covers Beluga\Validation\DateValidator::setOption
    * @covers Beluga\Validation\DateValidator::setMaxValue
    */
   public function testSetMaxValue()
   {

      $maxVal1 = DateTime::Now();
      $maxVal2 = new \DateTime();
      $maxVal3 = '2016-06-24 13:10:00';

      $this->assertSame(
         null,
         $this->validators[ 4 ]
            ->setOption( 'MaxValue', null )
            ->getOption( 'MaxValue', null ),
         'testSetMaxValue test 1'
      );
      $this->assertEquals(
         $maxVal1->getDate(),
         $this->validators[ 4 ]
            ->setOption( 'MaxValue', $maxVal1 )
            ->getOption( 'MaxValue', null ),
         'testSetMaxValue test 2'
      );

      $this->validators[ 4 ]->MaxValue = $maxVal2;
      $this->assertEquals(
         DateTime::Parse( $maxVal2 )->getDate(),
         $this->validators[ 4 ]->MaxValue,
         'testSetMaxValue test 3'
      );

      $this->validators[ 4 ]->setMaxValue( $maxVal3 );
      $this->assertEquals(
         DateTime::Parse( $maxVal3 )->getDate(),
         $this->validators[ 4 ]->MaxValue,
         'testSetMaxValue test 4'
      );

   }

   /**
    * @covers Beluga\Validation\Validator::__set
    * @covers Beluga\Validation\Validator::__get
    * @covers Beluga\Validation\DateValidator::getOption
    * @covers Beluga\Validation\DateValidator::setOption
    * @covers Beluga\Validation\Validator::getOption
    * @covers Beluga\Validation\Validator::setOption
    * @covers Beluga\Validation\Validator::setAllowEmpty
    * @covers Beluga\Validation\DateValidator::setAllowEmpty
    */
   public function testSetAllowEmpty()
   {

      $this->assertFalse(
         $this->validators[ 4 ]
            ->setOption( 'AllowEmpty', false )
            ->getOption( 'AllowEmpty', null ),
         'testSetAllowEmpty test 1'
      );

      $this->validators[ 4 ]->AllowEmpty = true;
      $this->assertTrue (
         $this->validators[ 4 ]->AllowEmpty,
         'testSetAllowEmpty test 2'
      );

      $this->validators[ 4 ]->setAllowEmpty( false );
      $this->assertFalse(
         $this->validators[ 4 ]->AllowEmpty,
         'testSetAllowEmpty test 3'
      );

   }

   /**
    * @covers Beluga\Validation\Validator::__set
    * @covers Beluga\Validation\Validator::__get
    * @covers Beluga\Validation\DateValidator::getOption
    * @covers Beluga\Validation\DateValidator::setOption
    * @covers Beluga\Validation\Validator::getOption
    * @covers Beluga\Validation\Validator::setOption
    * @covers Beluga\Validation\Validator::setRequired
    * @covers Beluga\Validation\DateValidator::setRequired
    */
   public function testSetRequired()
   {

      $this->assertFalse(
         $this->validators[ 4 ]
            ->setOption( 'Required', false )
            ->getOption( 'Required', null ),
         'testSetRequired test 1'
      );

      $this->validators[ 4 ]->Required = true;
      $this->assertTrue (
         $this->validators[ 4 ]->Required,
         'testSetRequired test 2'
      );

      $this->validators[ 4 ]->setRequired( false );
      $this->assertFalse(
         $this->validators[ 4 ]->Required,
         'testSetRequired test 3'
      );

   }

   /**
    * @covers Beluga\Validation\Validator::__set
    * @covers Beluga\Validation\Validator::__get
    * @covers Beluga\Validation\DateValidator::getOption
    * @covers Beluga\Validation\DateValidator::setOption
    * @covers Beluga\Validation\Validator::getOption
    * @covers Beluga\Validation\Validator::setOption
    * @covers Beluga\Validation\Validator::setDefaultValue
    * @covers Beluga\Validation\DateValidator::setDefaultValue
    */
   public function testSetDefaultValue()
   {

      $this->assertSame(
         '',
         $this->validators[ 4 ]
            ->setOption( 'DefaultValue', null )
            ->getOption( 'DefaultValue', null ),
         'testSetDefaultValue test 1'
      );

      $this->validators[ 4 ]->DefaultValue = '2016-06-24 13:13:00';
      $this->assertSame(
         '2016-06-24 13:13:00',
         $this->validators[ 4 ]->DefaultValue,
         'testSetDefaultValue test 2'
      );

      $this->validators[ 4 ]->setDefaultValue( '' );
      $this->assertSame(
         '',
         $this->validators[ 4 ]->DefaultValue,
         'testSetDefaultValue test 3'
      );

   }

   /**
    * @covers Beluga\Validation\Validator::__set
    * @covers Beluga\Validation\Validator::__get
    * @covers Beluga\Validation\DateValidator::getOption
    * @covers Beluga\Validation\DateValidator::setOption
    * @covers Beluga\Validation\Validator::getOption
    * @covers Beluga\Validation\Validator::setOption
    * @covers Beluga\Validation\Validator::setRequiredValue
    * @covers Beluga\Validation\DateValidator::setRequiredValue
    */
   public function testSetRequiredValue()
   {

      $this->assertSame(
         '',
         $this->validators[ 4 ]
            ->setOption( 'RequiredValue', null )
            ->getOption( 'RequiredValue', null ),
         'testSetRequiredValue test 1'
      );

      $this->validators[ 4 ]->RequiredValue = '2016-06-24 13:13:00';
      $this->assertSame(
         '2016-06-24 13:13:00',
         $this->validators[ 4 ]->RequiredValue,
         'testSetRequiredValue test 2'
      );

      $this->validators[ 4 ]->setRequiredValue( '' );
      $this->assertSame(
         '',
         $this->validators[ 4 ]->RequiredValue,
         'testSetRequiredValue test 3'
      );

   }

   /**
    * @covers Beluga\Validation\Validator::__set
    * @covers Beluga\Validation\Validator::__get
    * @covers Beluga\Validation\DateValidator::getOption
    * @covers Beluga\Validation\DateValidator::setOption
    * @covers Beluga\Validation\Validator::getOption
    * @covers Beluga\Validation\Validator::setOption
    * @covers Beluga\Validation\Validator::setDisplayName
    * @covers Beluga\Validation\DateValidator::setDisplayName
    */
   public function testSetDisplayName()
   {

      $this->assertSame(
         'Ä©Ö…Ü',
         $this->validators[ 4 ]
            ->setOption( 'DisplayName', 'Ä©Ö…Ü' )
            ->getOption( 'DisplayName', null ),
         'testSetDisplayName test 1'
      );

      $this->validators[ 4 ]->DisplayName = '2016-06-24 13:13:00';
      $this->assertSame(
         '2016-06-24 13:13:00',
         $this->validators[ 4 ]->DisplayName,
         'testSetDisplayName test 2'
      );

      $this->validators[ 4 ]->setDisplayName( 'DisplayName' );
      $this->assertSame(
         'DisplayName',
         $this->validators[ 4 ]->DisplayName,
         'testSetDisplayName test 3'
      );

   }

}
