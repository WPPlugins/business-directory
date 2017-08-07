<?php
/*
Plugin Name: Business Directory
Plugin URI: http://businessdirectory.squarecompass.com/
Description: The Business Directory plugin for Wordpress is an easy way to host a free directory page for your readers, affiliates, advertisers, community, club members, etc. Allow them to submit a simple advertisement listing for themselves on your blog.
Version: 0.8.6.1 Beta
Author: Square Compass
Author URI: http://squarecompass.com
*/

require_once(dirname(__FILE__)."/config.php"); //Load Biz-Directory Config File
/*
* Add functions
*/
//Add Actions
add_action('wp_head','bizdir_js_header'); //Add Listing Form Header Ajax Call
add_action('admin_menu','bizdir_navigation'); //Add Directory Tab in the menu
add_action('admin_print_scripts','bizdir_js_admin_header'); //Add Ajax to the admin side
add_action('wp_ajax_bizdir_edit_listing','bizdir_edit_listing' );
add_action('wp_ajax_bizdir_update_listing','bizdir_update_listing' );
add_action('wp_ajax_bizdir_show_manager_home','bizdir_show_manager_home' );
add_action('wp_ajax_bizdir_change_listing_status','bizdir_change_listing_status' );
add_action('wp_ajax_bizdir_delete_listing','bizdir_delete_listing' );
add_action('wp_ajax_bizdir_add_category', 'bizdir_add_category' );
add_action('wp_ajax_bizdir_edit_category', 'bizdir_edit_category');
add_action('wp_ajax_bizdir_show_category_home', 'bizdir_show_category_home');
add_action('wp_ajax_bizdir_update_category', 'bizdir_update_category');
add_action('wp_ajax_bizdir_delete_category', 'bizdir_delete_category');
//Add Short Code
add_shortcode("bizdir_addform","bizdir_addform_shortcode"); //Add ShortCode for "Add Form"
add_shortcode("bizdir_directory","bizdir_directory_shortcode"); //Add ShortCode for "Directory"
//Register Hooks
register_activation_hook(__FILE__,'bizdir_install');
//Add Javascript
wp_register_script('biz_dir_main_js',BIZDIRCALLBACK.'/main.js');
wp_enqueue_script("biz_dir_main_js");
/*
*  Set admin Messages
*/
$bizdir_categories = $wpdb->get_results("SELECT * FROM ".BIZDIRCATTABLE.";");
if(empty($bizdir_categories) || !is_array($bizdir_categories) || count($bizdir_categories) < 1)
	add_action('admin_notices','bizdir_warning_nocat');
elseif(count($bizdir_categories) == 1 && $bizdir_categories[0]->category == "General" && $bizdir_categories[0]->description == "")
	add_action('admin_notices','bizdir_warning_defaultcat');
