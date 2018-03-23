<?php
/**
 * Form to update prices and count of items in cart.
 * 
 * @package ShopSmart
*/

#-------------------#
# User Variables    #
#-------------------#

$lists = $data['lists'];
$cart_id = form_value( 'cart_id', $data );
$cart_name = form_value( 'cart_name', $data );
$item_id = form_value( 'item_id', $data );
$item_name = form_value( 'item_name', $data );
$item_count = form_value( 'item_count', $data );
$item_price = form_value( 'item_price', $data );
$heading = $data['update'] ? 'Update Item' : 'Add Item to List';
?>
<div class='container'>
    <div class='row'>
    <div class='col-md-12 page-header well well-lg'>
        <h1><?php echo $item_name; ?> <small><?php echo $heading; ?></small></h1>
    </div>
    </div>
    
    <div class='row'>
        <!-- ERROR MESSAGE -->
        <?php if (! empty( $error )): ?>
        <div class='alert alert-danger' role='alert'>
        <p><?php echo $error; ?></p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- HTML FORM --> 
    <form action='<?php echo getenv( 'SCRIPT_NAME' ) . '?action=new'; ?>' method='POST' class="form-horizontal">
        
        <div class='form-group'>
            <label for='cart_id' class="col-md-1 control-label">Shopping List</label>
            <div class='col-md-4'>
            <select class="form-control" name='cart_id'>
                <?php optionTagContent( $cart_id, $cart_name ); ?>
                <?php optionTag( $lists ); ?>                
            </select>
            </div>
        </div>
        <input type='hidden' name='item_id' value='<?php echo $item_id; ?>' />
        <input type='hidden' name='item_name' value='<?php echo $item_name; ?>' />
        <div class='form-group'>
            <label for='item_count' class="col-md-1 control-label">Quantity</label>
            <div class='col-md-4'>
            <input type='text' name='item_count' class="form-control" value='<?php echo $item_count; ?>' required />    
            </div>
        </div>
        <div class='form-group'>
            <label for='item_price' class='col-md-1 control-label'>Price</label>
            <div class='col-md-4'>
                <div class='input-group'>
                <span class="input-group-addon">$</span>
                <input type='text' name='item_price' class="form-control" value='<?php echo $item_price; ?>' required />
                </div>
            </div>
        </div>
        <div class='form-group'>
        	<div class='col-md-offset-1 col-md-11'>
            <button type='submit' class="btn btn-default">Submit</button> 
            </div>   
        </div>
    </form>
    <br/>
    <div class='row'>
        <div class='col-md-12'>
        <a class="btn btn-default btn-lg" href='./items.php?action=edit&id=<?php echo $item_id; ?>' role="button">Edit Item Details</a>            
        </div>
    </div>
</div>