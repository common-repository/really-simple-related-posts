<?php
/*
error_reporting(E_ALL);
   ini_set('display_errors', True);
 * 
 */


/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'rsrp_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'rsrp_post_meta_boxes_setup' );

/* Meta box setup function. */
function rsrp_post_meta_boxes_setup() {

  /* Add meta boxes on the 'add_meta_boxes' hook. */
  add_action( 'add_meta_boxes', 'rsrp_add_post_meta_boxes' );
  /* Save post meta on the 'save_post' hook. */
  add_action( 'save_post', 'rsrp_save_post_meta', 10, 2 );
}

function rsrp_add_post_meta_boxes() {

 add_meta_box(
    'rsrp_disable_related_posts',      // Unique ID
    esc_html__( 'Disable Related Posts', 'example' ),    // Title
    'really_simple_related_posts_meta_box',   // Callback function
    'post',         // Admin page (or post type)
    'side',         // Context
    'default'         // Priority
  );
}

/* Display the post meta box. */
function really_simple_related_posts_meta_box( $object, $box ) { ?>

  <?php wp_nonce_field( basename( __FILE__ ), 'really_simple_related_posts_nonce' ); ?>

  <p>
    <label for="rsrp_disable_related_posts"><?php _e( "Don't show related posts", 'example' ); ?></label>
    <br />
   
          <input id="rsrp_disable_related_posts" type="checkbox" name="rsrp_disable_related_posts" 
              value="1" <?php  echo (esc_attr( get_post_meta( $object->ID, 'rsrp_disable_related_posts', true ) )== 1 ? " checked" : ''); ?> />
  </p>
<?php }


/* Save the meta box's post metadata. */
function rsrp_save_post_meta( $post_id, $post ) {

  /* Verify the nonce before proceeding. */
  if ( !isset( $_POST['really_simple_related_posts_nonce'] ) || !wp_verify_nonce( $_POST['really_simple_related_posts_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  /* Get the posted data and sanitize it for use as an HTML class. */
  $new_meta_value = ( isset( $_POST['rsrp_disable_related_posts'] ) ? sanitize_html_class( $_POST['rsrp_disable_related_posts'] ) : '' );

  /* Get the meta key. */
  $meta_key = 'rsrp_disable_related_posts';

  /* Get the meta value of the custom field key. */
  $meta_value = get_post_meta( $post_id, $meta_key, true );

  /* If a new meta value was added and there was no previous value, add it. */
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );

  /* If the new meta value does not match the old value, update it. */
  elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post_id, $meta_key, $new_meta_value );

  /* If there is no new meta value but an old value exists, delete it. */
  elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );
}