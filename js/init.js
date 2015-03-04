$(document).bind('mobileinit', function(){
	//$.mobile.defaultPageTransition = 'fade';
	$.mobile.defaultPageTransition = 'none';
	$.mobile.defaultDialogTransition = 'none';
	//$.mobile.ignoreContentEnabled = true;
	$.mobile.page.prototype.options.domCache = true;
	$.mobile.ajaxEnabled  = false;
	$.mobile.hashListeningEnabled = false;
	$.mobile.pushStateEnabled = false;
	$.mobile.changePage.defaults.changeHash = false;
	//$.mobile.page.prototype.options.theme  = 'd';
});