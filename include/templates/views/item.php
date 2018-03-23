<?php
/**
 * View details and prices for individual item.
 * 
 * @package ShopSmart
*/

#-------------------#
# User Variables    #
#-------------------#

$item_name = $data['item_name'];
$item_id = $data['item_id'];
$description = $data['description'];
$item_prices = implode( "\n", $data['item_prices'] );
?>
<div class='container'>
    <div class='row'>
    <div class='col-md-12 page-header well well-lg'>
        <h1><?php echo $item_name; ?> <small>Item Description</small></h1>
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
    
    <!-- ITEM DETAILS -->
    <div class='row'>
    <div class='col-md-8'>
        <p class="lead"><?php echo $description; ?></p>
    </div>
    </div>
    
    <!-- ITEM PRICES -->
    <div class='row'>
    <div class='col-md-8'>
        <h3>Local Retailer Prices</h3>
        <ul class="list-group">
            <?php echo $item_prices; ?>
        </ul>
    </div>
    </div>
    <br/>
    <div class='row'>
        <div class="btn-group" role="group">
            <a class="btn btn-default" role="button" href='./items.php?action=edit&id=<?php echo $item_id; ?>'>Edit Item Details</a> 
            <a class="btn btn-default" role="button" href='./cart.php?action=add&item=<?php echo $item_id; ?>'>Add Item to List</a>
        </div>
    </div>
</div>