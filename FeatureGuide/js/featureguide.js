/* Copyright 2013 Zachary Doll */
$(function(){
	var guide = {
		id: 'jQuery.FeatureGuide',
		title: "Take a quick tour of our forums's features",
		steps: [
			{
			target: '#Menu, .SiteMenu',
			content: 'Each step is associated with a "target" element, specified by a CSS selector. jQuery.PageGuide will automatically filter out any steps with targets that are invisible or don\'t exist..',
			direction: 'left'
			},
			{
			target: '#Panel',
			content: 'Each step is associated with a "target" element, specified by a CSS selector. jQuery.PageGuide will automatically filter out any steps with targets that are invisible or don\'t exist..',
			direction: 'left'
			},
			
		]
    }
	
	$.pageguide(guide);
});
    