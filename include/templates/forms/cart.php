<?php
/**
 * Form to create new shopping list.
 * 
 * @package ShopSmart
*/

#-------------------#
# User Variables    #
#-------------------#

$member_id = form_value( 'member_id', $data );
$cart_name = form_value( 'cart_name', $data );
$store_id = form_value( 'store_id', $data );
$store_name = form_value( 'store_name', $data );
$cart_id = form_value( 'cart_id', $data );
$heading = isset( $data['edit'] ) ? "Update list's name" : 'Name your list';

?>

<div class='container'>
<div class='row'>
    <div class='page-header well well-lg'>
        <h1>Shopping List for <?php echo $store_name; ?> <small><?php echo $heading; ?></small></h1>
    </div>
</div>

<div class='row'>
    <?php if (! empty( $error )): ?>
    <div class='alert alert-danger' role='alert'>
    <p><?php echo $error; ?></p>
    </div>
    <?php endif; ?>
</div>

<div class='row'>
    <!-- CART FORM -->
    <form action='<?php echo getenv( 'SCRIPT_NAME' ) . '?action=new'; ?>' method='POST' class="form-inline">
    <div class='form-group'>
        <label for='cart_name'>List Name</label>
        <input type="text" name="cart_name" class="form-control" value='<?php echo $cart_name; ?>' required />              
    </div>
    <input type='hidden' name='member_id' value='<?php echo $member_id; ?>' />
    <input type='hidden' name='store_id' value='<?php echo $store_id; ?>' />
    <input type='hidden' name='cart_id' value='<?php echo $cart_id; ?>' />
    <button type="submit" class="btn btn-default">Submit</button>    
    </form>
</div>
<br/>
<div class='row'>
    <a class='btn btn-default' href='./cart.php?action=view&id=<?php echo $cart_id; ?>'>Edit List Items</a>
</div>
</div>
