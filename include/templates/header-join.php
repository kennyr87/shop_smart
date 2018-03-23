<!DOCTYPE html>
<html lang='eng'>
<head>
<!-- SITE META -->
<meta charset="utf-8">
<title><?php echo $data['title'] . ' ' . SITETITLE; ?></title>

<!-- CSS -->
<?php Assets::css( array( './assets/css/bootstrap/css/bootstrap.css', 
                          './assets/css/style.css' )); ?>
</head>

<body>
<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header navbar-left">
      <a class="navbar-brand" href="index.php">
        <img alt="Brand" src="./assets/images/shop_smart.jpg">
      </a>
        <form class="navbar-form navbar-left" action="./login.php?action=login" method="POST">
          <div class="form-group">
            <input type="text" name='email' class="form-control" placeholder="Email" />
            <input type='password' name='password' class='form-control' placeholder='Password' />
          </div>
          <button type="submit" class="btn btn-default">Log In</button>
        </form>
    </div>        
  </div>
</nav>