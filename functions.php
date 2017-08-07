<?php
/*
*  Add Listing Functions
*/
function bizdir_addform($width = "100%",$editView = false,$alreadyApproved = false) {
	global $wpdb;
	$form = 
		"<form id='bizdir_add_form' method='POST' width='$width'>".
			"<table width='100%' border='0' cellspacing='0' cellpadding='0'>".
			  "<tr><td id='bizdir_messages' colspan='2'></td></tr>"
	;
	//In the furute this will be changed to be more dynamic
	$adjective = $editView?"Lister's":"Your";
	$feilds = array(
		"bizdir_name"=>array("title"=>"$adjective Name *","autofill"=>"YourInfo","maxlength"=>100, "type"=>"text"),
        "bizdir_category_id"=>array("title"=>"Category*", 'type'=>"select", "autofill"=>"CategoryInfo"),
		"bizdir_email"=>array("title"=>"$adjective Email *","autofill"=>"YourInfo","maxlength"=>100, "type"=>"text"),
		"bizdir_cName"=>array("title"=>"Organization Name *","autofill"=>"CompanyInfo","maxlength"=>100, "type"=>"text"),
		"bizdir_description"=>array("title"=>"Org. Description *","autofill"=>"Description","maxlength"=>NULL, "type"=>"textarea"),
		"bizdir_keywords"=>array("title"=>"Keywords","autofill"=>"Keywords","maxlength"=>100, "type"=>"text"),
		"bizdir_website"=>array("title"=>"Org. Website","autofill"=>"CompanyInfo","maxlength"=>100, "type"=>"text"),
		"bizdir_phone"=>array("title"=>"Org. Phone","autofill"=>"CompanyInfo","maxlength"=>100, "type"=>"text"),
		"bizdir_cEmail"=>array(
			"title"=>"Org. Email","autofill"=>"CompanyInfo","maxlength"=>100, "type"=>"text", 'hint'=>'* Provide at least one form of contact.'
		),
		"bizdir_street1"=>array("title"=>"Org. Address","autofill"=>"GeoInfo","maxlength"=>100, "type"=>"text"),
		"bizdir_street2"=>array("title"=>"&nbsp;","autofill"=>"GeoInfo","maxlength"=>100, "type"=>"text"),
		"bizdir_city"=>array("title"=>"Org. City","autofill"=>"GeoInfo","maxlength"=>100, "type"=>"text"),
		"bizdir_state"=>array("title"=>"Org. State/Province","autofill"=>"GeoInfo","maxlength"=>100, "type"=>"text"),
		"bizdir_zip"=>array("title"=>"Org. Zip Code","autofill"=>"GeoInfo","maxlength"=>100, "type"=>"text"),
		"bizdir_country"=>array("title"=>"Org. Country","autofill"=>"GeoInfo","maxlength"=>100, "type"=>"text")
	);
	$feilds = bizdir_clean_output($feilds);
	foreach($feilds as $id=>$info) {
		$form .= 
		  "<tr>".
			"<td class='bizdir_form_text'>".$info["title"]."</td>".
			"<td class='bizdir_form_input'>";
            switch($info['type']) {
                case "text":
                    $form .= "<input ".
                        "type='text' ".
                        "id='$id' ".
                        "class='bizdir_input_style' ".
						"bizdir_autofill='".$info["autofill"]."' ".
                        "maxlength='".$info["autofill"]."' ".
                        "onFocus='bizdir_clearAutoFill(\"$id\", \"".$info["autofill"]."\");'".
                        "onClick='bizdir_clearAutoFill(\"$id\", \"".$info["autofill"]."\");'".
                    "/>"; 
                break;

                case "textarea":
                    $form .= "<textarea ".
                             "id='$id' ".
                             "class='bizdir_input_text_area' ".
                             "bizdir_autofill='".$info["autofill"]."' ".
                             "onFocus='bizdir_clearAutoFill(\"$id\",\"".$info["autofill"]."\");'".  
                             "onClick='bizdir_clearAutoFill(\"$id\",\"".$info["autofill"]."\");'".  
                     "></textarea>";
                break;

                case "select":
					$categories = $wpdb->get_results("SELECT * FROM ".BIZDIRCATTABLE." WHERE hide !=1 ORDER BY category ASC",ARRAY_A);
					$categories = bizdir_clean_output($categories);
                    $form .="<select ".
                            "id='$id' ".
                            "bizdir_autofill='".$info["autofill"]."' ".
							"onChange='this.setAttribute(\"selected_value\",this.value);' ".
                            "selected_value='' ".
                            "class='bizdir_input_select'".
						">";
					$options = "<option value='0'>--Select Category--</option>";
					foreach($categories as $category) 
						$options .= "<option value='".@$category["category_id"]."'>".@$category["category"]."</option>";
					$form .= $options."</select>";
                break;
                default:
                    $form .= "<input ".
                        "type='text' ".
                        "id='$id' ".
                        "class='bizdir_input_style' ".
                        "maxlength='".$info["autofill"]."' ".
                        "onFocus='bizdir_clearAutoFill(\"$id\", \"".$info["autofill"]."\");'".
                        "onClick='bizdir_clearAutoFill(\"$id\", \"".$info["autofill"]."\");'".
                    "/>";
            }
			$form .="</td>".
		  "</tr>";
		  if(array_key_exists('hint', $info)) {
		    $form .= "<tr>".
				"<td class='bizdir_form_text'>&nbsp;</td>".
				"<td class='bizdir_form_input'><small>".$info['hint']."</small></td>".
			"<tr/>";
          }
        }
	$form .= "<td class='bizdir_form_text'>&nbsp;</td><td class='bizdir_form_input'>";
	if($editView) {
		$form .= "<input type='submit' id='bizdir_save' value='Save Listing' onClick='bizdir_update_listing(null); return false;' disabled/>";
		if(!$alreadyApproved)
			$form .= 
				"<input ".
					"type='button' ".
					"id='bizdir_save_approve' ".
					"value='Save and Approve Listing' ".
					"onClick='bizdir_update_listing(\"approve\");' ".
					"disabled".
				"/>"
			;
		$form .= "<input type='button' id='bizdir_cancel' value='Cancel' onClick='bizdir_show_manager_home();' disabled/>";
		$form .= "<input type='hidden' id='bizdir_listing_id'/>";
	} else
		$form .= "<input type='submit' id='bizdir_submit' value='Submit Listing' onClick='bizdir_add_listing(); return false;'/> ";
	$form .=
					"<span id='bizdir_submit_message'>&nbsp;</span>".
				"</td>".
			  "</tr>".
			  "<tr><td class='bizdir_form_text'>&nbsp;</td><td class='bizdir_form_input bizdir_notes'>* required</td></tr>".
			"</table>".
		"</form>"
	;
	return $form;
}


