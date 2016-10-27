<?php 
/**
 * Plugin Name: LocationsLocator
 * Plugin URI: http://ganeshveer.tk
 * Description: Get live details about the all the countries Names, Capital and population
 * Version: 1.0.0
 * Author: Ganesh Veer
 * Author URI: 
 * License: GPL2
 **/

function themeslug_enqueue_script() {
		echo '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>';
		echo '<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>';
	  	$url = plugin_dir_url( __FILE__ ) . 'getlonglat.js';
	  	$jurl = plugin_dir_url( __FILE__ ) . 'outputjson.js';
	  	$lolocss = plugin_dir_url( __FILE__ ) . 'lolo.css';
	    	echo '<script type="text/javascript" src="'. $url . '"></script>';
	        echo '<script type="text/javascript" src="'. $jurl . '"></script>';
        	wp_enqueue_style('lolocss', $lolocss);
}
add_action( 'wp_enqueue_scripts', 'themeslug_enqueue_script' );

function custom_admin_js(){
    ?>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <script>
    	    function GetLocation() {
            var geocoder = new google.maps.Geocoder();
            var street = document.getElementById("location_details_street_1").value;            
            var city = document.getElementById("location_details_city").value;
            var country = document.getElementById("location_details_country").value;
            var address = street+','+city+','+ country;
            geocoder.geocode({ 'address': address }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var latitude = results[0].geometry.location.lat();
                    var longitude = results[0].geometry.location.lng();
                    //alert("Latitude: " + latitude + "\nLongitude: " + longitude);                    
                    document.getElementById('location_details_long').value=longitude;
                    document.getElementById('location_details_lat').value=latitude;                    
                } else {
                    alert("Request failed.");
                }
            });
        };
	</script>
    <?php
}
add_action('admin_footer', 'custom_admin_js');

function lolo_custom_post_type()
{
    register_post_type('lolo_locations',
                       [
                           'labels'      => [
                               'name'          => __('Locations'),
                               'singular_name' => __('Location'),
                           ],
                           'public'      => true,
                           'has_archive' => true,
                           'supports'              => array( 'title', 'thumbnail', 'revisions', 'custom-fields', ),
	                        'menu_icon'  =>   'dashicons-location-alt',
                           //'show_in_menu'=>'admin.php?post_type=page',
                           'rewrite'     => ['slug' => 'location'], // my custom slug
                       ]
    );
}
add_action('init', 'lolo_custom_post_type');


