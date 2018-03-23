<?php
/**
 * Lists food items.
 * 
 * @package ShopSmart
*/

#-------------------#
# User Variables    #
#-------------------#

$items = implode( "\n", $data['items'] );
$cat_data = $data['cat_data'];
$cat_id = $data['cat_id'];
$cat_name = $data['cat_name'];
$cat_link = "<a href='./category.php?action=edit&id=$cat_id'>Edit Category</a>";
$cat_header = empty( $cat_name ) ? 'All Items' : sprintf( "%s <small>%s</small>", $cat_name, $cat_link );
?>
<div class='container items-page'>
    <div class='row'>
    <div class='col-md-12 page-header well well-lg'>
        <h1>Item Catalog <small>Sort Items by Category</small></h1>
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

    <!-- CATEGORY FORM -->
    <form action='./category.php?action=view' method='GET' class="form-inline">
        <div class='form-group'>
            <label for='id'>Category</label>
            <select class='form-control' name='id'>
            <?php optionTagContent( $cat_id, $cat_name ); ?>
            <option value=''>All Items</option>
            <?php optionTag( $data['cat_data']); ?>
            </select>            
        </div>
        <input type='hidden' name='action' value='view' />
        <button type='submit' class="btn btn-default">Sort</button>
    </form>
    
    <div class='row'>
        <h3><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> <?php echo $cat_header; ?></h3>
    </div>
    
    <div class='row'>
    <div class='col-md-8'>
        <!-- ITEM LIST -->
        <ul class="list-group">
        <?php echo $items; ?>
        </ul>
    </div>
    </div>

    <div class='row'>
        <a class="btn btn-default btn-lg" href='./items.php?action=create' role="button">Add a New Item</a>
    </div>
</div>