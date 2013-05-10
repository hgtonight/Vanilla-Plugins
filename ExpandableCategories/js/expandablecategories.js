jQuery(document).ready(function($){
	// Find parent categories
	$('ul.PanelCategories li.Depth1, ul.PanelCategories li.Heading')
		.next('li.Depth2')
		.prev()
		.addClass('Leader');
	
	// Add click handler
	$('ul.PanelCategories li.Leader').click(function(event) {	
		if($(this).next().is('li.Depth2')) {
			event.preventDefault();
			event.stopPropagation();
			$(this).toggleClass('Expanded');
			$(this).nextUntil('li.Leader, li.Depth1').slideToggle();
		}
	});
	
	// Hide all the sub categories by default
	$('ul.PanelCategories li:not(.Depth1, :first)').hide();
	
	// Show the active subcategory (and siblings) if wanted
	if(gdn.definition('ExpandActiveOnLoad')) {
		$('ul.PanelCategories li.Active').prevAll('li.Leader:first').addClass('Expanded').next().nextUntil('.Leader').andSelf().show();
	}
});
