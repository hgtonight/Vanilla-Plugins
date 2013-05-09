jQuery(document).ready(function($){
	// Find all panel boxes
	$('#Panel .Box:not(.Group)').addClass('PanelBoxToggle');
	
	// Add click handler and trigger it
	$('.PanelBoxToggle').click(function(event) {	
		if($(this).children('h4').length > 0) {
			event.preventDefault();
			$(this).toggleClass('Collapsed');
			$(this).children(':not(:first-child)').slideToggle();
		}
	});
	
	// Collapse all the panels by default
	$('.PanelBoxToggle').children('h4 ~ *').hide();
	
	// Prevent links from closing the panel box
	$('.PanelBoxToggle a').click(function(event) {
		event.stopPropagation();
	});	
});
