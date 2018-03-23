<?php
/**
 * Helper functions.
 * 
 * @package ShopSmart
*/

/**
 * Trim array.
 * 
 * @param array $values Array to trim.
 * 
 * @return void
*/
function trim_values(array &$values) {
    foreach ( $values as &$str ) {
        if ( is_string( $str )) {
            trim( $str );
        }
    }
}

/**
 * Create option tag that uses values from an array as the HTML element's
 * content.
 * 
 * @param array $array Key/value paris are elements parameters and values.
 * 
 * @return string Outputs 'option' tag with content.
*/
function optionTag( $array ) {
    foreach ($array as $key => $value) {
        echo "<option value='$key'>$value</option>\n";
    }
}

/**
 * Sets default value of option tag content based on value returned from profile
 * array
 * 
 * 
 * @param text $value Default value for option tag.
 * @param test $text Default text for option tag as string.
 * 
 * @return string Option tag.
*/
function optionTagContent ( $value, $text ) {
    if (! empty( $value )) {
        echo "<option value='$value' selected>$text</option>"; 
    } else {
        echo "<option value='' selected>Please choose ...</option>";
    }
}

/**
 * Convert state name or abbreviation to corresponding abbreviation or name
 * 
 * @param string $strInput State name/abbreviation to convert.
 * @param string $strFormat Format to convert state abbreviation into: 'name', 'abbrev'.
 * 
 * @ return State name or abbreviation as string.
*/
function convertState($strInput, $strFormat='name') {
	$arrStates = array(
		array('abbrev'=>'AL', 'name'=>'Alabama'),
		array('abbrev'=>'AK', 'name'=>'Alaska'),
		array('abbrev'=>'AZ', 'name'=>'Arizona'),
		array('abbrev'=>'AR', 'name'=>'Arkansas'),
		array('abbrev'=>'CA', 'name'=>'California'),
		array('abbrev'=>'CO', 'name'=>'Colorado'),
		array('abbrev'=>'CT', 'name'=>'Connecticut'),
		array('abbrev'=>'DE', 'name'=>'Delaware'),
		array('abbrev'=>'DC', 'name'=>'District of Columbia'),
		array('abbrev'=>'FL', 'name'=>'Florida'),
		array('abbrev'=>'GA', 'name'=>'Georgia'),
		array('abbrev'=>'HI', 'name'=>'Hawaii'),
		array('abbrev'=>'ID', 'name'=>'Idaho'),
		array('abbrev'=>'IL', 'name'=>'Illinois'),
		array('abbrev'=>'IN', 'name'=>'Indiana'),
		array('abbrev'=>'IA', 'name'=>'Iowa'),
		array('abbrev'=>'KS', 'name'=>'Kansas'),
		array('abbrev'=>'KY', 'name'=>'Kentucky'),
		array('abbrev'=>'LA', 'name'=>'Louisiana'),
		array('abbrev'=>'ME', 'name'=>'Maine'),
		array('abbrev'=>'MD', 'name'=>'Maryland'),
		array('abbrev'=>'MA', 'name'=>'Massachusetts'),
		array('abbrev'=>'MI', 'name'=>'Michigan'),
		array('abbrev'=>'MN', 'name'=>'Minnesota'),
		array('abbrev'=>'MS', 'name'=>'Mississippi'),
		array('abbrev'=>'MO', 'name'=>'Missouri'),
		array('abbrev'=>'MT', 'name'=>'Montana'),
		array('abbrev'=>'NE', 'name'=>'Nebraska'),
		array('abbrev'=>'NV', 'name'=>'Nevada'),
		array('abbrev'=>'NH', 'name'=>'New Hampshire'),
		array('abbrev'=>'NJ', 'name'=>'New Jersey'),
		array('abbrev'=>'NM', 'name'=>'New Mexico'),
		array('abbrev'=>'NY', 'name'=>'New York'),
		array('abbrev'=>'NC', 'name'=>'North Carolina'),
		array('abbrev'=>'ND', 'name'=>'North Dakota'),
		array('abbrev'=>'OH', 'name'=>'Ohio'),
		array('abbrev'=>'OK', 'name'=>'Oklahoma'),
		array('abbrev'=>'OR', 'name'=>'Oregon'),
		array('abbrev'=>'PA', 'name'=>'Pennsylvania'),
		array('abbrev'=>'RI', 'name'=>'Rhode Island'),
		array('abbrev'=>'SC', 'name'=>'South Carolina'),
		array('abbrev'=>'SD', 'name'=>'South Dakota'),
		array('abbrev'=>'TN', 'name'=>'Tennessee'),
		array('abbrev'=>'TX', 'name'=>'Texas'),
		array('abbrev'=>'UT', 'name'=>'Utah'),
		array('abbrev'=>'VT', 'name'=>'Vermont'),
		array('abbrev'=>'VA', 'name'=>'Virginia'),
		array('abbrev'=>'WA', 'name'=>'Washington'),
		array('abbrev'=>'WV', 'name'=>'West Virginia'),
		array('abbrev'=>'WI', 'name'=>'Wisconsin'),
		array('abbrev'=>'WY', 'name'=>'Wyoming'),
		array('abbrev'=>'AB', 'name'=>'Alberta'),
		array('abbrev'=>'BC', 'name'=>'British Columbia'),
		array('abbrev'=>'MB', 'name'=>'Manitoba'),
		array('abbrev'=>'NB', 'name'=>'New Brunswick'),
		array('abbrev'=>'NL', 'name'=>'Newfoundland'),
		array('abbrev'=>'NS', 'name'=>'Nova Scotia'),
		array('abbrev'=>'NU', 'name'=>'Nunavut'),
		array('abbrev'=>'ON', 'name'=>'Ontario'),
		array('abbrev'=>'PE', 'name'=>'Prince Edward Island'),
		array('abbrev'=>'QC', 'name'=>'Quebec'),
		array('abbrev'=>'SK', 'name'=>'Saskatchewan'),
		array('abbrev'=>'YT', 'name'=>'Yukon Territory'),
	);

	$strOutput = $strInput;
	$strFormat = strtolower(trim($strFormat));

	foreach ($arrStates as $arrState) {
		foreach ($arrState as $strValue) {
			if (strtolower($strValue) == strtolower(trim($strInput))) {
				if ($strFormat == 'abbrev') {
					$strOutput = $arrState['abbrev'];
				} else {
					$strOutput = $arrState['name'];
				}
			}
		}
	}
	return $strOutput;
}

