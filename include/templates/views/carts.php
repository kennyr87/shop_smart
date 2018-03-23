<?php
/**
 * View all shopping carts.
 * 
 * @package ShopSmart
*/

#-------------------#
# User Variables    #
#-------------------#

$lists = implode( "\n", $data );

?>

<div class='container carts-page'>
    <div class='row'>
    <div class='col-md-12 page-header well well-lg'>
        <h1>Your Shopping Lists <small>Create retailer specific shopping lists</small></h1>
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
        <!-- LIST CARTS -->
        <div class='col-md-8'>
            <ul class="list-group">
            <?php echo $lists; ?>
            </ul>
        </div>
    </div>
    <div class='row'>
            <a class="btn btn-default btn-lg" href='./stores.php' role="button">New List</a>            
    </div>
</div>
