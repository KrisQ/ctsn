/*************************************
 * Ajax Requesst for Cart
 *************************************/
jQuery(document).ready(function(){
  $('.add-to-cart').on('submit',function(e){
    e.preventDefault();
    $.ajax({
        type: 'POST',
        url: '/cart/ajax_submit',
        data: $(this).serialize(),
        success: function(data) {
          alert('Item Added to cart!');
        },
        error: function(xhr, desc, err) {
        }
    });
    return false;
  });
});