/**
 * Sets default value for form element.
 * 
 * @param string $var_name Variables key name in session and profile array.
 * @param array $form_data Array whose values are form element values.
 * 
 * @return string Value for form element.
*/
function form_value( $var_name, $form_data ) {
    $value = isset( $form_data[$var_name] ) ? $form_data[$var_name] : '';
    return $value;
}

/**
 * List stores.
 * 
 * @param object[] $store_data Array of store objects.
 * 
 * @return string[] Array of HTML lists.
*/
function list_stores( $store_data ) {
	$return_array = array();
	
	if ( is_array( $store_data )) {
		foreach ( $store_data as $store ) {
			$id = $store->store_id;
			$name = $store->store_name;
			$street = $store->address->address;
			$city = $store->address->city;
			$state = $store->address->state;
			$zip = $store->address->zip_code;
			$phone = $store->tel_number;
			$html = <<<HTML
<li class='list-group-item'>
<address>
<h3><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> 
<a href='./stores.php?action=edit&id=$id'>$name</a></h3>
$street <br/>
$city, $state $zip <br/>
Phone: $phone <br/> 
</address>
<a class="btn btn-default" href='./shop.php?action=create&store=$id'>Create List</a>
</li>
HTML;
			array_push( $return_array, $html );
		}
	} else {
		$return_array[] = 'ADD A STORE';
	}
	return $return_array;
}

/**
 * Lists carts.
 * 
 * @param object[] $cart_data Array of Cart objects.
 * 
 * @return string[] Array of html lists.
*/
function list_carts( $cart_data ) {
    $return_array = array();
    
    if ( is_array( $cart_data )) {
        foreach ($cart_data as $cart ) {
            $cart_id = $cart->cart_id;
            $name = $cart->cart_name;
            $store_id = $cart->store->store_id;
            $list_html = <<<HTML
            <li class='list-group-item'>
            <h3><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> 
            <a href='./stores.php?action=view&id=$store_id'>$name</a></h3>
            <div class="btn-group" role="group">
            <a class="btn btn-default" href='./cart.php?action=view&id=$cart_id''>View List</a> 
            <a class="btn btn-default" href='./cart.php?action=add&id=$cart_id'>Add Item</a> 
            <a class="btn btn-default" href='./shop.php?action=edit&id=$cart_id'>Update List</a> 
            <a class="btn btn-default" href='./shop.php?action=delete&id=$cart_id'>Delete List</a>
            </div>
            </li>
HTML;
            array_push( $return_array, $list_html );
        }
    } else {
        $return_array[] = 'CREATE A NEW LIST';
    }
    return $return_array;
}

