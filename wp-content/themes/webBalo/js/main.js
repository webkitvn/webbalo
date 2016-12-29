jQuery('.woocommerce-message').on('click', function(){
	jQuery(this).fadeOut(500);
})

jQuery.scrollSpeed(100, 800);

jQuery(document).ready(function() {
   	jQuery("#menu ul.menu > li").click(function(){
   		var sub_menu = jQuery(this).find("ul.sub-menu");
  		if(!sub_menu.hasClass("active")){
  			sub_menu.slideDown(500);
  			sub_menu.addClass("active");
  		}
  		else{
  			sub_menu.slideUp(500);
  			sub_menu.removeClass("active");
  		}
  	});
  	jQuery("#striger-btn").click(function(){
  		if(!jQuery("#menu").hasClass('active')){ //Menu is unactive
  			jQuery("#menu").animate(
  				{
  					opacity: 1,
				    right: "0"
				}, 300, function(){
					jQuery("#menu").addClass("active");
				})
  		}
  		else{ //Menu is active
  			jQuery("#menu").animate(
  				{
				    opacity: 0,
				    right: "-500px"
				}, 300, function(){
					jQuery("#menu").removeClass("active");
				})
  		}
  		return false;
  	});
    jQuery("#loginModal a.um-button").click(function(){
      jQuery("#loginModal").modal('hide');
      jQuery("#registerModal").modal('show');
      return false;
    })
    jQuery("#registerModal a.um-button").click(function(){
      jQuery("#registerModal").modal('hide');
      jQuery("#loginModal").modal('show');
      return false;
    })
    jQuery('.tobottom').click(function(){
      jQuery('html,body').animate({
        scrollTop: jQuery("#lienhe").offset().top},
        'slow', function(){
            jQuery("#menu").animate(
            {
                opacity: 0,
                right: "-500px"
            }, 300, function(){
              jQuery("#menu").removeClass("active");
            })
        });
    })
 });

jQuery(window).scroll(function(){
    if (jQuery(this).scrollTop() > jQuery("#header").height()) {
      jQuery("#header").addClass('fixed-menu');
    }
    else{
      jQuery("#header").removeClass('fixed-menu');
    }
 });
 
jQuery("#searchform form a.search-btn").click(function(){
  jQuery("#searchform form button").css("display", "block");
  jQuery(this).css("display", "none");
  jQuery("#searchform input[type=search]").css("visibility", "visible").focus();
  return false;
})
 
 jQuery(".backtotop").click(function(){
   jQuery('html, body').animate({scrollTop : 0},500);
        return false;
 })