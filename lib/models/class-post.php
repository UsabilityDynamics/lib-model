<?php
/**
 * Post Model
 *
 * @namespace UsabilityDynamics
 * @module UsabilityDynamics
 * @author peshkov@UD
 * @version 0.1.0
 */
namespace UsabilityDynamics\Model {

  if( !class_exists( 'UsabilityDynamics\Model\Post' ) ) {

    class Post extends \WPModel\Post {
      
      protected $_structure = null;
      
      /**
       * 
       *
       */
      public function __construct( $post  ) {
        parent::__construct( $post );
        
        if( $this->_id ) {
          $structure = \UsabilityDynamics\Model::get( 'structure' );
          if( !empty( $structure ) && is_array( $structure ) && key_exists( $this->post_type, $structure ) ) {
            $this->_structure = $structure[ $this->post_type ];
          }
        }
        
      }
      
      /**
       * Returns structure
       *
       * @param string $type
       * @return array
       */
      public function getStructure( $type = null ) {
        $structure = $this->_structure ? $this->_structure : array();
        $structure = wp_parse_args( $structure, array(
          'meta' => array(),
          'terms' => array(),
        ) );
        if( !empty( $type ) ) {
          if( key_exists( $type, $structure ) ) {
            $structure = $structure[ $type ];
          } else {
           return false;
          }
        }
        return $structure;
      }
      
      /**
       * Returns all meta data
       *
       * @return array
       */
      public function getMeta() {
        $metas = $this->getStructure( 'meta' );
        
        $data = array();
        foreach( $metas as $meta ) {
          $data[ $meta ] = $this->meta->{$meta};
        }
        return $data;
      }
      
      /**
       * Returns all terms data
       *
       * @return array The list of WPModel\Term objects
       */
      public function getTerms() {
        $terms = $this->getStructure( 'terms' );
        return $this->terms( array( 'taxonomy' => $terms ) );
      }
    
    }
  
  }
  
}
