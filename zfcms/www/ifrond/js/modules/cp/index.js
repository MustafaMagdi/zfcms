jQuery(function($) {
	
	$("#tabsModule").tabs({
		ajaxOptions: {
			error: function(xhr, status, index, anchor) {
				$(anchor.hash).html("Невозможно загрузить этот модуль.");
			}, 
			cookie: {
				expires: 7
			},
			spinner: 'идет загрузка...'
		}
	});

	/* autoheight */
	$(window).resize(function(){setAutoHeight()});	
	setAutoHeight();

});