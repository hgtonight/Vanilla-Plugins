/* Copyright 2013 Zachary Doll */
$(function(){
	// store element for performance.
	$('#JumpToTop').hide();
	
	// Fade in.out on scroll
	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$('#JumpToTop').fadeIn();
			} else {
				$('#JumpToTop').fadeOut();
			}
		});
		 
		// scroll body to 0px on click
		$('#JumpToTop a').click(function (event) {
			event.preventDefault();
			$('body, html').animate({scrollTop: 0}, 800);
		});
	});
});