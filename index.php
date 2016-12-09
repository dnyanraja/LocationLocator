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
		wp_enqueue_style('lolocss', plugin_dir_url( __FILE__ ) . 'css/lolo.css', array(), '1.0.0', 'all');		
		echo '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>';
		echo '<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>';
	  	//$lolocss = plugin_dir_url( __FILE__ ) . 'lolo.css';
       	wp_register_script('getlonglat', plugin_dir_url(__FILE__).'js/getlonglat.js');
		wp_enqueue_script( 'getlonglat' );
		wp_register_script('outputdistance', plugin_dir_url(__FILE__).'js/outputdistance.js');
		
		$radius = esc_attr(get_option('lolo_radius')); // get the radius options from backend
		preg_match('#\((.*?)\)#', $radius, $match);  // retrieve value of default radius
	
		$translation_array = array(
		'some_string' => __( 'Some string to translate', 'plugin-domain' ),
		'radius' => $match[1]
		);
		wp_localize_script( 'outputdistance', 'lolo_object', $translation_array );

		wp_enqueue_script( 'outputdistance' );
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
//template - add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, callable $function = '' )
add_submenu_page( 'edit.php?post_type=lolo_locations', 'Locations Locator Settings', 'LL Settings', 'manage_options', 'locations_locator_settings', 
		'lolo_options_page' );
}
function lolo_settings_init() { 
	//template - register_setting( $option_group, $option_name, $sanitize_callback ); 
	register_setting( 'loloPage_group', 'lolo_radius' );
	register_setting( 'loloPage_group', 'lolo_unit' );

	//template- add_settings_section( $id, $title, $callback, $page );
	add_settings_section('lolo_pluginPage_section', __( 'Locations Locator display settings', 'lolo' ), 
	'lolo_settings_section_callback', 'loloPage');

	add_settings_field( 'location_radius', __( 'Radius Options', 'lolo' ), 'location_radius_render', 'loloPage', 'lolo_pluginPage_section' );
	add_settings_field( 'location_distance_unit', __( 'Distance Unit', 'lolo' ), 'location_distance_unit_render', 'loloPage', 'lolo_pluginPage_section' );
}
// function to display admin form and process it
function lolo_options_page(){
	include (plugin_dir_path(__FILE__).'/template/display-adminform.php');
}
// function to render section related info
function lolo_settings_section_callback(){ 
	//echo __( 'Edit section to adjust the locations display on frontend', 'lolo' );	
}
//Radius field render
function location_radius_render(){ 
	$radius =esc_attr(get_option('lolo_radius'));
	?>
	<input type="text" name="lolo_radius" value="<?php echo $radius; ?>" />
	<?php
}
//distance field render
function location_distance_unit_render(){ 
	$options = get_option('lolo_unit');
	$items = array("Miles", "Kilometers");
	echo "<select id='drop_down1' name='lolo_unit[dropdown1]'>";
	foreach($items as $item) {
		$selected = ($options['dropdown1']==$item) ? 'selected="selected"' : '';
		echo "<option value='$item' $selected>$item</option>";
	}
	echo "</select>";
}
/*function lolo_options_page(){ 
	?>
	<!--<form action='options.php' method='post'>
		<h2>Locations Locator Settings</h2>
		<?php
		settings_fields('loloPage');
		do_settings_sections('loloPage');
		submit_button();
		?>
	</form> -->
	<?php
}
*/
/*************************************
	Generate Shortcode 
	usage : [lolomap]
************************************/
function lolo_mapgenerator(){
	$unit = get_option('lolo_unit');
	//$radius = esc_attr(get_option('lolo_radius'));		
	//preg_match('#\((.*?)\)#', $radius, $match); // get the default radius
	//'<li id='radius'>".$match[1]."</li>"';

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
	    		 <a href="http://'.get_post_meta($post->ID, 'location_details_website', true).'" target="_blank">'
				 .get_post_meta($post->ID, 'location_details_website', true).'</a><br/>
				 <a href="http://mailto'.get_post_meta($post->ID, 'location_details_email', true).'" target="_blank">'
	    		 .get_post_meta($post->ID, 'location_details_email', true).'</a><br/>'
	    		 .get_post_meta($post->ID, 'location_details_phone', true).'<br/>
	    		 Distance: <span class="distance"> </span> <span id="unit">'.$unit['dropdown1'].'</span>
	    		 <span class="llat">'.get_post_meta($post->ID, 'location_details_lat', true).'</span><br/>
	    		 <span class="llon">'.get_post_meta($post->ID, 'location_details_long', true).'</span></span></li>';	    		 
	    		
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
    ));

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
     die;
}
add_action('wp_footer', 'get_all_locations' );

// Register the script - to pass radius value
//wp_register_script( 'outputdistance', plugin_dir_url(__FILE__).'js/outputdistance.js ); // already registered at top
// Localize the script with new data
/*$radius = esc_attr(get_option('lolo_radius'));		
preg_match('#\((.*?)\)#', $radius, $match); // get the default radius
$translation_array = array(
	'some_string' => __( 'Some string to translate', 'plugin-domain' ),
	'a_value' => '10'
);
wp_localize_script( 'outputdistance', 'object_name', $translation_array );
// Enqueued script with localized data.
wp_enqueue_script( 'outputdistance' );*/
?>