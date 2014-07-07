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
    
    }
  
  }
  
}
