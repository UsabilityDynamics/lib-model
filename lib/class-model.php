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
        return Loader::define( $args );
      }

    }

  }