/*
*  Add Category Functions
*/
function bizdir_category_addform($width = "100%",$editView = false) {
	$form = 
	"<form id='bizdir_add_category_form' method='POST' width='$width'>".
		"<table width='100%' border='0' cellspacing='0' cellpadding='0'>".
			"<tr><td id='bizdir_messages' colspan='2'></td></tr>".
		    "<tr>".
			    "<td class='bizdir_form_text'>Category</td>".
			    "<td class='bizdir_form_input'>".
                    "<input ".
                        "type='text' ".
                        "id='bizdir_category'".
                        "class='bizdir_input_style' ".
                        "bizdir_autofill='CategoryInfo' ".
                        "onFocus='bizdir_clearAutoFill(\"bizdir_category\", \"CategoryInfo\");'".
                        "onClick='bizdir_clearAutoFill(\"bizdir_category\", \"CategoryInfo\");'".
                    "/>".
		        "</td>".
	        "</tr>".
            "<tr>" .
                "<td class='bizdir_form_text'>Description</td>".
                "<td class='bizdir_form_input'>" .
                    "<textarea ".
                             "id='bizdir_description' ".
                             "class='bizdir_input_text_area' ".
                             "bizdir_autofill='Cat_Description' ".
                             "onFocus='bizdir_clearAutoFill(\"bizdir_description\",\"Cat_Description\");'".  
                             "onClick='bizdir_clearAutoFill(\"bizdir_description\",\"Cat_Description\");'".  
                     "></textarea>".
                "</td>".
            "</tr>".
            "<tr>".
	        "<td class='bizdir_form_text'>&nbsp;</td>".
            "<td class='bizdir_form_input'>";

	if($editView) {
		$form .= "<input type='submit' id='bizdir_save' value='Save Category' onClick='bizdir_update_category(null); return false;' disabled/>";
		$form .= "<input type='hidden' id='bizdir_category_id'/>";
	} else {
		$form .= "<input type='submit' id='bizdir_submit' value='Submit Category' onClick='bizdir_add_category(); return false;'/> ";
    }
    $form .= "<input type='button' id='bizdir_cancel' value='Cancel' onClick='bizdir_show_category_home();' disabled/>";
	$form .= "<span id='bizdir_submit_message'>&nbsp;</span>".
		    "</td>".
		    "</tr>".
		    "<tr><td class='bizdir_form_text'>&nbsp;</td><td class='bizdir_form_input bizdir_notes'>* required</td></tr>".
	    "</table>".
	"</form>"
	;
	return $form;
}

