/* Copyright 2013 Zachary Doll */
jQuery(window).bind('ImagesResized', function() {
  // wait until all the images have been resized before working with them
  $('.ImageResized').each(function() {
    $(this).prev().click(function(event) {
      event.preventDefault();
      // create the overlay objects if needed
      if ($('#ResizeOverlay').length === 0) {
        $('<div id="ResizeOverlay">&nbsp;</div>').appendTo('body').hide();
      }
      if ($('#ResizedImage').length === 0) {
        $('<img id="ResizedImage" />').appendTo('body').hide();
      }

      var InlineImage = $(this).children(':first');
      // get the current width and height and position
      var smallWidth = $(InlineImage).width();
      var smallHeight = $(InlineImage).height();
      var origPos = $(InlineImage).offset();

      // get the full width and height by using an offscreen copy
      $('#ResizedImage').attr('src', $(InlineImage).attr('src'));
      var bigWidth = $('#ResizedImage').width();
      var bigHeight = $('#ResizedImage').height();

      // set animating image to the clicked image's original size and position
      $('#ResizedImage').css({'position': 'absolute', 'top': origPos.top, 'left': origPos.left, 'width': smallWidth, 'height': smallHeight}).show();

      // Bring in that sweet overlay and animate the image to the full size
      $('#ResizeOverlay').fadeIn(300, function() {
        $('#ResizedImage').show().animate({'width': bigWidth, 'height': bigHeight, 'top': origPos.top - ((bigHeight - smallHeight) / 2), 'left': origPos.left - ((bigWidth - smallWidth) / 2)}, 700);
      });

      // add a click handler that animates in reverse
      $('#ResizeOverlay, #ResizedImage').click(function() {
        $('#ResizedImage').animate({'top': origPos.top, 'left': origPos.left, 'width': smallWidth, 'height': smallHeight}, 300, function() {
          $('#ResizeOverlay').fadeOut(200, function() {
            $('#ResizedImage').hide().css({'width': '', 'height': ''});
          });
          // drop that handler like it is hot
          $('#ResizeOverlay, #ResizedImage').unbind('click');
        });
      });
    });
  });
});