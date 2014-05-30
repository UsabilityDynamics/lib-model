<?php
/**
 * Inits Custom Post Types, Taxonimies, Meta
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @author peshkov@UD
 */
namespace UsabilityDynamics\Model {

  if( !class_exists( 'UsabilityDynamics\Model\Loader' ) ) {

    class Loader {

      /**
       * Full Schema Store
       *
       */
      static public $__schemas = array();

      /**
       *
       *
       */
      static private $args = array();

      /**
       *
       *
       */
      static private $structure = array();

      /**
       * Queued Metaboxes
       *
       */
      public static $__metaboxes = array();

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
       * Initialize Metbox Just Once.
       *
       * @return array
       */
      static public function initialize_metabox() {

        $_results = array();

        // Init \RW_Meta_Box defines if needed
        if ( !defined( 'RWMB_VER' ) ) {

          $reflector = new \ReflectionClass( '\RW_Meta_Box' );

          $file = dirname( dirname( $reflector->getFileName() ) ) . '/meta-box.php';

          if( !file_exists( $file ) ) {
            wp_die( 'Meta box ' . $file . ' file not found.' );
          }

          include_once( $file );

        }

        // Stop here if Meta Box class doesn't exist
        if( !class_exists( '\RW_Meta_Box' ) ) {
          wp_die( 'RW_Meta_Box not found' );
        }

        foreach( (array) self::$__metaboxes as $key => $data ) {
          $_results[] = new Meta_Box( $data );
        }

        return $_results;

      }

      /**
       * Define Data Structure
       *
       * @param $args
       * @param $args.title       (array) Readable title.
       * @param $args.schema      (array) URL to schema definition.
       * @param $args.type        (array) Type of structure.
       * @param $args.revision    (array) Version of structure.
       * @param $args.types       (array) Post type definitions
       * @param $args.meta        (array) Meta definitions.
       * @param $args.taxonomies  (array) Taxonomy fields.
       *
       * @return array|bool
       */
      static public function define( $args = array() ) {
        global $wp_post_types;

        self::$args = Utility::parse_args( $args, array(
          'title' => null,
          'types' => array(),
          'meta' => array(),
          'taxonomies' => array()
        ));

        // Deep convert to object.
        self::$args = json_decode( json_encode( self::$args ) );

        $structure = array();

        foreach( (array) self::$args->types as $object_type => $type ) {

          $object_type = sanitize_key( $object_type );

          self::$structure[ $object_type ] = array(
            'meta' => array(),
            'terms' => array(),
          );

          // STEP 1. Register post_type

          // Register Post Type
          $data = ( isset( $type->data ) && ( is_array( $type->data ) || is_object( $type->data ) ) ? ( $type->data ? $type->data : $type[ 'data' ] ) : array() );

          if( !post_type_exists( $object_type ) ) {
            register_post_type( $object_type, self::_prepare_post_type( $object_type, $data ));
          } else {
            // $_extended = Utility::extend( $wp_post_types[ $object_type ], $type->data );
            // $wp_post_types[ $object_type ] = $_extended[0];
          }

          // Set or extend Model definition.
          $wp_post_types[ $object_type ]->__model = Utility::parse_args(
            isset( $wp_post_types[ $object_type ]->__model ) ? $wp_post_types[ $object_type ]->__model : array()
            , array(
            'title' => $args->title,
            'revision' => $args->revision,
            'schema' => $args->schema
          ));

          // STEP 2. Register taxonomy ( and Taxonomy's Post Type if theme supports 'extended-taxonomies' feature )

          // Define post type's taxonomies
          $taxonomies = ( isset( $type->taxonomies ) && is_array( $type->taxonomies ) ) ? $type->taxonomies : array(
            'post_tag',
            'category'
          );

          // Initialize taxonomies if they don't exist and assign them to the current post type
          foreach( (array) $taxonomies as $taxonomy ) {

            if( empty( $taxonomy ) || !is_string( $taxonomy ) ) {
              continue;
            }

            if( !taxonomy_exists( $taxonomy ) ) {
              $data = self::_prepare_taxonomy( $taxonomy );
              register_taxonomy( $data->id, null, $data );
            }

            register_taxonomy_for_object_type( $data->id, $object_type );

            //** Add custom post type for our taxonomy if theme supports extended-taxonomies */
            $taxonomy_post_type = '_tp_' . $data->id;

            if( current_theme_supports( 'extended-taxonomies' ) && !post_type_exists( $taxonomy_post_type ) ) {

              register_post_type( $taxonomy_post_type, array(
                'label' => $data->label,
                'public' => false,
                'rewrite' => false,
                'labels' => array(
                  'name' => $data->label,
                  'edit_item' => 'Edit Term: ' . $data->label
                ),
                'supports' => array( 'title', 'editor' ),
              ));

            }
            if( isset( self::$structure[ $object_type ] ) && isset( self::$structure[ $object_type ]['terms' ] ) && is_array( self::$structure[ $object_type ]['terms' ] ) ) {
              array_push( self::$structure[ $object_type ][ 'terms' ], $data->id );
            }

          }

          $metaboxes = ( isset( $type->meta ) && is_object( $type->meta ) ) ? $type->meta : array();

          foreach( (array) $metaboxes as $key => $data ) {
            self::$__metaboxes[] =  self::_prepare_metabox( $key, $object_type, $data );

            //die( '<pre>' . print_r( $data, true ) . '</pre>' );
          }

        }

        // STEP 4. reset static vars and return structure data.
        $structure = array(
          'post_types' => self::$structure,
          'schema' => self::$args
        );

        // Save to defined Schemas.
        Loader::$__schemas[ self::$args->title ] = self::$args;

        if( did_action( 'init' ) ) {
          // _doing_it_wrong( 'UsabilityDynamics\Model\Loader::define', 'Called too late, should be called on, or before, init action.', self::$version );
          self::initialize_metabox();
        }

        // if( current_action() !== 'init' ) {}

        add_action( 'init', array( '\UsabilityDynamics\Model\Loader', 'initialize_metabox' ), 10 );

        self::$args = array();
        self::$structure = array();

        return $structure;

      }

      /**
       *
       *
       */
      static private function _prepare_metabox( $key, $object_type, $data ) {
        $label = Utility::de_slug( $key );

        $data = Utility::parse_args( $data, array(
          'id'        => $key,
          'title'     => $label,
          'pages'     => array( $object_type ),
          'context'   => 'normal',
          'priority'  => 'high',
          'autosave'  => false,
          'fields'    => array(),
        ));

        // There is no sense to init empty metabox
        if( !is_array( $data->fields ) || empty( $data->fields ) ) {
          return false;
        }

        $fields = array();

        foreach( $data->fields as $field ) {
          array_push( self::$structure[ $object_type ][ 'meta' ], $field );
          $fields[] = self::_prepare_metafield( $field );
        }

        // die( '<pre>' . print_r( $fields, true ) . '</pre>' );
        $data->fields = $fields;

        return $data;

      }

      /**
       *
       *
       */
      static private function _prepare_metafield( $key ) {
        $data = isset( self::$args->meta->{$key} ) ? self::$args->meta->{$key} : array();

        $data = Utility::parse_args( $data, array(
          'id'    => $key,
          'name'  => isset( $data->name ) ? $data->name : Utility::de_slug( $key ),
          'type'  => 'text'
        ));

        return $data;

      }

      /**
       *
       *
       */
      static private function _prepare_taxonomy( $key ) {
        $data = isset( self::$args->taxonomies->{$key} ) && is_object( self::$args->taxonomies->{$key} ) ? self::$args->taxonomies->{$key} : array();

        $data = Utility::parse_args( $data, array(
          'id' => $key,
          'hierarchical' => true,
          'public' => true,
          'show_ui' => true,
          'label' => isset( $data->name ) ? $data->name : Utility::de_slug( $key ),
        ));

        return $data;

      }

      /**
       *
       *
       */
      static private function _prepare_post_type( $key, $args = array() ) {

        $args = wp_parse_args( $args, array(
          'label' => Utility::de_slug( $key ),
          'exclude_from_search' => false
        ));

        return $args;

      }

    }

  }

}



