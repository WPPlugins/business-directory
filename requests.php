<?php
//Load WordPress
$wp_root = explode("wp-content",$_SERVER["SCRIPT_FILENAME"]);
$wp_root = $wp_root[0];
if($wp_root == $_SERVER["SCRIPT_FILENAME"]) {
	$wp_root = explode("index.php",$_SERVER["SCRIPT_FILENAME"]);
	$wp_root = $wp_root[0];
}
chdir($wp_root);
if(!function_exists("add_action")) require_once(file_exists("wp-load.php")?"wp-load.php":"wp-config.php");
require(dirname(__FILE__).'/config.php'); //Load Biz-Directory Config File
//Clean Input
$accepted_values = array(
	"action"=>"alpha",
	"category_id"=>"id",
	"name"=>"text","email"=>"email",
	"cName"=>"text","description"=>"text","keywords"=>"text","website"=>"url","cEmail"=>"email","phone"=>"text",
	"street1"=>"text","street2"=>"text","city"=>"text","state"=>"text","zip"=>"text","country"=>"text",
	"searchTerms"=>"text","category"=>"id",
	"offset"=>"numeric"
);
$v = bizdir_clean_array($_POST,$accepted_values);
$response = "";
//Process Data
switch(@$v["action"]) {
	case "AddListing":
		//validate Imput
		$errors = validateListing($v);
		//Process Input
		if(count($errors) < 1) {
			//Insert Listing into the database
			$keys = 
				"category_id,date_created,status,".
				"name,email,".
				"company_name,company_description,company_keywords,company_url,company_email,company_phone,".
				"company_street1,company_street2,company_city,company_state,company_zip,company_country"
			;
			$query = $wpdb->prepare(
				"INSERT INTO ".BIZDIRDBTABLE." ($keys) VALUES (%d,%s,%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s);",
				$v["category_id"],date("Y-m-d G:i:s"),0,
				$v["name"],$v["email"],
				$v["cName"],$v["description"],$v["keywords"],$v["website"],$v["cEmail"],$v["phone"],
				$v["street1"],$v["street2"],$v["city"],$v["state"],$v["zip"],$v["country"]
			);
			$wpdb->query($query);
			//create response
			$response = "
				var messages = document.getElementById('bizdir_messages');
				messages.className = 'bizdir_message';
				messages.innerHTML = 'Your listing has been submitted and is pending approval from an administrator.';
				document.getElementById('bizdir_submit').disabled = false;
				var submit_message = document.getElementById('bizdir_submit_message');
				submit_message.className = '';
				submit_message.innerHTML = '&nbsp;';
				bizdir_populateAutofill(true);
			";
		} else {
			$message = "";
			foreach($errors as $err)
				$message .= $err;
			$response = "
				var messages = document.getElementById('bizdir_messages');
				messages.className = 'bizdir_error_box';
				messages.innerHTML = '$message';
				document.getElementById('bizdir_submit').disabled = false;
				var submit_message = document.getElementById('bizdir_submit_message');
				submit_message.className = 'bizdir_error';
				submit_message.innerHTML = 'Please fix the above errors and submit again.';
			";
			$keys = array("name","email","cName","keywords","website","cEmail","phone","category_id");
            $response .= "bizdir_populateAutofill();";
		}
	break;
	case "SearchListings":
		if((empty($v["searchTerms"]) || !is_string($v["searchTerms"])) && (empty($v["category"]) || !is_string($v["category"]))) {
			$response = "
				var messages = document.getElementById('bizdir_search_message');
				messages.className = 'bizdir_error';
				messages.innerHTML = 'Enter search was unsearchable. Please Try again.';
				document.getElementById('bizdir_search').disabled = false;
			";
			break;
		}
		$listings = "";
		if(!empty($v["searchTerms"]) && is_string($v["searchTerms"]))
			$listings = str_replace("'","\'",stripslashes(bizdir_directory($v["searchTerms"])));
		elseif(!empty($v["category"]) && is_numeric($v["category"]))
			$listings = str_replace("'","\'",stripslashes(bizdir_directory("",0,$v["category"])));
		$response = "
			clearMessage();
			document.getElementById('bizdir_directory').innerHTML = '$listings';
			document.getElementById('bizdir_search').disabled = false;
		";
	break;
	case "ChangePage":
		$listings = str_replace("'","\'",bizdir_directory("",@$v["offset"]));
		$response .= "
			clearMessage();
			document.getElementById('bizdir_directory').innerHTML = '$listings';
			document.getElementById('bizdir_search').disabled = false;
		";
	break;
}
die($response);
