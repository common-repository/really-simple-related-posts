<?php

//error_reporting(E_ALL);
//ini_set('display_errors', True);



add_action( 'admin_menu', 'rja_really_simple_related_posts_create_menu' );

function rja_really_simple_related_posts_create_menu() {
	
	//create a submenu under Settings!!! - will call rja_related_posts_category_tag_settings_page
	add_options_page( 'Really Simple Related Posts', 'Really Simple Related Posts', 'manage_options', __FILE__, 'rja_really_simple_related_posts_settings_page' );
	
}

function rja_really_simple_related_posts_settings_page(){
 
    if ( isset($_POST['submit']) ) {
       
        if( !isset( $_POST['cat_post_type'] ) ) {
            $options=array();
        } else {
            $options = $_POST['cat_post_type'];
        }
        

        update_option('rja_related_posts_category_tag', $options);
   } else {
       $options = array();
   }
    
    
    $args='';
    $categories = get_categories( $args );        
    
    $option_data = get_option("rja_related_posts_category_tag");  
    
    echo '<div class="wrap">';
    //echo '
        echo '<h2>Really Simple Related Posts</h2>';
        
        ?>
                <p>Like this plugin? Please donate so we can continue making more great plugins.</p>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="PATFLGNC2KXFQ">
		<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
		</form>
		
        <form method="post" action="" >
        <table class="form-table">
                
            <tr valign="top">
                    <th>
                            Category
                    </th>
                    <th>
                            Show Related Posts?
                    </th>
                    <th>
                            Show Category link?
                    </th>
                    
                   
            </tr>
        
            <?php
        
               
                foreach($categories as $category){
                    
                               
                    $show_post_type = "none";
                    $show_cat_link = 0;
                    $show_tag_link = 0;
                    
                   if(is_array($option_data)){
                       foreach($option_data  as $key=> $option){                       
                        
                            if($key == $category->term_id){

                                $show_post_type = (!empty( $option['type']) ? $option['type'] : '');
                                $show_cat_link = (!empty( $option['show_cat_link']) ? $option['show_cat_link'] : '');
                                $show_tag_link = (!empty( $option['show_tag_link']) ? $option['show_tag_link'] : '');                        


                            }
                        }
                   } else {
                       $show_post_type = '';
                        $show_cat_link = '';
                        $show_tag_link = '';
                   }
                    
                    
                    
                    ?>
                    <tr valign="top">
                        <td>
                                <?php echo $category->name; ?>
                        </td>
                        <td>               
                                <select name="cat_post_type[<?php echo $category->term_id; ?>][type]" id="cat_post_type[<?php echo $category->term_id; ?>]"> 
                                    <option value="none"> </option>
                                    <?php
                                        if ($show_post_type== "none"){
                                            echo "<option value='none' selected >None</option>"; 
                                            echo "<option value='by_category' >By Category</option>";
                                            echo "<option value='by_tag' >By Tag</option>";    
                                            echo "<option value='by_category_and_tag' >By Category And Tag</option>"; 
                                        } elseif ($show_post_type== "by_category") {
                                            echo "<option value='none'  >None</option>"; 
                                            echo "<option value='by_category' selected >By Category</option>"; 
                                            echo "<option value='by_tag' >By Tag</option>";     
                                            echo "<option value='by_category_and_tag' >By Category And Tag</option>"; 
                                        } elseif ($show_post_type== "by_tag") {
                                            echo "<option value='none'  >None</option>"; 
                                            echo "<option value='by_category' >By Category</option>"; 
                                            echo "<option value='by_tag' selected >By Tag</option>"; 
                                            echo "<option value='by_category_and_tag' >By Category And Tag</option>"; 
                                        } elseif ($show_post_type== "by_category_and_tag") {
                                            echo "<option value='none'  >None</option>"; 
                                            echo "<option value='by_category' >By Category</option>"; 
                                            echo "<option value='by_tag' selected >By Tag</option>"; 
                                            echo "<option value='by_category_and_tag' selected >By Category And Tag</option>"; 
                                           
                                 
                                        } else {
                                            echo "<option value='none' selected >None</option>"; 
                                            echo "<option value='by_category' >By Category</option>";
                                            echo "<option value='by_tag' >By Tag</option>";    
                                            echo "<option value='by_category_and_tag' >By Category And Tag</option>"; 
                                        }
                                    ?>
                                </select>
                        </td>
                        <td>
                             <input id="show_cat_link_<?php echo $category->term_id ?>" type="checkbox" name="cat_post_type[<?php echo $category->term_id; ?>][show_cat_link]" 
                                       value="1" <?php  echo ($show_cat_link== 1 ? " checked" : ''); ?> />
                            
                        </td>
                        
                        
                        
                      
                    </tr>     
        
        <?php
            } //foreach($categories as $category)
          ?>  
            </table>

                                <p class="submit">

				 <?php submit_button(); ?> 

				</p>
      <?php
       
        
   echo '</form>';
   echo '</div>';
   
    
}