//Warning functions
function bizdir_warning_nocat() {
	echo 
		"<div id='bizdir_warning' class='updated fade'>".
			"You must add at least one category to Business Directory before your users will be able to add their businesses.".
		"</div>"
	;
}
function bizdir_warning_defaultcat() {
	echo 
		"<div id='bizdir_warning' class='updated fade'>".
			"You have not set up your categories for Business Directory.".
		"</div>"
	;
}
/*
*  Insatllation Script
*/
function bizdir_install() {
	global $wpdb;
	global $bizdir_version_var;
	$sql = "";
	$cur_version = get_option("bizdir_version");
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	//The Listings table is where all the user imputed data is stored
	if($wpdb->get_var("show tables like '".BIZDIRDBTABLE."'") != BIZDIRDBTABLE) {
		$wpdb->query( 
			"CREATE TABLE ".BIZDIRDBTABLE." (".
				"listing_id int(11) NOT NULL AUTO_INCREMENT,".
				"category_id int(11) NOT NULL DEFAULT '1',".
				"date_created datetime NULL DEFAULT NULL,".
				"status tinyint(1) DEFAULT '0' NOT NULL,".
				"name varchar(100) NULL DEFAULT NULL,".
				"email varchar(100) NULL DEFAULT NULL,".
				"company_name varchar(100) NULL DEFAULT NULL,".
				"company_keywords varchar(100) NULL DEFAULT NULL,".
				"company_description text NULL DEFAULT NULL,".
				"company_url varchar(100) NULL DEFAULT NULL,".
				"company_email varchar(100) NULL DEFAULT NULL,".
				"company_phone varchar(100) NULL DEFAULT NULL,".
				"company_street1 varchar(100) NULL DEFAULT NULL,".
				"company_street2 varchar(100) NULL DEFAULT NULL,".
				"company_city varchar(100) NULL DEFAULT NULL,".
				"company_state varchar(100) NULL DEFAULT NULL,".
				"company_zip varchar(100) NULL DEFAULT NULL,".
				"company_country varchar(100) NULL DEFAULT NULL,".
				"PRIMARY KEY (listing_id)".
			");"
		);
	} elseif(empty($cur_version) || $cur_version < "0.8.2 Beta") //If we are working with a previous install, we need to alter the existing table
		$wpdb->query("ALTER TABLE ".BIZDIRDBTABLE." ADD COLUMN category_id int(11) NOT NULL DEFAULT '1' AFTER listing_id;");
	//The Categories table stores the categories
	if($wpdb->get_var("show tables like '".BIZDIRCATTABLE."'") != BIZDIRCATTABLE) {
		$wpdb->query( 
			"CREATE TABLE ".BIZDIRCATTABLE." (".
				"category_id int(11) NOT NULL AUTO_INCREMENT,".
				"category varchar(100) NOT NULL,".
				"description text NULL DEFAULT NULL,".
				"hide tinyint(1) NOT NULL DEFAULT '0',".
				"PRIMARY KEY (category_id)".
			");"
		);
		$wpdb->query("INSERT INTO ".BIZDIRCATTABLE." (category,hide) VALUES ('General',0);");
	}
	//Update Version
	if(!add_option("bizdir_version",$bizdir_version_var));
		update_option("bizdir_version",$bizdir_version_var); 
}
/*
*  Set Header for Ajax calls
*/
function bizdir_js_header() {
	wp_print_scripts(array('sack'));//Include Ajax SACK library  
	?>
		<script>
			function bizdir_add_listing(name,email,cName,description,keywords,website,cEmail,phone) { //Add Form Ajax Call
				//Deactivate submit button and display processing message
				document.getElementById('bizdir_submit').disabled = true;
				var submit_message = document.getElementById('bizdir_submit_message');
				submit_message.className = "bizdir_message";
				submit_message.innerHTML = "Submitting Form, Please Wait...";
				//Clear inputs with Auto Text
				bizdir_clearAllAutoFill();
				//Build SACK Call
				var mysack = new sack("<?php echo BIZDIRCALLBACK; ?>requests.php");
				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar("action","AddListing");
				mysack.setVar("category_id", document.getElementById("bizdir_category_id").value);
				mysack.setVar("name",document.getElementById("bizdir_name").value);
				mysack.setVar("email",document.getElementById("bizdir_email").value);
				mysack.setVar("cName",document.getElementById("bizdir_cName").value);
				mysack.setVar("description",document.getElementById("bizdir_description").value);
				mysack.setVar("keywords",document.getElementById("bizdir_keywords").value);
				mysack.setVar("website",document.getElementById("bizdir_website").value);
				mysack.setVar("cEmail",document.getElementById("bizdir_cEmail").value);
				mysack.setVar("phone",document.getElementById("bizdir_phone").value);
				mysack.setVar("street1",document.getElementById("bizdir_street1").value);
				mysack.setVar("street2",document.getElementById("bizdir_street2").value);
				mysack.setVar("city",document.getElementById("bizdir_city").value);
				mysack.setVar("state",document.getElementById("bizdir_state").value);
				mysack.setVar("zip",document.getElementById("bizdir_zip").value);
				mysack.setVar("country",document.getElementById("bizdir_country").value);
				mysack.onError = function() { alert('An ajax error occured while adding your listing. Please reload the page and try again.') };
				mysack.runAJAX();//excecute
				return true;
			}
			
			function bizdir_search_listings() { //Search Ajax Call
				var search_term = document.getElementById('bizdir_search_term');
				if(search_term.value == "" || search_term.value == null)
					return;
				//Deactivate submit button and display processing message
				document.getElementById('bizdir_search').disabled = true;
				var submit_message = document.getElementById('bizdir_messages');
				submit_message.className = "bizdir_message";
				submit_message.innerHTML = "Searching Listings, Please Wait...";
				//Build SACK Call
				var mysack = new sack("<?php echo BIZDIRCALLBACK; ?>requests.php");
				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar("action","SearchListings");
				mysack.setVar("searchTerms",search_term.value);
				mysack.onError = function() { alert('An ajax error occured while searching. Please reload the page and try again.') };
				mysack.runAJAX();//excecute
				return true;
			}
			
			function bizdir_change_listings_page(offset) { //Jump to the appropriate page in the directory Ajax Call
				//Build SACK Call
				var mysack = new sack("<?php echo BIZDIRCALLBACK; ?>requests.php");
				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar("action","ChangePage");
				mysack.setVar("offset",offset);
				mysack.onError = function() { alert('An ajax error occured. Please reload the page and try again.') };
				mysack.runAJAX();//excecute
				return true;
			}

            function bizdir_sort_categories(category) { //Jump to the appropriate page in the directory Ajax Call
				//Build SACK Call
                var category = category.value;
                var mysack = new sack("<?php echo BIZDIRCALLBACK; ?>requests.php");
				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar("action","SearchListings");
                mysack.setVar('category',category);
				mysack.onError = function() { alert('An ajax error occured. Please reload the page and try again.') };
				mysack.runAJAX();//excecute
				return true;
			}
		</script>
	<?php 
}
/*
*  Navigation
*/
function bizdir_navigation() { 
	add_menu_page(
		"Biz-Directory Manager",
		"Biz Directory",
		8,
		__FILE__,
		"bizdir_show_manager",
		"http://businessdirectory.squarecompass.com/wp-content/themes/thematic/images/mini_bd_icon.png"
	); 
    add_submenu_page(__FILE__, 'Biz-Directory Categories' , 'Categories', 8,'sub-page', 'bizdir_show_category' );


}
/*
*  Add Form Script
*/
function bizdir_addform_shortcode($atts) { 
	extract(shortcode_atts(array('width'=>'100%'),$atts));
	return 
		"<link rel='stylesheet' href='".BIZDIRCALLBACK."main.css' type='text/css' media='screen'/>".
		"<hr/>".
		bizdir_addform($width).
		"<a href='http://businessdirectory.squarecompass.com/' target='_blank' class='bizdir_notes_grey'>Powered by Business Directory for Wordpress</a>".
		"<hr/>";
}
/*
*  Directory Script
*/
function bizdir_directory_shortcode($atts) {
	global $wpdb;
	//get attributes
	extract(shortcode_atts(array('width'=>'100%','name'=>' the Business Directory'),$atts));
	//Get Category options
	$categories = $wpdb->get_results("SELECT * FROM ".BIZDIRCATTABLE." WHERE hide !=1 ORDER BY category ASC");
	$options = "<option value='0'>--Select Category--</option>";
	foreach($categories as $category) 
		$options .= "<option value='".wp_specialchars($category->category_id)."'>".wp_specialchars($category->category)."</option>";
	//display Listings
	return
		"<link rel='stylesheet' href='".BIZDIRCALLBACK."main.css' type='text/css' media='screen'/>". 
		"<form name='search' onSubmit='bizdir_search_listings(); return false;'>".
			"<b>Search $name:</b> ".
			"<input type='text' id='bizdir_search_term'/>".
			"<input type='submit' id='bizdir_search' value='Search'/> ".
		"</form>".
        "<form name='sort'>".
        	"<b>Search by Category:</b> ".
        	"<select id='categories' onChange='bizdir_sort_categories(this.options[this.selectedIndex]); return false;' bizdir_autofill='CategoryInfo'>".
				$options.
			"</select>".
        "</form>".
		"<div style='display:inline;' id='bizdir_messages'></div><br/>".
		"<hr/>".
		"<div id='bizdir_directory' style='width:$width'>".bizdir_directory()."</div>".
		"<a href='http://businessdirectory.squarecompass.com/' target='_blank' class='bizdir_notes_grey'>Powered by Business Directory for Wordpress</a>".
		"<hr/>"
	;
}
/*
*  Listing Manager
*/
function bizdir_show_manager() { 
	echo 
		"<link rel='stylesheet' href='".BIZDIRCALLBACK."main.css' type='text/css' media='screen'/>".
		"<div class='wrap wpcf7'>".
			"<div id='icon-tools' class='icon32'><br></div>".
			"<h2>Business Directory</h2>".
			"<table class='widefat'>".
			  "<tr>".
				"<td>".
					"<b style='font-size:1.3em;'>How to Display Business Directory</b><br/>".
					"To display the <b>Business Directory Listings</b>, place the following code into the content of a page or post: ".
					"[bizdir_directory]<br/>".
					"To display the <b>Business Directory Add Form</b>, place the following code into the content of a page or post: ".
					"[bizdir_addform]<br/><br/>".
					"Please note that Business Directory is not currently set to have the Listings and the Add Form on the same page. ".
					"For more information, documentation, and/or help see ".
					"<a href='http://businessdirectory.squarecompass.com/documentation/' target='_blank'>Business Directory Help/Documentation</a>.".
				"</td>".
			  "</tr>".
			"</table>".
			"<hr/>".
			"<div id='bizdir_messages'></div>".
			"<div id='bizdir_manager'>".bizdir_manager_home()."</div>".
		"<div>".
		"<a href='http://businessdirectory.squarecompass.com/' target='_blank' class='bizdir_notes_grey'>Powered by Business Directory for Wordpress</a>".
		"<br/><br/><br/>"
	;
} //Display manager