/*
*  Listing/Searching Functions
*/
function bizdir_directory($searchTerms = "",$offset = 0,$category = 0) {
	global $wpdb;
	//Validate input
	$searchTerms = strip_tags(trim($searchTerms));
	if(!is_numeric($offset) || round($offset) != $offset) 
		$offset = 0;
	//get Listings
	$numListings = 0;
	$query = "SELECT * FROM ".BIZDIRDBTABLE." l LEFT JOIN ".BIZDIRCATTABLE." c ON (c.category_id = l.category_id) WHERE status='1' ";
	if(!empty($searchTerms)) {
		$searchFeilds = array(
			"company_name","company_description","company_keywords",
			"company_street1","company_street2",
			"company_city","company_state","company_zip","company_country",
            "category"
		);
		$temp = "AND (1=2";
		foreach($searchFeilds as $field)
			$temp .= $wpdb->prepare(" OR $field LIKE %s",$searchTerms);
		$temp .= ");";
		$query .= str_replace(array(" '",' "')," '%",str_replace(array("' ",'" '),"%' ",$temp));
	} elseif(!empty($category) && is_numeric($category)) {
		$query .= $wpdb->prepare("AND c.category_id=%d ORDER BY company_name ASC",$category);
	} else {
		$query .= $wpdb->prepare("ORDER BY company_name ASC LIMIT ".PERPAGE." OFFSET %d",$offset);
		$numListings = $wpdb->get_var("SELECT COUNT(*) FROM ".BIZDIRDBTABLE);
	}
	$listings = bizdir_clean_output($wpdb->get_results($query,ARRAY_A));
	$directory = "";
	$pagination = "";
	//Display Pages
	if($numListings > PERPAGE && empty($searchTerms) && empty($category)) {
		$directory .= "<div style='margin:3px 0 8px 0;padding-bottom:2px;border-bottom:1px solid black;'>";
		$pagination .= "<b>Pages:</b>";
		$remaining = $numListings - PERPAGE;
		$count = 0;
		while($remaining > -1 * PERPAGE) {
			$index = PERPAGE * $count++;
			$pagination .= "&nbsp;&nbsp;";
			if($offset >= $index && $offset < $index + PERPAGE)
				$pagination .= "$count";
			else
				$pagination .= "<a style='cursor:pointer' onClick='bizdir_change_listings_page(".(($count - 1) * PERPAGE).")'>$count</a>";
			$remaining -= PERPAGE;
		}
		$directory .= "$pagination</div>";
	}
	//Display Listings
	$directory .= "<table width='100%' border='0' cellspacing='0' cellpadding='0' id='bizdir_directory'>";
	if(!empty($searchTerms))
		$directory .= 
			"<tr><th style='text-align:center;padding:5px 15px 10px 15px;'>".
				"<b>Search results for: \"$searchTerms\"</b><br/>".
				"<small><a style='cursor:pointer;' onClick='bizdir_change_listings_page(0);'>View Entire Directory</a></small>".
			"</th></tr>"
		;
	foreach($listings as $l) {
		$directory .= 
			"<tr><td>".
			"<b>".
				(empty($l["company_url"])?
					str_replace("&Acirc;&","&",@$l["company_name"]):
					"<a href='".$l["company_url"]."' class='bizdir_linked_title' target='_blank'>".
						str_replace("&Acirc;&","&",@$l["company_name"]).
					"</a>"
				).
			"</b>".
			"<p>".str_replace("&Acirc;&","&",$l["company_description"])."</p>"
		;
		if(!empty($l->company_url))
			$directory .= "<a href='".@$l["company_url"]."' target='_blank' title='".@$l["company_name"]."'>".@$l["company_url"]."</a><br/>";
		if(!empty($l->company_email))
			$directory .= "<a href='mailto:".@$l["company_email"]."'>".@$l["company_email"]."</a><br/>";
		if(!empty($l->company_phone))
			$directory .= @$l["company_phone"]."<br/>";
		$directory .= "</td></tr>";
	}
	if(count($listings) < 1)
		$directory .= "<tr><td>".(empty($searchTerms)?"There are currently no listings in the directory":"No Results")."</td></tr>";
	$directory .= "</table>";
	//Add Footer Pagination
	if(!empty($pagination))
		$directory .= "<div style='margin:3px 0 3px 0;padding:2px 0 2px 0;border:1px solid black;border-left:0;border-right:0;'>$pagination</div>";
	return $directory;
}
/*
*  Managing Functions
*/
function bizdir_manager_home() {
	global $wpdb;
	//get Listings
	$query = "SELECT * FROM ".BIZDIRDBTABLE." l LEFT JOIN ".BIZDIRCATTABLE." c ON (c.category_id = l.category_id) ORDER BY company_name ASC;";
	$listings = bizdir_clean_output($wpdb->get_results($query,ARRAY_A));
	$listingsByStatus = array("Pending"=>array(),"Approved"=>array());
	foreach($listings as $l)
		if(@$l["status"] == 0)
			$listingsByStatus["Pending"][] = $l;
		else
			$listingsByStatus["Approved"][] = $l;
	$display = "";
	foreach($listingsByStatus as $title=>$list) {
		$display .= "<h3>$title Listings: ".count($list)."</h3><table width='100%' border='0' cellspacing='0' cellpadding='0' class='widefat'>";
		$display .= 
			"<thead><tr>".
				"<th>Company Name</th>".
				"<th>Category</th>".
				"<th>Name</th>".
				"<th>Email</th>".
				"<th>Company Website</th>".
				"<th>Actions</th>".
			"</tr></thead>".
			"<tbody>"
		;
		$count = 0;
		foreach($list as $l)
			$display .= 
				"<tr ".($count++%2 == 0?"class='alternate'":"").">".
					"<td>".str_replace("&Acirc;&","&",@$l["company_name"])."</td>".
                    "<td>".@$l["category"]."</td>".
					"<td>".@$l["name"]."</td>".
					"<td><a href='mailto:".@$l["email"]."' target='_blank'>".@$l["email"]."</a></td>".
					"<td>".(empty($l["company_url"])?"&nbsp;":"<a href='".$l["company_url"]."' target='_blank'>".@$l["company_url"]."</a>")."</td>".
					"<td>".
						"<a style='cursor:pointer;' onClick='bizdir_edit_listing(\"".@$l["listing_id"]."\")'>review</a> | ".
						($title == "Pending"?"<a style='cursor:pointer;' onClick='bizdir_change_status(\"".@$l["listing_id"]."\",1)'>approve</a> | ":"").
						"<a style='cursor:pointer;' onClick='bizdir_delete_listing(\"".@$l["listing_id"]."\")'>delete</a>".
					"</td>".
				"</tr>";
			;
		if(count($list) < 1)
			$display .= "<tr><td colspan='4'>There are no \"$title Listings\".</td></tr>";
		else
			$display .= "</tbody>";
		$display .= "</table>";
	}
	return $display;
}

