/* Copyright 2013 Zachary Doll */
jQuery(document).ready(function($) {
  // Get the wanted information from some definitions and return false if they don't exist
  var Title = gdn.definition('TG_DiscussionTitle', false);
  var Tag = gdn.definition('TG_DiscussionTag', false);
  
  // If the title or tag is missing we don't want to pre-populate
  if(!Title || !Tag) {
    return;
  }
  else {
    // We want to loop through all the labe
	$('input#Form_Name').val(Title);
	$('.token-input-input-token input').removeval(Tag);
  }
});