/*
 * Categories Manager
 */
function bizdir_show_category() {
    echo 
        "<link rel='stylesheet' href='".BIZDIRCALLBACK."main.css' type='text/css' media='screen'/>".
		"<div class='wrap wpcf7'>".
			"<div id='icon-tools' class='icon32'><br></div>".
			"<h2>Business Directory</h2>".
			"<a href='http://businessdirectory.squarecompass.com/documentation/' target='_blank'>Business Directory Help/Documentation</a>".
			"<hr />".
			"<div id='bizdir_messages'></div>".
			"<div id='bizdir_categories'>".bizdir_category_home()."</div>".
		"</div>".
		"<a href='http://businessdirectory.squarecompass.com/' target='_blank' class='bizdir_notes_grey'>Powered by Business Directory for Wordpress</a>"
    ;
}


function bizdir_js_admin_header() { //Set Ajax Calls for manager
	wp_print_scripts(array('sack')); //use JavaScript SACK library for Ajax
	?>
		<script type="text/javascript">
			function bizdir_edit_listing(id) {
				clearMessage();
				//Build SACK Call
				var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php");
				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar("action","bizdir_edit_listing");
				mysack.setVar("listing_id",id);
				mysack.onError = function() { alert('An ajax error occured while processing your request. Please reload the page and try again.') };
				mysack.runAJAX();//excecute
				return true;
			}

            function bizdir_add_category() {
                clearMessage();
				//Clear AutoFill Text
				var description = document.getElementById("bizdir_description");
				bizdir_clearAutoFill(description.id,description.getAttribute('bizdir_autofill'));
                var mysack = new sack("<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php");
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar("action", "bizdir_add_category");
                mysack.setVar("category",document.getElementById("bizdir_category").value);
				mysack.setVar("description",document.getElementById("bizdir_description").value);
                mysack.onError = function () { alert('An ajax error occured while processing your request. Please reload the page and try again.') };
                mysack.runAJAX();
                return true;
            }


            function bizdir_edit_category(id) {
                clearMessage();
                var mysack = new sack("<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php");
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar("action", "bizdir_edit_category");
                mysack.setVar("category_id", id);
                mysack.onError = function () { alert('An ajax error occured while processing your request. Please reload the page and try again.') };
                mysack.runAJAX();
                return true;
            }

            function bizdir_update_category() {
				clearMessage();
				//Disable buttons and display message
				document.getElementById('bizdir_save').disabled = true;
				document.getElementById('bizdir_cancel').disabled = true;
				var submit_message = document.getElementById('bizdir_submit_message')
				submit_message.className = "bizdir_message";
				submit_message.innerHTML = "Submitting...";
				//Build SACK Call
				bizdir_clearAllAutoFill();
				var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php");
				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar("action","bizdir_update_category");
				mysack.setVar("category_id",document.getElementById("bizdir_category_id").value);
				mysack.setVar("category",document.getElementById("bizdir_category").value);
				mysack.setVar("description",document.getElementById("bizdir_description").value);
				mysack.onError = function() { alert('An ajax error occured while updating the listing. Please reload the page and try again.') };
				mysack.runAJAX();//excecute
				return true;
			}
			
			function bizdir_update_listing(status) {
				clearMessage();
				//Disable buttons and display message
				document.getElementById('bizdir_save').disabled = true;
				var save_approve = document.getElementById('bizdir_save_approve')
				if(save_approve != null)
					save_approve.disabled = true;
				document.getElementById('bizdir_cancel').disabled = true;
				var submit_message = document.getElementById('bizdir_submit_message')
				submit_message.className = "bizdir_message";
				submit_message.innerHTML = "Submitting...";
				//Clear AutoFill
				bizdir_clearAllAutoFill();
				//Build SACK Call
				var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php");
				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar("action","bizdir_update_listing");
				if(status == "approve")
					mysack.setVar("status",1);
				mysack.setVar("listing_id",document.getElementById("bizdir_listing_id").value);
				mysack.setVar("category_id", document.getElementById("bizdir_category_id").value);
				mysack.setVar("name",document.getElementById("bizdir_name").value);
				mysack.setVar("email",document.getElementById("bizdir_email").value);
				mysack.setVar("cName",document.getElementById("bizdir_cName").value);
				mysack.setVar("description",document.getElementById("bizdir_description").value);
				mysack.setVar("keywords",document.getElementById("bizdir_keywords").value);
				mysack.setVar("website",document.getElementById("bizdir_website").value);
				mysack.setVar("cEmail",document.getElementById("bizdir_cEmail").value);
				mysack.setVar("phone",document.getElementById("bizdir_phone").value);
				mysack.setVar("street1",document.getElementById("bizdir_street1").value);
				mysack.setVar("street2",document.getElementById("bizdir_street2").value);
				mysack.setVar("city",document.getElementById("bizdir_city").value);
				mysack.setVar("state",document.getElementById("bizdir_state").value);
				mysack.setVar("zip",document.getElementById("bizdir_zip").value);
				mysack.setVar("country",document.getElementById("bizdir_country").value);
				mysack.onError = function() { alert('An ajax error occured while updating the listing. Please reload the page and try again.') };
				mysack.runAJAX();//excecute
				return true;
			}
			
			function bizdir_show_manager_home() {
				clearMessage();
				//Build SACK Call
				var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php");
				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar("action","bizdir_show_manager_home");
				mysack.onError = function() { alert('An ajax error occured while processing your request. Please reload the page and try again.') };
				mysack.runAJAX();//excecute
				return true;
			}

            function bizdir_show_category_home() {
				clearMessage();
				//Build SACK Call
				var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php");
				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar("action","bizdir_show_category_home");
				mysack.onError = function() { alert('An ajax error occured while processing your request. Please reload the page and try again.') };
				mysack.runAJAX();//excecute
				return true;
			}
			
			function bizdir_change_status(id,status) {
				clearMessage();
				//Build SACK Call
				var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php");
				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar("action","bizdir_change_listing_status");
				mysack.setVar("listing_id",id);
				mysack.setVar("status",status);
				mysack.onError = function() { alert('An ajax error occured while processing your request. Please reload the page and try again.') };
				mysack.runAJAX();//excecute
				return true;
			}
			
			function bizdir_delete_listing(id) {
				clearMessage();
				if(confirm('Are you sure you want to delete this listing? This listing will be perminantly removed.')) {
					//Build SACK Call
					var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php");
					mysack.execute = 1;
					mysack.method = 'POST';
					mysack.setVar("action","bizdir_delete_listing");
					mysack.setVar("listing_id",id);
					mysack.onError = function() { alert('An ajax error occured while processing your request. Please reload the page and try again.') };
					mysack.runAJAX();//excecute
				}
				return true;
			}
            
            function bizdir_delete_category(id,name,count) {
				clearMessage();
				var confirmMessage = "Are you sure you want to delete the \""+name+"\" category? "; 
				confirmMessage += "The \""+name+"\" category and all listings in it will be perminantly removed ";
				confirmMessage += "(There "+(count == 1?"is":"are")+" currently "+count+" listing"+(count == 1?"":"s")+" in the \""+name+"\" category). ";
				confirmMessage += "You may want to back up your database before deleting the \""+name+"\" category.";
				if(confirm(confirmMessage)) {
					//Build SACK Call
					var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php");
					mysack.execute = 1;
					mysack.method = 'POST';
					mysack.setVar("action","bizdir_delete_category");
					mysack.setVar("category_id",id);
					mysack.onError = function() { alert('An ajax error occured while processing your request. Please reload the page and try again.') };
					mysack.runAJAX();//excecute
				}
				return true;
			}
		</script>
	<?php
}
