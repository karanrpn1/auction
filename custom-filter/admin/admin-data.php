<?php

add_action('admin_menu', 'my_cool_plugin_create_menu');

function my_cool_plugin_create_menu() {

	add_menu_page('Search Fields', 'Search Fields', 'administrator', 'search-field', 'search_field_settings_page' );
	//call register settings function
	add_action( 'admin_init', 'search_field_plugin_settings' );
	
}

function search_field_plugin_settings() {
	$fields = array();
	$productAttributes = wc_get_attribute_taxonomies(); 
	foreach($productAttributes as $productAttribute ) {
		register_setting( 'my-cool-plugin-settings-group',"filterAttributeStatus_".$productAttribute->attribute_name);
		register_setting( 'my-cool-plugin-settings-group',"filterAttributeType_".$productAttribute->attribute_name);
	}
}

function search_field_settings_page() {  ?>
	<h1>Select the filter fields </h1>
    <form method="post" action="options.php" id="custom-filter-option">
    	<div class="single-continer-heading">
        	<div class="single-continer-inner attributeId"><h3>Attribute Id</h3></div>
            <div class="single-continer-inner"><h3>Attribute Name</h3></div>
            <div class="single-continer-inner"><h3>Status ON / OFF</h3></div> 
            <div class="single-continer-inner"><h3>Filter Type </div>   
   		</div>
    <?php settings_fields( 'my-cool-plugin-settings-group' ); ?>
 	<?php do_settings_sections( 'my-cool-plugin-settings-group' ); ?>
    <?php $fields = array(); ?> 
    <?php  
		$productAttributes = wc_get_attribute_taxonomies(); 
		
		foreach($productAttributes as $productAttribute ) { ?>
			<div class="single-continer">
            	<div class="single-continer-inner attributeId"><h3><?php echo $productAttribute->attribute_id; ?></h3></div>
      			<div class="single-continer-inner"><h3><?php echo $productAttribute->attribute_label; ?></h3></div>
              	<div class="check-continer single-continer-inner">
                	<label class="switch">
                		<input name="<?php echo 'filterAttributeStatus_'.$productAttribute->attribute_name; ?>" type="checkbox" <?php if(get_option('filterAttributeStatus_'.$productAttribute->attribute_name)=='on') { echo "checked";}?>>
                		<span class="switch_slider"></span>
                   	</label> 
              	</div>   
                
                <div class="single-continer-inner">
                	<select name="filterAttributeType_<?=$productAttribute->attribute_name?>">
                    	<option value="1" <?php if(get_option('filterAttributeType_'.$productAttribute->attribute_name)=='1') { echo "selected";}?>>Checkbox</option>
                        <option value="2" <?php if(get_option('filterAttributeType_'.$productAttribute->attribute_name)=='2') { echo "selected";}?>>From - To</option> 
                    </select>
                </div>   
   			</div>
	<?php } ?>
    <?php submit_button(); ?>
    </form>
    
    <div><strong>Note: </strong> Please be careful while selecting filter type as from-to ( Only attribute with integer value are supported in this type ) . </div>
<?php } ?>