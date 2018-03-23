<?php
/**
 * View items in shopping cart.
 * 
 * @package ShopSmart
*/

#-------------------#
# User Variables    #
#-------------------#

$cart_name = $data['cart_name'];
$cart_id = $data['cart_id'];
$items = $data['items'];
?>

<div class='container'>
    <div class='row'>
    <div class='col-md-12 page-header well well-lg'>
        <h1><?php echo $cart_name; ?> Shopping List <small>Edit your shopping list</small></h1>
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
    
    <div class='row'>
    <div class='col-md-8'>
        <!-- CART ITEMS -->
        <?php echo $items; ?>
    </div>
    </div>

    <div class='row'>
        <a class="btn btn-default btn-lg" href='./cart.php?action=add&id=<?php echo $cart_id; ?>' role="button">Add Item to List</a>
    </div>
</div>