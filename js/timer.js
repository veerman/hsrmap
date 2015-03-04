function appTimer(params){
	var self = this;

	self.add = function(){
    self.seconds++;
    self.$label.html(self.seconds > 9 ? self.seconds : '0' + self.seconds);
    self.time();
	}

	self.time = function(){
    self.timer = setTimeout(self.add, 1000);
	}

	self.start = function(){
		self.time();
	}

	self.stop = function(){
		clearTimeout(self.timer);
	}

	self.clear = function(){
		self.seconds = 0;
		self.$label.html('00');
	}
	
	self.reset = function(){
		self.stop();
		self.clear();
		self.start();
	}

	self.init = function(params){
		console.log('init ' + self.constructor.name);

		self.timer = null;
		self.$label = $(params.id);
		self.clear();
	}(params);
}