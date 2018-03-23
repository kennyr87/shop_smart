<?php
/**
 * Lists all stores.
 * 
 * @package ShopSmart
*/

#-------------------#
# User Variables    #
#-------------------#

$stores = implode("\n", $data );
?>
<div class='container stores-page'>
    <div class='row'>
    <div class='col-md-12 page-header well well-lg'>
        <h1>Store/Retail Locations<small>&nbsp;Create lists linked to stores</small></h1>
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
        <!-- LIST STORES -->
        <div class='col-md-8'>
            <ul class="list-group">
            <?php echo $stores; ?>
            </ul>            
        </div>
    </div>
    
    <div class='row'>
        <a class="btn btn-default btn-lg" role='button' href='./stores.php?action=create'>Add Store</a>
    </div>
</div>