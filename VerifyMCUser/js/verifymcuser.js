/* Copyright 2013 Zachary Doll */
jQuery(document).ready(function($) {
  $('#Register input[name$=Name], body.register input[name$=Name]').blur(function() {
    var name = $(this).val();
    if (name != '') {
      $.ajax({
        url: gdn.url('/plugin/verifymcuser/' + encodeURIComponent(name)),
        dataType: "text",
        success: function(data, textStatus, jqXHR) {
          if (data === 'false') {
            $('#NameUnavailable').text('Not a valid Minecraft account').show();
            $('body.register input[type="submit"], #Register input[type="submit"]').attr('disabled', 'disabled');
          }
          else {
            // Make sure the name is also not in use.
            var checkUrl = gdn.url('/dashboard/user/usernameavailable/'+encodeURIComponent(name));
            $.ajax({
              type: "GET",
              url: checkUrl,
              dataType: 'text',
              error: function(XMLHttpRequest, textStatus, errorThrown) {
                $.popup({}, XMLHttpRequest.responseText);
              },
              success: function(text) {
                if (text === 'FALSE') {
                  $('#NameUnavailable').text('Name Unavailable').show();
                  $('body.register input[type="submit"], #Register input[type="submit"]').attr('disabled', 'disabled');
                }
                else {
                  $('#NameUnavailable').hide();
                  $('body.register input[type="submit"], #Register input[type="submit"]').removeAttr('disabled');
                }
              }
            });
            
          }
        }
      });
    }
  });
});
