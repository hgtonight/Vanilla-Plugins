/* Copyright 2013 Zachary Doll */
$(function(){
	function PreviewPostUpdate(Effect) {
		var newListItems = $('#LPLNewItems');
		switch(Effect) {
		case 'Rolling Hide':
			$("#LPLPreview li").each( function( index ) {
				$(this).delay(200 * index).hide('slow', function() {
					$(this).html(newListItems.find(':nth-child('+(index+1)+')').html());
					$(this).show('slow');
				});
			});
			break;
		case 'Full Fade':
			$("#LPLPreview").fadeOut('slow', function() {
				$(this).html(newListItems.html());
				$("#LPLPreview").fadeIn('slow');
			});
			break;
		case 'Rolling Fade':
			$("#LPLPreview li").each( function( index ) {
				$(this).delay(200 * index).fadeOut('slow', function() {	
					$(this).html(newListItems.find(':nth-child('+(index+1)+')').html());
					$(this).fadeIn(350);
				});
			});
			break;
		case 'Rolling Slide':
			$("#LPLPreview li").each( function( index ) {
				$(this).delay(200 * index).slideToggle('slow', function() {
					$(this).html(newListItems.find(':nth-child('+(index+1)+')').html());
					$(this).slideToggle('slow');
				});
			});
			break;
		case 'Rolling Width Fade':
			$("#LPLPreview li").each( function( index ) {
				var oldHeight = $(this).height();
				$(this).delay(200 * index).animate({opacity: 'toggle', width: 'toggle', height: oldHeight}, 'slow', function() {
					$(this).html(newListItems.find(':nth-child('+(index+1)+')').html());
					$(this).animate({opacity: 'toggle', width: 'toggle', height: oldHeight}, 'slow', function() {
						$(this).css({height: ''});
					});
					
				});
			});
			break;
		default:
		case 'none':
			$("#LPLPreview").html(newListItems.html());
			break;
		}
	}
	
	$('#Form_Plugins-dot-LatestPostList-dot-Effects').change( function() {
		PreviewPostUpdate($(this).find(':selected').text());
	});
});