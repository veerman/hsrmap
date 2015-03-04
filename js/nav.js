function appNav(params){
	var self = this;

	self.openMenu = function(){
		self.$menu_panel.panel('open');
	}

	self.closeMenu = function(){
		self.$menu_panel.panel('close');
	}

	self.toggleMenu = function(){
		self.$menu_panel.panel('toggle');
	}

	self.closePopup = function(){
		$('.ui-popup').popup('close');
	}

	self.init = function(params){
		console.log('init ' + self.constructor.name);

		self.$menu_panel = $('.menu_nav');
		self.$about_popup = $('#about');

		$(document).on('click', '.menu_toggle', function(event, ui){
			self.toggleMenu();
		});

		$(document).on('click', '.menu_about', function(event, ui){
			self.closeMenu();
			self.$about_popup.popup('open', {'y': $('.ui-header').offset().top} ); // {'positionTo': 'window'}
		});

		$(document).on('click', '.close_popup', function(event, ui){
			self.closePopup();
		});
	}(params);
}
