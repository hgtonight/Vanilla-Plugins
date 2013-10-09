/* Copyright 2013 Zachary Doll */
jQuery(document).ready(function($) {
  $('body.register input[type="submit"], #Register input[type="submit"]').attr('disabled', 'disabled');
  $('#NameUnavailable').after(function() {
    return $(this).clone().attr('id', 'MCUsernameInvalid').text('Enter a valid Minecraft account name.');
  });

  $('#Register input[name$=Name], body.register input[name$=Name]').blur(function() {
    var name = $(this).val();
    if (name != '') {
      $.ajax({
        url: gdn.url('/plugin/verifymcuser/' + encodeURIComponent(name)),
        dataType: 'text',
        success: function(data, textStatus, jqXHR) {
          if (data === 'false') {
            $('#MCUsernameInvalid').slideDown();
            $('body.register input[type="submit"], #Register input[type="submit"]').attr('disabled', 'disabled');
          }
          else {
            $('#MCUsernameInvalid').slideUp();
            $('body.register input[type="submit"], #Register input[type="submit"]').removeAttr('disabled');
          }
        }
      });
    }
  });
});