/*
*  Categories Functions
*/
function bizdir_category_home() {
	global $wpdb;
	//get category count
	$res = bizdir_clean_output(
		$wpdb->get_results("SELECT category_id,COUNT(category_id) as num_listings FROM ".BIZDIRDBTABLE." GROUP BY category_id",ARRAY_A)
	);
	$categories_count = array();
	foreach($res as $cat)
		if(!empty($cat["category_id"]) && !empty($cat["num_listings"]))
			$categories_count[$cat["category_id"]] = $cat["num_listings"];
	//get categories
	$categories = bizdir_clean_output($wpdb->get_results("SELECT * FROM ".BIZDIRCATTABLE." ORDER BY hide,category ASC",ARRAY_A));
	$display = "";
    $display .= "<h3>Categories</h3>";
    $display .= "<p><a style='cursor:pointer;' onClick='bizdir_edit_category(\"\")'>Add New Category</a></p>";
    $display .= "<table width='100%' border='0' cellspacing='0' cellpadding='0' class='widefat'>";
	$display .= "<thead><tr><th width='15%'>Category</th><th width='70%'>Description</th><th width='15%'>Actions</th></tr></thead><tbody>";
    if (count($categories) > 0) {
	    foreach($categories as $key=>$category) {
			$display .= 
				"<tr ".($key%2 == 0?"class='alternate'":"").">".
					"<td>".@$category["category"]."</td>".
					"<td>".@$category["description"]."</td>";
			if($category["hide"] == 0) {
				$display .= "<td>".
								"<a style='cursor:pointer;' onClick='bizdir_edit_category(\"".@$category["category_id"]."\")'>edit</a> | ".
								"<a ".
									"title='".@$category["category"]."' ".
									"style='cursor:pointer;' ".
									"onClick='bizdir_delete_category(".
										@$category["category_id"].",".
										"this.title,".
										(empty($categories_count[@$category["category_id"]])?"0":$categories_count[@$category["category_id"]]).
									");'".
								">delete</a>".
							"</td>";
			} else {
				$display .= "<td>".
								"<span class='bizdir_notes_grey'>--disabled--</span>".
							"</td>";
			}    
			$display .= "</tr>";
        }
    } else {
	    $display .= "<tr><td colspan='4'>There are no \"Categories\".</td></tr>";
	}
    $display .= "</tbody>";
	$display .= "</table>";
	return $display;
}

