/*!
* ShopSmart: main.js
*/

// Adds breadcrumb to nav when carts, items, store pages loads
$( function() {
  if ( $('div').is('.carts-page') ) {
      $( '#nav-shop' ).addClass( 'active' );
  } else if ( $('div').is('.items-page') ) {
      $( '#nav-items' ).addClass( 'active' );
  } else if ( $('div').is('.stores-page') ) {
      $( '#nav-stores' ).addClass( 'active' );
  }
});