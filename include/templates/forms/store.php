<?php
/**
 * Form to create new store.
 * 
 * @package ShopSmart
*/

#-------------------#
# User Variables    #
#-------------------#

$store_id = form_value( 'store_id', $data );
$store_name = form_value( 'store_name', $data );
$tel_number = form_value( 'tel_number', $data );
$address = form_value( 'address', $data );
$city = form_value( 'city', $data);
$state = form_value( 'state', $data );
$zip_code = form_value( 'zip_code', $data );
$error_msg = form_value( 'error_msg', $data );
$heading = isset( $data['edit'] ) ? "$store_name <small>Edit Store</small>" : 'Add New Store';

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
    
    <!-- STORE FORM -->
    <form class='form-horizontal' action="<?php echo getenv( 'SCRIPT_NAME' ) . '?action=new'; ?>" method="POST">
        <input type='hidden' name='store_id' value='<?php echo $store_id; ?>' />
        <div class='form-group'>
            <label for='store_name' class='col-md-1 control-label'>Name</label>
            <div class='col-md-4'>
                <input class="form-control" type="text" name="store_name" value='<?php echo $store_name; ?>' required />
            </div>
        </div>
        
        <div class='form-group'>
            <label for='tel_number' class='col-md-1 control-label'>Phone</label>
            <div class='col-md-4'>
                <input class="form-control" type='text' name='tel_number' placeholder='000-000-0000' value='<?php echo $tel_number; ?>' required />
            </div>
        </div>
        
        <div class='form-group'>
            <label for='address' class='col-md-1 control-label'>Address</label>
            <div class='col-md-4'>
                <input class="form-control" type="text" name="address" value='<?php echo $address; ?>' required />
            </div>
        </div>
        
        <div class='form-group'>
            <label for='city' class='col-md-1 control-label'>City</label>
            <div class='col-md-4'>
                <input class="form-control" type="text" name="city" value='<?php echo $city; ?>' required />
            </div>
        </div>
        
        <div class='form-group'>
            <label for='state' class='col-md-1 control-label'>State</label>
            <div class='col-md-4'>
            <select class="form-control" name='state' required>
                <?php echo optionTagContent( $state, convertState( $state )); ?>
                <option value="AL">Alabama</option>
                <option value="AK">Alaska</option>
                <option value="AZ">Arizona</option>
                <option value="AR">Arkansas</option>
                <option value="CA">California</option>
                <option value="CO">Colorado</option>
                <option value="CT">Connecticut</option>
                <option value="DE">Delaware</option>
                <option value="DC">District of Columbia</option>
                <option value="FL">Florida</option>
                <option value="GA">Georgia</option>
                <option value="HI">Hawaii</option>
                <option value="ID">Idaho</option>
                <option value="IL">Illinois</option>
                <option value="IN">Indiana</option>
                <option value="IA">Iowa</option>
                <option value="KS">Kansas</option>
                <option value="KY">Kentucky</option>
                <option value="LA">Louisiana</option>
                <option value="ME">Maine</option>
                <option value="MD">Maryland</option>
                <option value="MA">Massachusetts</option>
                <option value="MI">Michigan</option>
                <option value="MN">Minnesota</option>
                <option value="MS">Mississippi</option>
                <option value="MO">Missouri</option>
                <option value="MT">Montana</option>
                <option value="NE">Nebraska</option>
                <option value="NV">Nevada</option>
                <option value="NH">New Hampshire</option>
                <option value="NJ">New Jersey</option>
                <option value="NM">New Mexico</option>
                <option value="NY">New York</option>
                <option value="NC">North Carolina</option>
                <option value="ND">North Dakota</option>
                <option value="OH">Ohio</option>
                <option value="OK">Oklahoma</option>
                <option value="OR">Oregon</option>
                <option value="PA">Pennsylvania</option>
                <option value="RI">Rhode Island</option>
                <option value="SC">South Carolina</option>
                <option value="SD">South Dakota</option>
                <option value="TN">Tennessee</option>
                <option value="TX">Texas</option>
                <option value="UT">Utah</option>
                <option value="VT">Vermont</option>
                <option value="VA">Virginia</option>
                <option value="WA">Washington</option>
                <option value="WV">West Virginia</option>
                <option value="WI">Wisconsin</option>
                <option value="WY">Wyoming</option>
            </select>
            </div>
        </div>
        
        <div class='form-group'>
            <label for='zip_code' class='col-md-1 control-label'>Zip Code</label>
            <div class='col-md-4'>
                <input class="form-control" type="text" name="zip_code" value='<?php echo $zip_code; ?>' required />
            </div>
        </div>
        <div class='form-group'>
            <div class='col-md-offset-1 col-md-11'>
            <button type="submit" class='btn btn-default'>Submit</button>
            </div>
        </div>
    </form>
</div>