function bizdir_edit_listing() {
	global $wpdb;
	//Get Listing to edit
	$v = bizdir_clean_array($_POST,array("listing_id"=>"id"));
	$query = $wpdb->prepare(
		"SELECT * FROM ".BIZDIRDBTABLE." l LEFT JOIN ".BIZDIRCATTABLE." c ON (c.category_id = l.category_id) WHERE l.listing_id=%d;",
		(empty($v["listing_id"])?0:$v["listing_id"])
	);
	$listing = bizdir_clean_output($wpdb->get_row($query,ARRAY_A),true);
	if(empty($listing))
		die("alert('There was an error processing your request. Please reload the page and try again.');");
	//Display add Form
	$form = "<h3>Edit Listing</h3>".str_replace("'","\'",bizdir_addform("100%",true,(@$listing["status"] != 0)));
    $response = "
        document.getElementById('bizdir_manager').innerHTML = '".$form."';
        document.getElementById('bizdir_listing_id').value = '".absint(@$listing["listing_id"])."';
        document.getElementById('bizdir_name').value = '".@$listing["name"]."';
        document.getElementById('bizdir_category_id').value = ".@$listing["category_id"].";
        document.getElementById('bizdir_email').value = '".@$listing["email"]."';
        document.getElementById('bizdir_cName').value = '".@$listing["company_name"]."';
        document.getElementById('bizdir_description').innerHTML = '".@$listing["company_description"]."';
        document.getElementById('bizdir_keywords').value = '".@$listing["company_keywords"]."';
        document.getElementById('bizdir_website').value = '".@$listing["company_url"]."';
        document.getElementById('bizdir_cEmail').value = '".@$listing["company_email"]."';
        document.getElementById('bizdir_phone').value = '".@$listing["company_phone"]."';
        document.getElementById('bizdir_street1').value = '".@$listing["company_street1"]."';
        document.getElementById('bizdir_street2').value = '".@$listing["company_street2"]."';
        document.getElementById('bizdir_city').value = '".@$listing["company_city"]."';
        document.getElementById('bizdir_state').value = '".@$listing["company_state"]."';
        document.getElementById('bizdir_zip').value = '".@$listing["company_zip"]."';
        document.getElementById('bizdir_country').value = '".@$listing["company_country"]."';
        document.getElementById('bizdir_save').disabled = false;
        var save_approve = document.getElementById('bizdir_save_approve')
        if(save_approve != null)
            save_approve.disabled = false;
        document.getElementById('bizdir_cancel').disabled = false;
        bizdir_populateAutofill();
    ";
	die($response); 
}

function bizdir_update_listing() {
	global $wpdb;
	$response = "";
	$acceptable_fields = array(
		"listing_id"=>"id","category_id"=>"id","status"=>"numeric",
		"name"=>"string","email"=>"email",
		"cName"=>"text","description"=>"text","keywords"=>"string","website"=>"url","cEmail"=>"email","phone"=>"phone",
		"street1"=>"text","street2"=>"text","city"=>"string","state"=>"string","zip"=>"string","country"=>"string"
	);
	$v = bizdir_clean_array($_POST,$acceptable_fields);
	$errors = validateListing($v,true);
	//Process Input
	if(count($errors) < 1) {
		//Insert Listing into the database
		$query = "UPDATE ".BIZDIRDBTABLE." SET ";
		if(@$v["status"] == 1)
			$query .= "status=1,";
		$query .= "name=%s,category_id=%d,email=%s,";
		$query .= "company_name=%s,company_description=%s,company_keywords=%s,company_url=%s,company_email=%s,company_phone=%s,";
		$query .= "company_street1=%s,company_street2=%s,company_city=%s,company_state=%s,company_zip=%s,company_country=%s ";
		$query .= "WHERE listing_id=%d;";
		$query = $wpdb->prepare($query,
				$v["name"],$v['category_id'],$v["email"],$v["cName"],$v["description"],$v["keywords"],$v["website"],$v["cEmail"],$v["phone"],
				$v["street1"],$v["street2"],$v["city"],$v["state"],$v["zip"],$v["country"],(empty($v["listing_id"])?0:$v["listing_id"])
		);
		$wpdb->query($query);
		$response = "
			var messages = document.getElementById('bizdir_messages');
			messages.className = 'bizdir_message';
			messages.innerHTML = 'Listing Updated.';
			document.getElementById('bizdir_manager').innerHTML = '".str_replace("'","\'",bizdir_manager_home())."';
			window.location = '#';
		";
	} else {
		$message = "";
		foreach($errors as $err)
			$message .= str_replace("'","\'",$err);
		$response = "
			var messages = document.getElementById('bizdir_messages');
			messages.className = 'bizdir_error_box';
			messages.innerHTML = '$message';
			document.getElementById('bizdir_save').disabled = false;
			var save_approve = document.getElementById('bizdir_save_approve')
			if(save_approve != null)
				save_approve.disabled = false;
			document.getElementById('bizdir_cancel').disabled = false;
			var submit_message = document.getElementById('bizdir_submit_message');
			submit_message.className = 'bizdir_error';
			submit_message.innerHTML = 'Please fix the above errors and submit again.';
		";
	}
	die($response);
}

function bizdir_show_manager_home() { die("document.getElementById('bizdir_manager').innerHTML = '".str_replace("'","\'",bizdir_manager_home())."';"); }

function bizdir_change_listing_status() {
	global $wpdb;
	$v = bizdir_clean_array($_POST,array("listing_id"=>"id","status"=>"numeric"));
	$status = @$v["status"] == 1?1:0;
	$query = $wpdb->prepare("UPDATE ".BIZDIRDBTABLE." SET status=$status WHERE listing_id=%d;",(empty($v["listing_id"])?0:$v["listing_id"]));
	$wpdb->query($query);
	$response = "
		var messages = document.getElementById('bizdir_messages');
		messages.className = 'bizdir_message';
		messages.innerHTML = 'Listing ".($status == 1?"Approved":"Reomved from Approved List").".';
		document.getElementById('bizdir_manager').innerHTML = '".str_replace("'","\'",bizdir_manager_home())."';
	";
	die($response);
}

