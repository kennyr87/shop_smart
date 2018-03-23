<?php
/**
 * Form to to add/edit new category.
 * 
 * @package ShopSmart
*/

#-------------------#
# User Variables    #
#-------------------#

$cat_id = form_value( 'cat_id', $data );
$cat_name = form_value( 'cat_name', $data );
$heading = isset( $data['update'] ) ? 'Update ' . $cat_name . ' Category' : 'Add New Category';
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
    
    <!-- CATEGORY FORM -->
    <form class="form-inline" action='<?php echo getenv( 'SCRIPT_NAME' ) . '?action=new'; ?>' method='POST'>
        <input type='hidden' name='cat_id' value='<?php echo $cat_id; ?>' />
        <div class='form-group'>
            <label for='cat_name'>Category Name</label>
            <input class='form-control' type="text" name="cat_name" value='<?php echo $cat_name; ?>' required /> 
        </div>
        <button type="submit" class='btn btn-default'>Submit</button>
    </form>
</div>