function location_details_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function location_details_add_meta_box() {
	add_meta_box(
		'location_details-location-details',
		__( 'Location Details', 'location_details' ),
		'location_details_html',
		'lolo_locations',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'location_details_add_meta_box' );


function location_details_html( $post) {
	wp_nonce_field( '_location_details_nonce', 'location_details_nonce' ); ?>
	<p>Address, website, url, email etc</p>
	<p>
		<label for="location_details_street_1"><?php _e( 'Street 1', 'location_details' ); ?></label><br>
		<input type="text" name="location_details_street_1" id="location_details_street_1" value="<?php echo location_details_get_meta( 'location_details_street_1' ); ?>">
	</p>	<p>
		<label for="location_details_street_2"><?php _e( 'Street 2', 'location_details' ); ?></label><br>
		<input type="text" name="location_details_street_2" id="location_details_street_2" value="<?php echo location_details_get_meta( 'location_details_street_2' ); ?>">
	</p>	<p>
		<label for="location_details_city"><?php _e( 'City', 'location_details' ); ?></label><br>
		<input  type="text" name="location_details_city" id="location_details_city" value="<?php echo location_details_get_meta( 'location_details_city' ); ?>">
	</p>	<p>
		<label for="location_details_state"><?php _e( 'State', 'location_details' ); ?></label><br>
		<input type="text" name="location_details_state" id="location_details_state" value="<?php echo location_details_get_meta( 'location_details_state' ); ?>">
	</p>	<p>
		<label for="location_details_zip_postal_code"><?php _e( 'ZIP / Postal Code', 'location_details' ); ?></label><br>
		<input type="text" name="location_details_zip_postal_code" id="location_details_zip_postal_code" value="<?php echo location_details_get_meta( 'location_details_zip_postal_code' ); ?>">
	</p>	<p>
		<label for="location_details_country"><?php _e( 'Country', 'location_details' ); ?></label><br>
		<input onblur="GetLocation()" type="text" name="location_details_country" id="location_details_country" value="<?php echo location_details_get_meta( 'location_details_country' ); ?>">
	</p>	
	<p>
		<label for="location_details_long"><?php _e( 'Longitude', 'location_details' ); ?></label><br>
		<input type="text" name="location_details_long" id="location_details_long" value="<?php echo location_details_get_meta( 'location_details_long' ); ?>" >
	</p>	
<p>
		<label for="location_details_lat"><?php _e( 'Lattitude', 'location_details' ); ?></label><br>
		<input type="text" name="location_details_lat" id="location_details_lat" value="<?php echo location_details_get_meta( 'location_details_lat' ); ?>" >
	</p>	

	<p>
		<label for="location_details_website"><?php _e( 'Website', 'location_details' ); ?></label><br>
		<input type="text" name="location_details_website" id="location_details_website" value="<?php echo location_details_get_meta( 'location_details_website' ); ?>">
	</p>	<p>
		<label for="location_details_email"><?php _e( 'Email', 'location_details' ); ?></label><br>
		<input type="text" name="location_details_email" id="location_details_email" value="<?php echo location_details_get_meta( 'location_details_email' ); ?>">
	</p>	<p>
		<label for="location_details_phone"><?php _e( 'Phone', 'location_details' ); ?></label><br>
		<input type="text" name="location_details_phone" id="location_details_phone" value="<?php echo location_details_get_meta( 'location_details_phone' ); ?>">
	</p><?php
}

function location_details_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['location_details_nonce'] ) || ! wp_verify_nonce( $_POST['location_details_nonce'], '_location_details_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['location_details_street_1'] ) )
		update_post_meta( $post_id, 'location_details_street_1', esc_attr( $_POST['location_details_street_1'] ) );
	if ( isset( $_POST['location_details_street_2'] ) )
		update_post_meta( $post_id, 'location_details_street_2', esc_attr( $_POST['location_details_street_2'] ) );
	if ( isset( $_POST['location_details_city'] ) )
		update_post_meta( $post_id, 'location_details_city', esc_attr( $_POST['location_details_city'] ) );
	if ( isset( $_POST['location_details_state'] ) )
		update_post_meta( $post_id, 'location_details_state', esc_attr( $_POST['location_details_state'] ) );
	if ( isset( $_POST['location_details_zip_postal_code'] ) )
		update_post_meta( $post_id, 'location_details_zip_postal_code', esc_attr( $_POST['location_details_zip_postal_code'] ) );
	if ( isset( $_POST['location_details_country'] ) )
		update_post_meta( $post_id, 'location_details_country', esc_attr( $_POST['location_details_country'] ) );
	if ( isset( $_POST['location_details_long'] ) )
		update_post_meta( $post_id, 'location_details_long', esc_attr( $_POST['location_details_long'] ) );
	if ( isset( $_POST['location_details_lat'] ) )
		update_post_meta( $post_id, 'location_details_lat', esc_attr( $_POST['location_details_lat'] ) );
	if ( isset( $_POST['location_details_website'] ) )
		update_post_meta( $post_id, 'location_details_website', esc_attr( $_POST['location_details_website'] ) );
	if ( isset( $_POST['location_details_email'] ) )
		update_post_meta( $post_id, 'location_details_email', esc_attr( $_POST['location_details_email'] ) );
	if ( isset( $_POST['location_details_phone'] ) )
		update_post_meta( $post_id, 'location_details_phone', esc_attr( $_POST['location_details_phone'] ) );
}
add_action( 'save_post', 'location_details_save' );

/*
	Usage: location_details_get_meta( 'location_details_street_1' )
	Usage: location_details_get_meta( 'location_details_street_2' )
	Usage: location_details_get_meta( 'location_details_city' )
	Usage: location_details_get_meta( 'location_details_state' )
	Usage: location_details_get_meta( 'location_details_zip_postal_code' )
	Usage: location_details_get_meta( 'location_details_country' )
	Usage: location_details_get_meta( 'location_details_website' )
	Usage: location_details_get_meta( 'location_details_email' )
	Usage: location_details_get_meta( 'location_details_phone' )
*/

/*****************************
	Settings Page 
******************************/

add_action( 'admin_menu', 'lolo_add_admin_menu' );
add_action( 'admin_init', 'lolo_settings_init' );

function lolo_add_admin_menu() { 
	add_submenu_page( 'edit.php?post_type=lolo_locations', 
		'Locations Locator', 
		'LL Settings', 
		'manage_options', 
		'locations_locator', 
		'lolo_options_page' );

}

function lolo_settings_init() { 
	register_setting( 'loloPage', 'lolo_settings' );
	add_settings_section(
		'lolo_pluginPage_section', 
		__( 'Edit section to adjust the locations', 'lolo' ), 
		'lolo_settings_section_callback', 
		'loloPage'
	);
	add_settings_field( 
		'lolo_text_field_0', 
		__( 'Radius Options', 'lolo' ), 
		'lolo_text_field_0_render', 
		'loloPage', 
		'lolo_pluginPage_section' 
	);
	add_settings_field( 
		'lolo_select_field_1', 
		__( 'Distance Unit', 'lolo' ), 
		'lolo_select_field_1_render', 
		'loloPage', 
		'lolo_pluginPage_section' 
	);
}

