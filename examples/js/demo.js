APE.Controller = new Class({
	
	Extends: APE.Client,
	
	initialize: function(){
		this.onRaw('switchClass', this.switchClass);
		this.addEvent('load',this.start);
	},
	
	start: function(core){
		this.core.start({'name': $time().toString()});
	},
	
	switchClass: function(raw){
        for(var i = 0; i < raw.data.length; i++)
          $(raw.data[i].id).set('class', raw.data[i].classes);
	}
	
});