function bizdir_delete_listing() {
	global $wpdb;
	$response = "";
	$v = bizdir_clean_array($_POST,array("listing_id"=>"id"));
	$status = @$v["status"] == 1?1:0;
	$query = $wpdb->prepare("DELETE FROM  ".BIZDIRDBTABLE." WHERE listing_id=%d;",(empty($v["listing_id"])?0:$v["listing_id"]));
	if($wpdb->query($query))
		$response = "
			var messages = document.getElementById('bizdir_messages');
			messages.className = 'bizdir_message';
			messages.innerHTML = 'Listing Deleted.';
			document.getElementById('bizdir_manager').innerHTML = '".str_replace("'","\'",bizdir_manager_home())."';
		";
	else
		$response = "alert('Unable to delete listing. Please reload the page and try again.')";
	die($response);
}

function bizdir_show_category_home() { 
	die("document.getElementById('bizdir_categories').innerHTML = '".str_replace("'","\'",bizdir_category_home())."';"); 
}


function bizdir_edit_category() {
	global $wpdb;
    $form = '';
    $v = bizdir_clean_array($_POST,array("category_id"=>"id","category"=>"string","description"=>"text"));
    $edit = (is_null($v['category_id'])) ? false : true;
    if ($edit) {
	    //Get Listing to edit
		$query = $wpdb->prepare("SELECT * FROM ".BIZDIRCATTABLE." WHERE category_id=%d;",(empty($v["category_id"])?0:$v["category_id"]));
	    $category = bizdir_clean_output($wpdb->get_row($query,ARRAY_A),true);
	    if(empty($category))
		    die("alert('There was an error processing your request. Please reload the page and try again.');");
	}
        //Display add Form
	    $form .= "<h3> Category</h3>".str_replace("'","\'",bizdir_category_addform("100%",$edit));
    $response = "
        document.getElementById('bizdir_categories').innerHTML = '".$form."';";
        if ($edit) {
    $response .= "
            document.getElementById('bizdir_category_id').value = '".absint(@$category["category_id"])."';
            document.getElementById('bizdir_category').value = '".@$category["category"]."';
            document.getElementById('bizdir_description').innerHTML = '".@$category["description"]."';
            document.getElementById('bizdir_save').disabled = false;";
       }
    $response .= "
        document.getElementById('bizdir_cancel').disabled = false;
        bizdir_populateAutofill();
    ";
	die($response); 
}

function bizdir_add_category() {
   	global $wpdb;
	$response = "";
	$v = bizdir_clean_array($_POST,array("category"=>"string","description"=>"text"));
	//validate Input
	$errors = validateCategory($v,true);
	//Process Input
	if(count($errors) < 1) {
        //Insert Category into the database
		$wpdb->query($wpdb->prepare("INSERT INTO ".BIZDIRCATTABLE." (category,description) VALUES (%s,%s);",$v["category"],$v["description"]));
		//create response
		$response = 
			"var messages = document.getElementById('bizdir_messages');".
			"messages.className = 'bizdir_message';".
			"messages.innerHTML = 'Category Added.';".
			"var warning = document.getElementById('bizdir_warning');".
			"if(warning != null) {".
				"warning.className = '';".
				"warning.innerHTML = '';".
			"}".
			"document.getElementById('bizdir_categories').innerHTML = '".str_replace("'","\'",bizdir_category_home())."';".
			"window.location = '#';"
		;
	} else {
		$message = "";
		foreach($errors as $err)
			$message .= str_replace("'","\'",$err);
		$response = "
			var messages = document.getElementById('bizdir_messages');
			messages.className = 'bizdir_error_box';
			messages.innerHTML = '$message';
			document.getElementById('bizdir_save').disabled = false;
			var save_approve = document.getElementById('bizdir_save_approve')
			if(save_approve != null)
				save_approve.disabled = false;
			document.getElementById('bizdir_cancel').disabled = false;
			var submit_message = document.getElementById('bizdir_submit_message');
			submit_message.className = 'bizdir_error';
			submit_message.innerHTML = 'Please fix the above errors and submit again.';
		";
	}
	die($response);
}

