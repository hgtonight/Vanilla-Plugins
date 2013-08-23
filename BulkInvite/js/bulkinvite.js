/* Copyright 2013 Zachary Doll */
$(function() {
  var PlaceHolderText = gdn.definition('BI_Placeholder');
  var EmailInput = $('#BI_Recipients');
  if ($(EmailInput).val() === '') {
    $(EmailInput).val(PlaceHolderText);
  }

  $(EmailInput).focus(function() {
    if ($(this).val() === PlaceHolderText) {
      $(this).val('');
    }
  });
  $(EmailInput).blur(function() {
    if ($(this).val() === '') {
      $(this).val(PlaceHolderText);
    }
  });
});