/**
 * Lists item in cart.
 * 
 * @param object $item Item object.
 * @param int $cart_id Cart's id.
 * 
 * @return string HTML list item.
*/
function list_item( $item, $cart_id ) {
	$html = '';
	
    if ( is_item( $item )) {
			$item_id = $item->item_id;
			$name = $item->item_name;
			$price = sprintf( "$%s", $item->item_price);
			$count = $item->item_count;
            $html = <<<HTML
            <li class='list-group-item'>
            <h4>$name Quantity: $count, Price: $price</h4>
            <div class="btn-group" role="group">
            <a class="btn btn-default" href='./cart.php?action=update&id=$cart_id&item=$item_id'>Set Quantity</a>
            <a class="btn btn-default" href='./cart.php?action=update&id=$cart_id&item=$item_id'>Set Price</a>
            <a class="btn btn-default" href='./items.php?action=view&id=$item_id'>Compare Prices</a>
            <a class="btn btn-default" href='./cart.php?action=delete&id=$cart_id&item=$item_id'>Delete Item</a>        
            </div>
            </li>
HTML;
        }
    return $html;
}

/**
 * Lists items for a particular category in cart.
 * 
 * @param object $cat Category object.
 * @param object $cart Cart object.
 * 
 * @return string HTML.
*/
function list_cat( $cat, $cart ) {
	$html = '';
	$item_array = array();

	foreach ( $cart->cart_items as $item ) {
		if ( $cat->cat_id == $item->cat->cat_id ) {
			array_push( $item_array, list_item( $item, $cart->cart_id ));
		}
	}

	if (! empty( $item_array )) {
		$cat_name = $cat->cat_name;
		$cat_id = $cat->cat_id;
		$items = implode( "\n", $item_array );
		$html = <<<HTML
<ul class="list-group">
<h3><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> 
$cat_name <a href='./category.php?action=edit&id=$cat_id'><small>Edit Category</small></a></h3>
$items
</ul>
HTML;
	}
	return $html;
}

/**
 * Lists all items
 * 
 * @param object $item_data Items to list.
 * @param int $cart_id Optional.  Cart ID.
 * 
 * @return string[] Array of html lists.
*/
function list_all_items( $item_data, $cart_id = 0 ) {
    $return_array = array();
    
    if ( is_array( $item_data )) {
        foreach ( $item_data as $item ) {
        	$item_id = $item->item_id;
        	$name = empty( $item->company ) ? $item->item_name : sprintf( "%s by %s", $item->item_name, $item->company );
        	$list_html = <<<HTML
<li class="list-group-item">
<h4><a href='./items.php?action=view&id=$item_id'>$name</a> <small>$item_id</small></h4>
<div class="btn-group" role="group">
<a class="btn btn-default" href='./items.php?action=view&id=$item_id'>View Item</a>
<a class="btn btn-default" href='./items.php?action=edit&id=$item_id'>Edit Item</a> 
<a class="btn btn-default" href='./cart.php?action=add&id=$cart_id&item=$item_id'>Add Item to List</a>
</div>
</li>
HTML;
            array_push( $return_array, $list_html );
        }
    } else {
        $return_array[] = 'NO ITEMS IN THIS CATEGORY';
    }
    return $return_array;
}

/**
 * Builds list of item prices.
 * 
 * @param object[] Array of result objects.
 * 
 * @return string[] Array of list elements.
*/
function list_item_prices( $item_data ) {
    $item_prices = array();
    
    if ( is_array( $item_data )) {
        foreach( $item_data as $data ) {
        	$name = $data->store_name;
        	$store_id = $data->store_id;
        	$price = sprintf( "$%02.2f", $data->item_price );
        	$date = $data->date;
            $html = <<<HTML
<li class='list-group-item'>
<h4><a href='./stores.php?action=view&id=$store_id'>$name</a></h4> 
$price, Last Updated: $date
</li>
HTML;
			array_push( $item_prices, $html );
        }
    } else {
        array_push( $item_prices, 'Price comparisons not available for this item.');
    }
    return $item_prices;
}

/**
 * Check to see if value is real cart.
 * 
 * @param mixed $value Value to check.
 * 
 * @return bool True if value is Cart, false otherwise.
*/
function is_cart( $value ) {
    if (! $value instanceof Cart ) {
        return false;
    }
    
    if ( $value->cart_exists() ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check to see if value is real item.
 * 
 * @param mixed $value Value to check.
 * 
 * @return bool True if value is Item, false otherwise.
*/
function is_item( $value ) {
    if (! $value instanceof Item ) {
        return false;
    }
    
    if ( $value->item_exists() ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check to see if value is real category.
 * 
 * @param mixed $value Value to check.
 * 
 * @return bool True if value is Category, false otherwise.
*/
function is_cat( $value ) {
    if (! $value instanceof Category ) {
        return false;
    }
    
    if ( $value->cat_exists() ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check to see if value is real member.
 * 
 * @param mixed $value Value to check.
 * 
 * @return bool True if value is Member, false otherwise.
*/
function is_member( $value ) {
    if (! $value instanceof Member ) {
        return false;
    }
    
    if ( $value->member_exists() ) {
        return true;
    } else {
        return false;
    }
}
?>