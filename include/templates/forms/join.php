<?php
/**
 * Form to sign up.
 * 
 * @package ShopSmart
*/

#-------------------#
# User Variables    #
#-------------------#

$name = form_value( 'name', $data );
$email =  form_value( 'email', $data );
$password = form_value( 'password', $data );
?>
<div class="jumbotron">
<div class='container'>
  <h1>Welcome to ShopSmart!</h1>
  <p>ShopSmart is an easy way to create and organize your shopping lists.</p>
  <p>Best of all, it's free!</p>
</div>
</div>

<div class='container'>
    
<div class='row'>
    <!-- ERROR MESSAGE -->
    <?php if (! empty( $error )): ?>
    <div class='col-md-12 alert alert-danger' role='alert'>
    <p><?php echo $error; ?></p>
    </div>
    <?php endif; ?>
</div>
<div class='row'>
    <div class='col-md-offset-1 col-md-5'>
        <h3>ShopSmart Features</h3>
        <h4><span class="glyphicon glyphicon-plus"></span>
        Add your own items or lookup items added by other members.</h4>
        <h4><span class="glyphicon glyphicon-plus"></span>
        Compare prices between local retailers.</h4>
        <h4><span class="glyphicon glyphicon-plus"></span>
        Create lists for multiple retailers.</h4>
    </div>
    <div class='col-md-6'>
    <div class='col-md-offset-2 col-md-10'>
        <h3>Sign Up</h3>
    </div>
    <!-- LOGIN FORM -->
    <form action="./join.php?action=new" method="POST" class='form-horizontal input-lg'>
        <div class='form-group'>
        <label for="name" class="col-md-3 control-label">Name</label>
        <div class='col-md-8'>
            <input class='form-control' type="text" name="name" placeholder='Name' value='<?php echo $name; ?>' required/>
        </div>
        </div>
        <div class='form-group'>
        <label for="email" class="col-md-3 control-label">Email</label>
        <div class='col-md-8'>
            <input class='form-control' type="text" name="email" placeholder='Email' value='<?php echo $email; ?>' required/>
        </div>
        </div>
        <div class='form-group'>
        <label for="password" class="col-md-3 control-label">Password</label>
        <div class='col-md-8'>
            <input class='form-control' type="password" name="password" placeholder='Password' value='<?php echo $password; ?>' required/>  
        </div>
        </div>
        <div class='form-group'>
        <div class='col-md-offset-3 col-md-9'>
            <button type="submit" class="btn btn-default">Sign Up</button>    
        </div>
        </div>
    </form>
    </div>
</div>
</div>
