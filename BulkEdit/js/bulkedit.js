/* Copyright 2013 Zachary Doll */
$(function(){
	var DropDownString = gdn.definition('BulkEditTools');
	// Add dropdown in 2.0.18.8+
	$('.FilterMenu').append(DropDownString);
	
	if($('#BulkEditDropDown').length == 0) {
		// Add dropdown in 2.1b1+ if needed
		$('.Wrap .Popup.SmallButton').after(DropDownString);
	}
	
	$('#BulkEditDropDown').change( function(event) {
		event.preventDefault();
		if($('.BulkSelect:checked').length != 0 && $(this).val() !=0) {
			$('form').attr({'action': gdn.url('plugin/bulkedit/' + $(this).val()), 'method': 'post'}).submit();
		}
		else {
			// gotta check some boxes son!
			// change to modal later
			alert('You must select at least one user...');
			$(this).val(0);
		}
	});
	$('#BulkEditAction').click( function(event) {
		event.preventDefault();
		$(this).closest('form').find(':checkbox').prop('checked', function(i, val) {
			return !val;
		});
	});
});