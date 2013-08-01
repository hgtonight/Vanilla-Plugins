/* Copyright 2013 Zachary Doll */
$(function(){
	var guide = {
		id: 'jQuery.FeatureGuide',
		title: "Take a quick tour of our forums's features",
		steps: [
			{
			target: '#Menu, .SiteMenu',
			content: 'Discussions will list all the latest discussions newest first. Activity shows member activity.',
			direction: 'bottom'
			},
			{
			target: '.SiteSearch',
			content: 'Use this to search for specific topics, users, etc.',
			direction: 'bottom'
			},
			{
			target: '.MeBox',
			content: 'This panel is all about you. It gives you access to your notifications, private messages, bookmarks, and settings.',
			direction: 'left'
			},
			{
			target: '.BoxNewDiscussion',
			content: 'Use this button to start a new discussion in the current category.',
			direction: 'right'
			},
			{
			target: '.BoxDiscussionFilter',
			content: 'This panel lists all the categories you currently follow.',
			direction: 'left'
			},
			{
			target: '.BoxCategories',
			content: 'This panel lists all the categories you currently follow.',
			direction: 'left'
			},
		]
    }
	
	$.pageguide(guide);
});
    