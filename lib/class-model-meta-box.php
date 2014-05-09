<?php
/**
 * Model Handler
 *
 * @namespace UsabilityDynamics
 * @module UsabilityDynamics
 * @author potanin@UD
 * @version 0.0.1
 */
namespace UsabilityDynamics\Model {

  if( !class_exists( 'UsabilityDynamics\Model\Meta_Box' ) ) {

    /**
     * Class Models
     *
     * @author team@UD
     * @version 0.1.1
     * @class Utility
     * @subpackage Models
     */
    class Meta_Box extends \RW_Meta_Box {

      /**
       * Models Class version.
       *
       * @public
       * @static
       * @property $version
       * @type {Object}
       */
      public static $version = '0.2.0';

      /**
       * @param $data     array
       * @param $data.id  string
       */
      public function __construct( $data = array() ) {

        try {

          $_instance = parent::__construct( json_decode( json_encode( $data ), true ) );

        } catch( Exception $e ) {}

        return $_instance;

      }

    }

  }

}