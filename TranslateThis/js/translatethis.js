$(document).ready(function () {
	var Settings = $.parseJSON(gdn.definition('TranslateThisSettings'));
	console.log(Settings);
	
	TranslateThis({
		GA : false, // Google Analytics tracking
		scope : 'content', // ID to confine translation
		wrapper : 'translate-this', // ID of the TranslateThis wrapper

		cookie : 'tt-lang', // Name of the cookie - set to 0 to disable

		panelText : 'Translate Into:', // Panel header text
		moreText : '42 More Languages »', // More link text
		busyText : 'Translating page...',
		cancelText : 'cancel',
		doneText : 'Translated by the', // Completion message text
		undoText : 'Undo »', // Text for untranslate link

		undoLength : 10000, // Time undo link stays visible (milliseconds)


		ddLangs : [ // Languages in the dropdown
		'cs',
		'pt-PT',
		'it',
		'ru',
		'ar',
		'zh-CN',
		'ja',
		'ko'
		],

		noBtn : false, //whether to disable the button styling
		btnImg : 'http://x.translateth.is/tt-btn1.png',
		btnWidth : 180,
		btnHeight : 18,

		noImg : false, // whether to disable flag imagery
		imgHeight : 12, // height of flag icons
		imgWidth : 8, // width of flag icons
		bgImg : 'http://x.translateth.is/tt-sprite.png',

		reparse : false // whether to reparse the DOM for each translation
	});
});