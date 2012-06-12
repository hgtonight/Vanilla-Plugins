$(function(){
   /* Do some javascripts on page load! */
   
   	function GetLatestPosts() {
		var url = gdn.url('/plugin/latestpostlist/module');
		
		$.ajax({
			url: url,
			global: false,
			type: "GET",
			data: null,
			dataType: "html",
			success: function(Data){
				$("#LatestPostList").replaceWith(Data);
				setTimeout(GetLatestPosts, gdn.definition('LatestPostListFrequency') * 1000);
			}
		});
	}
	
	if (gdn.definition('LatestPostListFrequency') > 0) {
		setTimeout(GetLatestPosts, gdn.definition('LatestPostListFrequency') * 1000);
	}
});