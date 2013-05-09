/* Copyright 2013 Zachary Doll */
$(function(){
	$('#BulkEditDropDown').change( function(event) {
		event.preventDefault();
		if($('.md:checked').length != 0 && $(this).val() !=0) {
			$('#Form_Form').attr({'action': gdn.url('plugin/bulkedit'), 'method': 'post'}).submit();
		}
		else {
			// gotta check some boxes son!
			// change to modal later
			alert('Please select one or more users...');
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