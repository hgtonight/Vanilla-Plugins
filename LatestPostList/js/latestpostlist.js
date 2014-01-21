/* Copyright 2013 Zachary Doll */
$(function(){
	// Set variables from configs
	var Effects = gdn.definition('LatestPostListEffects');
	var Frequency = gdn.definition('LatestPostListFrequency');
   	var LastDate = gdn.definition('LatestPostListLastDate');

	function GetLatestPosts() {
		var url = gdn.url('/plugin/latestpostlist/getnewlist');
		
		$.ajax({
			url: url,
			global: false,
			type: "GET",
			data: null,
			dataType: "json",
			success: function(Data){
				if(Data.date != LastDate) {
					LastDate = Data.date;
					console.log(Effects);
					switch(Effects) {
					case '1':
						var newListItems = $('<ul id="LPLNUl" />').html(Data.list).contents();
						$("#LPLUl li").each( function( index ) {
							$(this).delay(200 * index).hide('slow', function() {
								$(this).html(newListItems.filter(':nth-child('+(index+1)+')').html());
								$(this).show('slow');
							});
						});
						break;
					case '2':
						$("#LPLUl").fadeOut('slow', function() {
							$(this).html(Data.list);
							$("#LPLUl").fadeIn('slow');
						});
						break;
					case '3':
						var newListItems = $('<ul id="LPLNUl" />').html(Data.list).contents();
						$("#LPLUl li").each( function( index ) {
							$(this).delay(200 * index).fadeOut('slow', function() {	
								$(this).html(newListItems.filter(':nth-child('+(index+1)+')').html());
								$(this).fadeIn(350);
							});
						});
						break;
					case '4':
						var newListItems = $('<ul id="LPLNUl" />').html(Data.list).contents();
						$("#LPLUl li").each( function( index ) {
							$(this).delay(200 * index).slideToggle('slow', function() {
								$(this).html(newListItems.filter(':nth-child('+(index+1)+')').html());
								$(this).slideToggle('slow');
							});
						});
						break;
					case '5':
						var newListItems = $('<ul id="LPLNUl" />').html(Data.list).contents();
						$("#LPLUl li").each( function( index ) {
							var oldHeight = $(this).height();
							$(this).delay(200 * index).animate({opacity: 'toggle', width: 'toggle', height: oldHeight}, 'slow', function() {
								$(this).html(newListItems.filter(':nth-child('+(index+1)+')').html());
								$(this).animate({opacity: 'toggle', width: 'toggle', height: oldHeight}, 'slow', function() {
									$(this).css({height: ''});
								});
								
							});
						});
						break;
					default:
					case 'none':
						$("#LPLUl").html(Data.list);
						break;
					}
				}
				setTimeout(GetLatestPosts, Frequency * 1000);
			}
		});
	}
	
	if (Frequency > 0) {
		setTimeout(GetLatestPosts, Frequency * 1000);
	}
});