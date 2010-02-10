
(function($) {
	$(document).ready(function() {
		
		$.fn.SlotManager = function() {
			this.each(function() {
				$.fn.SlotManager.init(this);
			});
		};
		
		$.fn.SlotManager.init = function(obj) {
			var $container = $(obj);
			
			$.fn.SlotManager.setPopupSize($container)
			
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
			
			$("table.SlotContent a").click(function(){
				return false;
			});
		}
		
		$.fn.SlotManager.setPopupSize = function(container) {
			var container_id = '#'+container.attr('id');
			$(container_id+"_PopupHeight").attr("value", $(window).height()-100);
			$(container_id+"_PopupWidth").attr("value", $(window).width()-150);
		}
		
		$("td.editable").editable({
			onSubmit:function(content){
				var Element = $(this).parents("div").attr("id").match(/(.+)[-=_](.+)/);
				$.post('SlotManager/editTitle/',{"ID": Element[2], "Name":content.current});
			}
		});
		
		$("div.SlotManager").livequery(function() {
			$(this).SlotManager();
		});
	
		
	
	});
})(jQuery);



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

		Behaviour.register('SlotManager_'+this.id,rules);
		
		this.setPopupSize();
		
	},
	
	setPopupSize: function() {
		try {
			this.popupHeight = parseInt($(this.id + '_PopupHeight').value);
			this.popupWidth = parseInt($(this.id + '_PopupWidth').value);
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
			var input = Event.findElement(e,"input");
			var tr = Event.findElement(e, "tr");
			
			// stop on non-found lines
			if(tr && Element.hasClassName(tr, 'notfound')) {
				Event.stop(e);
				return false;
			}
			
			// normal behaviour for input elements
			if(el.nodeName == "INPUT" || input.length > 0) {
				return true;
			}
			
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
		
		if(this.GB_Caption) {
			var title = this.GB_Caption;
		} else {
			// Getting the title from the URL is pretty ugly, but it works for now
			type = popupLink.match(/[0-9]+\/([^\/?&]*)([?&]|$)/);
			var title = (type && type[1]) ? type[1].ucfirst() : "";
		}
		
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
	}
}

SlotManager.applyTo('div.SlotManager');
