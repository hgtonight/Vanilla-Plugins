$(document).ready(function () {
	// Find images in front page blog posts and insert a caption block
	$('.Blog .Discussion .Message img').each(function () {
		if( $(this).attr('alt') != 'image')
		{
			$(this).after('<div class="Caption">' + $(this).attr('alt') + '</div>');
		}
	});
	
	// adjust the position dynamically to account for different sized captions
	$('.Caption').each(function () {
		$(this).css( {'width' : $(this).prev('img').width() + 'px'} );
		if( $(this).height() < $(this).prev('img').height() )
		{
			$(this).css( {
				'bottom' : ( $(this).height() + 25 ) + 'px',
				'margin-bottom' : '-' + ( $(this).height() + 25 ) + 'px',
			});
		}
		else
		{
			$(this).remove();
		}
	});
});