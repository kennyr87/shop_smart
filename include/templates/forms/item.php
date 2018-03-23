<?php
/**
 * Form to create and update food items.
 * 
 * @package ShopSmart
*/

#-------------------#
# User Variables    #
#-------------------#

$item_id = form_value( 'item_id', $data );
$company = form_value( 'company', $data );
$item_name = form_value( 'item_name', $data );
$item_unit = form_value( 'item_unit', $data );
$item_size = form_value( 'item_size', $data );
$cat_id = form_value( 'cat_id', $data );
$cat_name = form_value( 'cat_name', $data );
$cat_data = form_value( 'cat_data', $data );
$heading = $data['update'] ? "$item_name <small>Edit Item Details</small>" : 'Add New Item';
?>
<div class='container'>
    <div class='row'>
    <div class='col-md-12 page-header well well-lg'>
        <h1><?php echo $heading; ?></h1>
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
    <form class="form-horizontal" action='<?php echo getenv( 'SCRIPT_NAME' ) . '?action=new'; ?>' method='POST'>
        <div class='form-group'>
            <label for='item_id' class='col-md-1 control-label'>Barcode (PLU)</label>
            <div class='col-md-4'>
                <input class="form-control" type='text' name='item_id' value='<?php echo $item_id; ?>' required />
            </div>
        </div>
        
        <div class='form-group'>
            <label for='company' class='col-md-1 control-label'>Company</label>
            <div class='col-md-4'>
                <input class="form-control" type='text' name='company' value='<?php echo $company; ?>' />
            </div>
        </div>
        
        <div class='form-group'>
            <label for='item_name' class='col-md-1 control-label'>Name</label>
            <div class='col-md-4'>
                <input class="form-control" type='text' name='item_name' value='<?php echo $item_name; ?>' required />
            </div>
        </div>
        
        <div class='form-group'>
            <label for='item_size' class='col-md-1 control-label'>Size</label>
            <div class='col-md-4'>
                <input class="form-control" type='text' name='item_size' value='<?php echo $item_size; ?>' required />
            </div>
        </div>
        
        <div class='form-group'>
            <label for='item_unit' class='col-md-1 control-label'>Unit</label>
            <div class='col-md-4'>
                <select class="form-control" name='item_unit' required>
                    <?php echo optionTagContent( $item_unit, $item_unit ); ?>
                    <option value="Each">Each</option>
                    <option value='OZ'>OZ</option>
                    <option value='g'>g</option>
                    <option value='LB'>LB</option>
                    <option value='kg'>kg</option>
                    <option value='FL OZ'>FL OZ</option>
                    <option value='L'>L</option>
                    <option value='GAL'>GAL</option>
                    <option value='PT'>PT</option>
                    <option value='QT'>QT</option>
                    <option value='ml'>ml</option>
                </select>
            </div>
        </div>
        
        <div class='form-group'>
            <label for='cat_id' class='col-md-1 control-label'>Category</label>
            <div class='col-md-4'>
                <select class="form-control" name='cat_id' required>
                    <?php optionTagContent( $cat_id, $cat_name ); ?>
                    <?php optionTag( $cat_data ); ?>
                </select> 
            </div>
        </div>
        
        <div class='form-group'>
            <label class="sr-only">New Category</label>
            <div class='col-md-offset-1 col-md-2'>
                <p class="form-control-static">Don't See a Category?</p>
            </div>
            <div class='col-md-9'>
                <a class="btn btn-default" href='./category.php?action=add' role="button">Add New Category</a>
            </div>
        </div>
        
        <div class='form-group'>
            <div class='col-md-offset-1 col-md-11'>
                <button type='submit' class="btn btn-default">Submit</button>
            </div>
        </div>
    </form>
</div>