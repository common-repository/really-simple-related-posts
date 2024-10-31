<?php

/**
 * Plugin Name: Really Simple Related Posts
 * Author: Robert Austin
 * Author URI: 
 * Description: Display Related Posts
 * Version: 1.0.4
 * License: GPL2+
 * Text Domain: related-posts
 * Domain Path: /languages/
 *
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

//error_reporting(E_ALL);
//ini_set('display_errors', True);

//style sheet
add_action( 'wp_enqueue_scripts', 'rja_safely_add_stylesheet' );

/**
 * Add stylesheet to the page
 */
function rja_safely_add_stylesheet() { 
    wp_enqueue_style( 'really-simple-related-posts', plugins_url( 'css/rsrp_styles.css', __FILE__ ), array() );   

}

// Admin
if( is_admin() ){
	require_once dirname( __FILE__ ) . '/admin/admin.php';
}

//includes
require_once dirname( __FILE__ ) . '/inc/post_meta.php';

add_filter( 'the_content', 'display_really_simple_related_posts', 88 );

function display_really_simple_related_posts($the_content) {
        
        
    
        if ( is_single() && !is_home() && !is_page() ) {
            
        $disable_related_posts = get_post_meta( get_the_ID(), 'rsrp_disable_related_posts', true );
        
        if($disable_related_posts == 1){
            return $the_content;
        }   
        
        
        
	$option_data = get_option("rja_related_posts_category_tag");
        $postID =  get_the_ID();       
       
        $cat_post_type=array();
        
        
        foreach((get_the_category()) as $cat_key =>$category) { 
           $catID = $category->term_id . ' ';
            if (is_array($option_data)){
                foreach($option_data as $opt_key => $option){
                
                    if($opt_key == $category->term_id ){
                       //print('<pre>'); print_r($option); print('</pre>');
                       $cat_post_type[$opt_key] = $option;

                    }
                }
            }            
        }
    
        $posts_to_link_to = array();
   
        
        foreach($cat_post_type as $key => $value){
            $show_cat_link = (!empty( $value['show_cat_link']) ? $value['show_cat_link'] : '');            
            if($value['type'] == "by_category"){
                                
              
                $args=array(
                  
                    'category_name'=> get_the_category_by_ID( $key ),
                    'tag' => '',
                    'orderby'=>'title',
                    'order' => 'ASC',  
                    'showposts'=>100,
                    'post__not_in' => array(get_the_ID()),
                    'caller_get_posts'=>1
                  );
                
               
                $my_query = new WP_Query($args);
                
                if( $my_query->have_posts() ) {
                    
                   
                    while ($my_query->have_posts()) : $my_query->the_post(); 
                                         
                        $posts_to_link_to[get_the_ID()]['title']=get_the_title();
                        $posts_to_link_to[get_the_ID()]['cat_id'][$key]['id']=$key;
                        $posts_to_link_to[get_the_ID()]['cat_id'][$key]['show']=$show_cat_link;
                                 
                    endwhile;
                    wp_reset_query();  // Restore global post data stomped by the_post().
                } 
            } elseif($value['type'] == "by_tag"){
                
                $posttags = get_the_tags();
                //print('<pre>'); print_r($posttags); print('</pre>');
                if ($posttags) {
                    foreach($posttags as $tag) {
                       
                        
                        $args=array(                           
                          'tag_id' => $tag->term_id,
                            'orderby'=>'title',
                            'order' => 'ASC',  
                          'showposts'=>100,
                            'post__not_in' => array(get_the_ID()),
                          'caller_get_posts'=>1
                        );
                        
                        $my_query = new WP_Query($args);
                     
                        if( $my_query->have_posts() ) {
                         
                            while ($my_query->have_posts()) : $my_query->the_post();
                            
                                           
                                $posts_to_link_to[get_the_ID()]['title']=get_the_title();                               
                                $posts_to_link_to[get_the_ID()]['tag_id'][$tag->term_id]=$tag->term_id;
                                                                       
                            endwhile;
                            wp_reset_query();  // Restore global post data stomped by the_post().
                        }           
                      
                    }
                    
                   
                }
            } elseif($value['type'] == "by_category_and_tag"){
                
                //By Category
                $args=array(
                    
                    'category_name'=> get_the_category_by_ID( $key ),
                    'tag' => '',
                    'orderby'=>'title',
                    'order' => 'ASC',  
                    'showposts'=>100,
                    'post__not_in' => array(get_the_ID()),
                    'caller_get_posts'=>1
                  );
                
                //get posts
                $my_query = new WP_Query($args);
                
                if( $my_query->have_posts() ) {
                    
                   
                    while ($my_query->have_posts()) : $my_query->the_post(); 
                        
                                    
                        $posts_to_link_to[get_the_ID()]['title']=get_the_title();
                        $posts_to_link_to[get_the_ID()]['cat_id'][$key]['id']=$key;
                        $posts_to_link_to[get_the_ID()]['cat_id'][$key]['show']=$show_cat_link;
                                        
                    endwhile;
                    wp_reset_query();  // Restore global post data stomped by the_post().
                } 
                
                
                //by tag
                $posttags = get_the_tags();
                if ($posttags) {
                    foreach($posttags as $tag) {
                       
                    
                        $args=array(                           
                          'tag_id' => $tag->term_id,
                            'orderby'=>'title',
                            'order' => 'ASC',  
                          'showposts'=>100,
                            'post__not_in' => array(get_the_ID()),
                          'caller_get_posts'=>1
                        );
                        
                        $my_query = new WP_Query($args);
                        if( $my_query->have_posts() ) {
                            
                            while ($my_query->have_posts()) : $my_query->the_post();
                            
                                //fill array                        
                                $posts_to_link_to[get_the_ID()]['title']=get_the_title();                               
                                $posts_to_link_to[get_the_ID()]['tag_id'][$tag->term_id]=$tag->term_id;
                                              
                            endwhile;
                            wp_reset_query();  // Restore global post data stomped by the_post().
                        }           
                      
                    }
                }//($posttags)
               
               
            } //elseif($value['type'] == "by_category_and_tag")
        }//foreach($cat_post_type as $key => $value)
	 
            
            

    
 
        
        if (count($posts_to_link_to) > 0){
            $the_content = $the_content.'<h2>Related Posts</h2>';
        }
        //loop through array and create content
        foreach($posts_to_link_to as $key => $value){
            //key is post id          
            $the_content = $the_content."<a href=".get_permalink($key).">".get_the_title($key)." </a>" ;
            foreach($value as $value_key => $value_value){
                if($value_key=='cat_id'){
                    $the_content = $the_content.'<span class="rsrp-tag-links">';
                    foreach($value_value as $cat_id){
                        //show cat link
                        if($cat_id['show']==1){
                            $the_content = $the_content.'<a rel="tag" href="'.get_category_link($cat_id['id']).'">'.get_the_category_by_ID($cat_id['id']).'</a>';  
                        }
                    }
                    $the_content = $the_content.'</span>';
                }elseif($value_key=='tag_id'){
                    $the_content = $the_content.'<span class="rsrp-tag-links">';
                    foreach($value_value as $tag_id){
                        $tag = get_tag($tag_id);
                        $the_content = $the_content.'<a rel="tag" href="'.get_tag_link($tag_id).'">'.$tag->name.'</a>';  
                       
                    }
                    $the_content = $the_content.'</span>';
                }
            }
            $the_content = $the_content."<br>";
        }         
        
        
    
        }
        
        
    return $the_content;
        
}

