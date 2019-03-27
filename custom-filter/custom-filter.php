<?php
/*
Plugin Name: Custom Filter
Plugin URI: http://www.helpfulinsight.in
Description: Search and Filtering system for Product
Author: Karan
Author URI: http://www.karanrupani.com/
Version: 1.0
*/
 
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );


if (!defined('CUS_SEARCH_THEME_DIR'))
    define('CUS_SEARCH_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());


if (!defined('CUS_SEARCH_PLUGIN_NAME'))
    define('CUS_SEARCH_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('CUS_SEARCH_PLUGIN_DIR'))
    define('CUS_SEARCH_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . CUS_SEARCH_PLUGIN_NAME);
	
	
if (!defined('CUS_SEARCH_PLUGIN_URL'))
    define('CUS_SEARCH_PLUGIN_URL', WP_PLUGIN_URL . '/' . CUS_SEARCH_PLUGIN_DIR);

if ( ! class_exists( 'cussearchandfilter' ) ) {
	class cussearchandfilter {
		public function __construct()	{
			// Add styles			
			add_action( 'wp_enqueue_scripts', array($this, 'of_enqueue_styles') );
			add_action( 'admin_enqueue_scripts', array($this, 'of_enqueue_admin_ss') );
		}
		
		public function of_enqueue_styles()	{
			wp_enqueue_style( 'cus_custom_css', plugins_url('style.css', __FILE__) );			
		}
		 
		public function of_enqueue_admin_ss($hook)	{
			wp_enqueue_style( 'search-field-style', plugins_url('/admin/css/admin-style.css', __FILE__) , false, '1.0.0' );
			
		}
	} 
}

if ( class_exists( 'cussearchandfilter' ) ) {
	global $cussearchandfilter;
	$cussearchandfilter	= new cussearchandfilter();
}

/* ADD ADMIN MENU TO THEME */
require_once(CUS_SEARCH_PLUGIN_DIR."/admin/admin-data.php");
 
   
/* SHORTOCDE FOR FILTER */
function cus_filter_shortcode()	{
	require CUS_SEARCH_PLUGIN_DIR.'/filter-template.php';		
}
add_shortcode('cus_custom_search_filter', 'cus_filter_shortcode');

add_action('woocommerce_archive_description','before_loop_add_filter');
function before_loop_add_filter() {
	echo do_shortcode('[cus_custom_search_filter]');
}

if(isset($_GET['filterProduct'])) {
	function new_product_query( $q ){
		//$taxArray = array('relation' => 'AND');
		$taxArray  = (array) $q->get( 'tax_query' );	
		if(isset($_GET['mainCategory'])) {
			if($_GET['mainCategory']!="all") {
				$taxArray[] = array('taxonomy' => 'product_cat' ,'field' => 'slug','terms' => $_GET['mainCategory'],'operator'=> 'IN');
			}			
		}
		$productAttributes = wc_get_attribute_taxonomies(); 
		foreach($productAttributes as $productAttribute ) {
			
			if(get_option("filterAttributeStatus_".$productAttribute->attribute_name)=="on") 		{	
						
				if(get_option("filterAttributeType_".$productAttribute->attribute_name)==2) {					
					$fromValue = $_GET['pa_'.$productAttribute->attribute_name.'_from'];
					$toValue = $_GET['pa_'.$productAttribute->attribute_name.'_to'];
					 
					$filterValue = array();
					for($i = $fromValue ; $i <= $toValue; $i++ ) {
						$filterValue[] = $i;
					}
					$taxArray[] = array('taxonomy' => 'pa_'.$productAttribute->attribute_name ,'field' => 'slug','terms' => $filterValue,'operator'=> 'IN');
					
				}
				
				else {				
					if(isset($_GET['pa_'.$productAttribute->attribute_name])) {						
						
						$taxArray[] = array('taxonomy' => 'pa_'.$productAttribute->attribute_name ,'field' => 'slug','terms' => $_GET['pa_'.$productAttribute->attribute_name],'operator'=> 'IN');
						   
					}				
					
				}
				
			} 
		}		
				
		$q->set('tax_query',$taxArray ); 
		
		//echo "<pre>";print_r($q);echo "</pre>";
		//die();
	}
	add_action( 'woocommerce_product_query', 'new_product_query' ); 
}   
?>