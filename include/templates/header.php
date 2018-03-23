<?php

/**
 * Default header.
 * 
 * @package ShopSmart
*/

#-------------------#
# User Variables    #
#-------------------#

$name = empty( $data['name'] ) ? '' : $data['name'];
?>
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
      <ul class="nav navbar-nav">
        <li id='nav-shop'><a href="./shop.php"><h3>Shop</h3></a></li>
        <li id='nav-items'><a href="./items.php"><h3>Items</h3></a></li>
        <li id='nav-stores'><a href="./stores.php"><h3>Stores</h3></a></li>
      </ul>
    </div>
    <div class="navbar-header navbar-right">
      <ul class="nav navbar-nav">
        <li><a href="#"><h3><?php echo $name; ?></h3></a></li>
        <li>
        <form method="POST" action='./login.php?action=logout'>
            <button type="submit" class="btn btn-default navbar-btn">Sign Out</button>
        </form>
        </li>  
      </ul>
    </div>
  </div>
</nav>