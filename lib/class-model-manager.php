<?php
/**
 * Model Manager class
 * Manage custom post types, taxonomies, meta data
 * 
 * @author peshkov@UD
 * @version 0.1.0
 * @package UsabilityDynamics
 * @subpackage lib-model
 */
namespace UsabilityDynamics\Model {

  if( !class_exists( 'UsabilityDynamics\Model\Manager' ) ) {

    class Manager {
    
      /**
       * The list of existing custom structure
       *
       * @type array
       * @author peshkov@UD
       */
      private static $structure = array();
      
      private static $data = array();
      
      /**
       * Returns structure.
       * should be called after 'init' action.
       *
       * @return array
       * @author peshkov@UD
       */
      static public function get() {
        if( function_exists( 'did_action' ) && did_action( 'init' ) && current_filter() !== 'init' ) {
          return self::$structure;
        }
        return false;
      }
      
      /**
       * Adds
       *
       * @author peshkov@UD
       */
      static public function set( $data ) {
      
        if( function_exists( 'did_action' ) && did_action( 'init' ) && current_filter() !== 'init' ) {
          _doing_it_wrong( __FUNCTION__, __( 'method must be called before or during \'init\' action.' ), '1.0' );
        }
        
        //** Initialize our structure at last moment. */
        add_action( 'init', array( __CLASS__, 'init' ), 999 );
      
        array_push ( self::$data, wp_parse_args( $data, array(
          'priority' => 10,
          'types' => array(),
          'meta' => array(),
          'taxonomies' => array()
        ) ) );
        
        return true;
        
      }
      
      /**
       * Initialize our custom post_type structure.
       * Note: must not be called directly.
       *
       * @author peshkov@UD
       */
      static public function init() {
        
        if( current_filter() !== 'init' ) {
          _doing_it_wrong( __FUNCTION__, __( 'method must be called during \'init\' action.' ), '1.0' );
        }
        
        usort( self::$data, create_function( '$a,$b', 'if ($a[\'priority\'] == $b[\'priority\']) { return 0; } return ($a[\'priority\'] < $b[\'priority\']) ? 1 : -1;' ) );
        
        $data = array();
        foreach( self::$data as $d ) {
          $data = Utility::extend( $data, $d );
        }
        // Remove priority, it is not needed anymore
        unset( $data[ 'priority' ] );
        
        self::$structure = Loader::define( $data );
      }
      
    }

  }

}