function bizdir_update_category() {
	global $wpdb;
	$response = "";
	$v = bizdir_clean_array($_POST,array("category_id"=>"id","category"=>"string","description"=>"text"));
	//validate Imput
	$errors = validateCategory($v,true);
	//Process Input
	if(count($errors) < 1) {
		//Insert Listing into the database
		$query = "UPDATE ".BIZDIRCATTABLE." SET category=%s,description=%s WHERE category_id=%d;";
		$wpdb->query($wpdb->prepare($query,$v["category"],$v["description"],$v["category_id"]));
		$response = 
			"var messages = document.getElementById('bizdir_messages');".
			"messages.className = 'bizdir_message';".
			"messages.innerHTML = 'Category Updated.';".
			"var warning = document.getElementById('bizdir_warning');".
			"if(warning != null) {".
				"warning.className = '';".
				"warning.innerHTML = '';".
			"}".
			"document.getElementById('bizdir_categories').innerHTML = '".str_replace("'","\'",bizdir_category_home())."';".
			"window.location = '#';"
		;
	} else {
		$message = "";
		foreach($errors as $err)
			$message .= str_replace("'","\'",$err);
		$response = "
			var messages = document.getElementById('bizdir_messages');
			messages.className = 'bizdir_error_box';
			messages.innerHTML = '$message';
			document.getElementById('bizdir_save').disabled = false;
			var save_approve = document.getElementById('bizdir_save_approve')
			if(save_approve != null)
				save_approve.disabled = false;
			document.getElementById('bizdir_cancel').disabled = false;
			var submit_message = document.getElementById('bizdir_submit_message');
			submit_message.className = 'bizdir_error';
			submit_message.innerHTML = 'Please fix the above errors and submit again.';
		";
	}
	die($response);
}

function bizdir_delete_category() {
	global $wpdb;
	$response = "";
	$v = bizdir_clean_array($_POST,array("category_id"=>"id"));
	$query = $wpdb->prepare("DELETE FROM ".BIZDIRDBTABLE." WHERE category_id=%d;",(empty($v["category_id"])?0:$v["category_id"]));
	if($wpdb->query($query) === false)
		die("alert('Unable to delete category. Please reload the page and try again.')");
	$query = $wpdb->prepare("DELETE FROM ".BIZDIRCATTABLE." WHERE category_id=%d;",(empty($v["category_id"])?0:$v["category_id"]));
	if($wpdb->query($query)) {
		$response = "
			var messages = document.getElementById('bizdir_messages');
			messages.className = 'bizdir_message';
			messages.innerHTML = 'Category Deleted.';
			document.getElementById('bizdir_categories').innerHTML = '".str_replace("'","\'",bizdir_category_home())."';
		";
	} else {
		$response = "alert('Unable to delete category. Please reload the page and try again.')";
    }
	die($response);
}


/*
* Helper Functions
*/
function bizdir_clean_array($v,$keys) {
	$v = stripslashes_deep($v);
	if(is_array($v) && is_array($keys) && count($v) > 0 && count($keys) > 0) {
		$res = array();
		$allowable_types = array(
			"alpha","alpha_numeric","id","email","website","url","phone","numeric","n","num","decimal","d","string","s","str","text"
		);
		$line_breaks = array("\r\n","\n\r","\n","\r");
		foreach($v as $key=>$value) {
			if(
				!empty($keys[$key]) && (is_string($key) || is_numeric($key)) && 
				in_array($keys[$key],$allowable_types) && //Validate that the key is valid and wanted
				(is_string($value) || is_numeric($value)) //Validate that the value is a string or a number
			) {
				//Ensure that the key is a safe value
				$key = stripcslashes(strip_tags(str_replace($line_breaks,"",bizdir_clean_multiquotes(trim($key)))));
				if(!preg_match("/^[\w\s\-]+$/",$key))
					continue;
				//Ensure that the value is a safe value.
				$val = bizdir_clean_multiquotes(stripcslashes(strip_tags(str_replace($line_breaks,"",trim($value)))));
				switch(strtolower($keys[$key])) { //Verify that the value is acceptable
					case "alpha": //String containing only letters
						if(preg_match("/^[a-zA-Z]+$/",$val))
							$res[$key] = $val; //Save the value to the new array
					break;
					case "alpha_numeric": //String containing only letters and numbers (no decimals)
						if(preg_match("/^[a-zA-Z0-9]+$/",$val))
							$res[$key] = $val; //Save the value to the new array
					break;
					case "id": //A positive, non-zero integer
						if(is_numeric($val) && round($val) == $val && $val > 0)
							$res[$key] = $val; //Save the value to the new array
					break;
					case "email": //An email
						if(preg_match('/^[A-z]+[A-z0-9\._-]*[@][A-z0-9_-]+([.][A-z0-9_-]+)*[.][A-z]{2,4}$/',$val))
							$res[$key] = $val; //Save the value to the new array
					break;
					case "website":case "url": //A URL
						if(substr($val,0,7) != "http://" && substr($val,0,8) != "https://") 
							$val = "http://$val";
						if(preg_match('/^http{1}s?:{1}\/\/{1}[A-z0-9]+[A-z0-9\-\.]*\.{1}[A-z]{2,4}(\/([a-zA-Z0-9\.\-_])+)*(\/){0,1}$/',$val))
							$res[$key] = $val; //Save the value to the new array
					break;
					case "numeric":case "n":case "num": //Integer number
						if(is_numeric($val) && round($val) == $val)
							$res[$key] = $val; //Save the value to the new array
					break;
					case "decimal":case "d": //Any real number
						if(is_numeric($val))
							$res[$key] = $val; //Save the value to the new array
					break;
					//A string containing leters, numbers, basic symbols, whitespace (no line breaks), and html safe characters.
					case "string":case "s":case "str":case "text":case "phone": 
						if(is_string($val))
							$res[$key] = $val; //Save the value to the new array
					break;
				} 
			}
		}
		return $res;
	}
	return array();
}
function bizdir_clean_output($v,$decode = false) {
	//Clean array to be outputed to HTML
	$res = array();
	if(is_array($v) && count($v) > 0) {
		$v = stripslashes_deep($v);
		foreach($v as $key=>$value)
			if((is_string($key) || is_numeric($key)) && (is_string($value) || is_numeric($value))) {
				$res[$key] = wp_specialchars(bizdir_clean_multiquotes($value),ENT_QUOTES);
				if($decode)
					$res[$key] = str_replace("&#039;","\'",html_entity_decode($res[$key]));
			} elseif((is_string($key) || is_numeric($key)) && is_array($value))
				$res[$key] = bizdir_clean_output($value);
	}
	return $res;
}
function bizdir_clean_multiquotes($string) {
	if(is_string($string)) {
		$res = stripslashes_deep($string);
		do {
			//Remove Multiple Double Quotes
			while($res != str_replace(array('""',"&quot;&quot;"),'"',$res))
				$res = str_replace(array('""',"&quot;&quot;"),'"',$res);
			//Remove Multiple Single Quotes
			while($res != str_replace(array("''","&#039;&#039;"),"'",$res))
				$res = str_replace(array("''","&#039;&#039;"),"'",$res);
			$new_string = str_replace(array("''","&#039;&#039;"),'"',str_replace(array('""',"&quot;&quot;"),"'",$res));
		} while($res != $new_string);
		return $res;
	}
	return $string;
}
function bizdir_trim_array($v) {
	if(is_array($v)) {
		$res = array();
		foreach($v as $key=>$value)
			$res[$key] = trim($value);
		return $res;
	}
	return array();
}