function lolo_text_field_0_render(){ 
	$options = get_option('lolo_settings'); 	?>
	<input type='text' name='lolo_settings[lolo_text_field_0]' value='<?php echo $options['lolo_text_field_0']; ?>'>
<?php }
function lolo_select_field_1_render(){ 
	$options = get_option('lolo_settings'); ?>
	<select name='lolo_settings[lolo_select_field_1]'>
		<option value='1' <?php selected( $options['lolo_select_field_1'], 1 ); ?>>Miles</option>
		<option value='2' <?php selected( $options['lolo_select_field_1'], 2 ); ?>>Kilometers</option>
	</select>
<?php
}
function lolo_settings_section_callback(){ 
	//echo __( 'This section description', 'lolo' );
	 $options = get_option('lolo_settings');
	 echo $options['lolo_text_field_0'];

	 $selected = $options['lolo_select_field_1'];
	 if($selected == 1){
	 	echo "Miles";
	 }else{ echo "Kilometers";}	
}
function lolo_options_page(){ 
	?>
	<form action='options.php' method='post'>
		<h2>Locations Locator Settings</h2>
		<?php
		settings_fields('loloPage');
		do_settings_sections('loloPage');
		submit_button();
		?>
	</form>
	<?php
}

/*************************************
Shortcode	usage : [lolomap]
************************************/
function lolo_mapgenerator(){
	echo "<div id='dvMap'></div>";	
	echo "<div id='allMap'>
	<ul id='allloc'><li id='long'></li><li id='latt'></li>"; 
	?>
	<script>var ulong = document.getElementById('long').innerHTML;
	var ulatt = document.getElementById('latt').innerHTML;
	</script><?php 
		$posts = get_posts( array(
	    	    'post_type'        => 'lolo_locations',
	        	'posts_per_page'   => -1,
	        	'orderby'          => 'title',
	        	'order'            => 'ASC',        
	        	'post_status'      => 'publish'
	    	) );
	    if($posts){
	    	$list = array();
	    foreach ($posts as $post) {
		    	echo '<li  class="loc"><h3>'.$post->post_title.'</h3><span>'
	    		 .get_post_meta($post->ID, 'location_details_street_1', true).', '
	    		 .get_post_meta($post->ID, 'location_details_street_2', true).'<br/>'
	    		 .get_post_meta($post->ID, 'location_details_city', true).', '
	    		 .get_post_meta($post->ID, 'location_details_state', true).'<br/>'	    		 
	    		 .get_post_meta($post->ID, 'location_details_country', true).' - '
	    		 .get_post_meta($post->ID, 'location_details_zip_postal_code', true).'<br/>
	    		 <a href="'.get_post_meta($post->ID, 'location_details_website', true).'" target="_blank">'
			 .get_post_meta($post->ID, 'location_details_website', true).'</a><br/>
			  <a href="mailto'.get_post_meta($post->ID, 'location_details_email', true).'" target="_blank">'
	    		 .get_post_meta($post->ID, 'location_details_email', true).'</a><br/>'
	    		 .get_post_meta($post->ID, 'location_details_phone', true).'<br/>
	    		 Distance :<span class="distance"></span> Km</span>
	    		 <span class="llat">'.get_post_meta($post->ID, 'location_details_lat', true).'</span><br/>
	    		 <span class="llon">'.get_post_meta($post->ID, 'location_details_long', true).'</span></li>';   		 
	    		
	  		  }
	  	}
	echo "</ul></div>";
}
add_shortcode( 'lolomap', 'lolo_mapgenerator' );

function get_all_locations(){	
    $posts = get_posts( array(
        'post_type'        => 'lolo_locations',
        'posts_per_page'   => -1,
        'orderby'          => 'title',
        'order'            => 'ASC',        
        'post_status'      => 'publish'
    ) );

    if($posts){
    $list = array();
    foreach ($posts as $post) {
        $list[] = array(
           // 'id'   => $post->ID,
            'title' => $post->post_title,
            'lng' => get_post_meta($post->ID, 'location_details_long', true),
            'lat' => get_post_meta($post->ID, 'location_details_lat', true),
            'website' => get_post_meta($post->ID, 'location_details_website', true),
            'city' => get_post_meta($post->ID, 'location_details_city', true),
            'contry' => get_post_meta($post->ID, 'location_details_country', true),
            'description' =>get_post_meta($post->ID, 'location_details_street_1', true)
        );
    	}
	}
     echo "<script>/* <![CDATA[ */";
     echo "var markers =".json_encode($list);
     echo "/* ]]> */</script>";       
}
add_action('wp_footer', 'get_all_locations' );
?>
