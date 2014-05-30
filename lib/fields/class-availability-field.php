<?php
/**
 *
 */
if( !class_exists( 'RWMB_Availability_Field' ) && class_exists( 'RWMB_Field' ) ) {

  class RWMB_Availability_Field extends RWMB_Field {

    /**
     * Get meta values to save
     * Save terms in custom field, no more by setting post terms
     * Save in form of comma-separated IDs
     *
     * @param mixed $new
     * @param mixed $old
     * @param int   $post_id
     * @param array $field
     *
     * @return string
     */
    static function value( $new, $old, $post_id, $field )
    {
      return "WHOO HOO";
    }

    /**
     * Save meta value
     *
     * @param mixed $new
     * @param mixed $old
     * @param int   $post_id
     * @param array $field
     *
     * @return string
     */
    static function save( $new, $old, $post_id, $field )
    {
      if ( $new )
        update_post_meta( $post_id, $field['id'], $new );
      else
        delete_post_meta( $post_id, $field['id'] );
    }

    /**
     * Standard meta retrieval
     *
     * @param int   $post_id
     * @param bool  $saved
     * @param array $field
     *
     * @return array
     */
    static function meta( $post_id, $saved, $field )
    {
      $meta = get_post_meta( $post_id, $field['id'], true );
      $meta = array_map( 'intval', array_filter( explode( ',', $meta . ',' ) ) );
      return $meta;
    }
  }

}