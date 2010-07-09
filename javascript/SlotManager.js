
jQuery(function($) {
		
		$.fn.SlotManager = function() {
			this.each(function() {
				$.fn.SlotManager.init(this);
			});
		};
		
		$.fn.SlotManager.init = function(obj) {
			
			var $container = $(obj);
			
			$(".handle img").mousedown(function() {
				$(this).attr("src",
					$(this).attr("src").replace("_drag.png", "_drop.png"));
			}).mouseup(function() {
				$(this).attr("src",
					$(this).attr("src").replace("_drop.png", "_drag.png"));
			}).click(function() {
				return false;
			});
			
			$("div.Slot", $container).livequery(function() {
				$(this).sortable({
					items: 'div.Element',
					handle: 'td.handle',
					placeholder: 'Element-holder',
					update: function(event, ui) {
						var Slot = $(this).attr("id").match(/(.+)[-=_](.+)/);
						$.post('SlotManager/sort/'+Slot[2]+"/",$(this).sortable("serialize"));
					},
					connectWith: $('.Slot')
				});
			});
			
			$("table.ElementContent a").click(function(){
				return false;
			});
			
			$("td.editable").editable({
				onSubmit:function(content){
					var Element = $(this).parents("div").attr("id").match(/(.+)[-=_](.+)/);
					$.post('SlotManager/editTitle/',{"ID": Element[2], "Name":content.current});
				}
			});
			
		}
		
		$("div.SlotManager").livequery(function() {
			$(this).SlotManager();
		});
});



GB_OpenerObj = {};
GB_RefreshLink = "";

SlotManager = Class.create();
SlotManager.prototype = {
	
	// These are defaults used if setPopupSize encounters errors
	defaultPopupWidth: 560,
	defaultPopupHeight: 390,
	
	initialize: function() {
		var rules = {};
		rules['#'+this.id+' table a.popuplink'] = {onclick: this.openPopup.bind(this)};
		
		// Assume that the delete link uses the deleteRecord method
		rules['#'+this.id+' table a.deletelink'] = {onclick: this.deleteRecord.bind(this)};
		
		rules['#'+this.id+' table a.publishlink'] = {onclick: this.publishElement.bind(this)};

		Behaviour.register('SlotManager_'+this.id,rules);
		
		this.setPopupSize();
		
	},
	
	setPopupSize: function() {
		
		try {
			this.popupHeight = parseInt(window.innerHeight-100);
			this.popupWidth = parseInt(window.innerWidth-100);
		} catch (ex) {
			this.popupHeight = this.defaultPopupHeight;
			this.popupWidth = this.defaultPopupWidth;
		}
	},
	
	
	/**
	 * @param href, table Optional dom object (use for external triggering without an event)
	 */
	openPopup: function(e, _popupLink, _table) {
		// If already in a popup, simply open the link instead
		// of opening a nested lightwindow
		if(window != top) return true;
		
		this.setPopupSize();
		
		var el,type;
		var popupLink = "";
		if(_popupLink) {
			popupLink = _popupLink;
			table = _table;
		} else {
			// if clicked item is an input-element, don't trigger popup
			var el = Event.element(e);
			var tr = Event.findElement(e, "tr");
			
			
			try {
				var table = Event.findElement(e,"table");
				if(Event.element(e).nodeName == "IMG") {
					link = Event.findElement(e,"a");
					popupLink = link.href+"?ajax=1";
				} else {
					el = Event.findElement(e,"tr");
					var link = $$("a",el)[0];
					popupLink = link.href;
				}
			} catch(err) {
				// no link found
				Event.stop(e);
				return false;
			}
			// no link found
			if(!link || popupLink.length == 0) {
				Event.stop(e);
				return false;
			}
		}
		
		var title = el.title;
		
		// reset internal greybox callbacks, they are not properly unregistered
		// and fire multiple times on each subsequent popup close action otherwise
		if(GB_ONLY_ONE) GB_ONLY_ONE.callback_fn = [];
		
		GB_show(
			title, 
			popupLink, 
			this.popupHeight, 
			this.popupWidth,
			this.refresh.bind(this)
		);
		
		if(e) {
			Event.stop(e);
		}
		return false;
	},
	/**
	 * Deletes the given dataobject record via an ajax request
	 * to complextablefield->Delete()
	 * @param {Object} e
	 */
	deleteRecord: function(e) {
		var img = Event.element(e);
		var link = Event.findElement(e,"a");
		var container = Event.findElement(e,"div");
		

		// TODO ajaxErrorHandler and loading-image are dependent on cms, but formfield is in sapphire
		var confirmed = confirm("Are you sure you want to "+link.title+"?");
		if(confirmed)
		{
			img.setAttribute("src",'cms/images/network-save.gif'); // TODO doesn't work
			new Ajax.Request(
				link.getAttribute("href"),
				{
					method: 'post', 
					postBody: 'forceajax=1' + ($('SecurityID') ? '&SecurityID=' + $('SecurityID').value : ''),
					onComplete: function(){
						Effect.Fade(
							container,
							{
								afterFinish: function(obj) {
									// remove row from DOM
									obj.element.parentNode.removeChild(obj.element);
									// recalculate summary if needed (assumes that TableListField.js is present)
									// TODO Proper inheritance
									if(this._summarise) this._summarise();
									// custom callback
									if(this.callback_deleteRecord) this.callback_deleteRecord(e);
								}.bind(this)
							}
						);
					}.bind(this),
					onFailure: this.ajaxErrorHandler
				}
			);
		}
		Event.stop(e);
	},
	
	publishElement: function(e) {
		var img = Event.element(e);
		var link = Event.findElement(e,"a");
		
		var confirmed = confirm("Are you sure you want to "+link.title+"?");
		if(confirmed)
		{
			var origsrc = img.getAttribute("src");
			
			img.setAttribute("src",'cms/images/network-save.gif');
			
			// TODO better interaction / hide button when no newer version is there etc
			new Ajax.Request(
				link.getAttribute("href"),
				{
					method: 'post', 
					postBody: 'forceajax=1' + ($('SecurityID') ? '&SecurityID=' + $('SecurityID').value : ''),
					onComplete: function(){
						//img.setAttribute("src",origsrc);
						img.setAttribute("style","display:none;");
					}.bind(this)
				}
			);
		}
		Event.stop(e);
	}
	
}

SlotManager.applyTo('div.SlotManager');
