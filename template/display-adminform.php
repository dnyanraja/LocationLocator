<h1>Locations Locator Options</h1>
<?php settings_errors(); ?>
<?php 
	/* $radius =esc_attr(get_option('lolo_radius'));
	 echo $radius.'<br/>';
	 $options = get_option('lolo_unit');
	 echo $options['dropdown1'];	 */
?>
<form method="post" action="options.php" class="ganesh-general-form">
<?php settings_fields('loloPage_group'); ?>
<?php do_settings_sections('loloPage'); ?>
<?php submit_button('Save Changes', 'primary', 'btnSubmit'); ?>
</form>
