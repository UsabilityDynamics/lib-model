<?php
  /**
   * Model Handler
   *
   * @namespace UsabilityDynamics
   * @module UsabilityDynamics
   * @author potanin@UD
   * @version 0.0.1
   */
  namespace UsabilityDynamics {

    /**
     * Class Model
     *
     * @author team@UD
     * @version 0.1.1
     * @class Model
     * @subpackage Models
     */
    final class Model {

      /**
       * Models Class version.
       *
       * @public
       * @static
       * @property $version
       * @type {Object}
       */
      public static $version = '0.2.1';

      /**
       * Define Structure
       *
       * @author potanin@UD
       * @method define
       * @param $args
       * @return mixed
       */
      public static function define( $args ) {
        return Model\Loader::define( $args );
      }

      /**
       * Return list of defined schemas
       *
       * @author potanin@UD
       * @method getSchema
       *
       * @param null $name
       *
       * @return array
       */
      public static function getSchema( $name = null ) {
        return $name ? Model\Loader::$__schemas[ $name ] : Model\Loader::$__schemas;
      }

    }

  }