function validateCategory($v,$admin = false) {
	//Trim the content of $_POST
	$post = bizdir_trim_array($_POST);
	//Validate $v
	$errors = array();
	if(!is_array($v))
		return array("Error processing data, please try again.");
	if(empty($v["category"])) 
		$errors[] = empty($post["category"])?"Please enter a category name.<br/>":"Category name must be alpha-numeric.<br/>";
	if(empty($v["description"])) 
		$errors[] = 
			empty($post["description"])?"Please enter a breif description of the company.<br/>":"Description contains invalid character(s)";
	elseif(strlen($v["description"]) > 800) 
		$errors[] = "The description for the company is too long. Please shorten it to 800 characters or less.<br/>";
	return $errors;
}

function validateListing($v,$admin = false) {
	//Set Adjective
	$adjective = $admin?"lister's":"your";
	//Trim the content of $_POST
	$post = bizdir_trim_array($_POST);
	//Validate $v
	$errors = array();
	if(!is_array($v))
		return array("Error processing data, please try again.");
	if(empty($v["name"])) 
		$errors[] = empty($post["name"])?"Please enter $adjective name.<br/>":ucfirst($adjective)." name can only contain letters and spaces.<br/>";
    if (empty($v["category_id"]) || $v['category_id'] == 0) 
        $errors[] = "Please select a catagory.<br />";
	if(empty($v["email"])) 
		$errors[] = empty($post["email"])?"Please enter $adjective email.<br/>":ucfirst($adjective)." email is not valid.<br/>";
	if(empty($v["cName"])) 
		$errors[] = empty($post["cName"])?"Please enter the organization name.<br/>":"Organization name must be alpha-numeric<br/>";
	if(empty($v["description"])) 
		$errors[] = empty($post["description"])?
			"Please enter a brief description for the organization.<br/>":
			"Invalid character(s) in $adjective description.<br/>"
		;
	elseif(strlen($v["description"]) > 800) 
		$errors[] = "The description for the organization is too long. Please shorten it to 800 characters or less.<br/>";
	if(empty($v["website"]) && empty($v["cEmail"]) && empty($v["phone"])) 
		$errors[] = "Please enter at least one method of contact (i.e. a website, an email, and/or a phone number).<br/>";
	if(!empty($post["website"]) && empty($v["website"]))
		$errors[] = "Organization website is not a valid URL.<br/>";
	if(!empty($post["phone"]) && empty($v["phone"]))
		$errors[] = ucfirst($adjective)." phone is not a valid phone number.<br/>";
	if(!empty($post["cEmail"]) && empty($v["cEmail"]))
		$errors[] = "Organization email address is not a valid email.<br/>";
	return $errors;
}
