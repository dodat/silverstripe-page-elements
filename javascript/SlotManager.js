
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
		
		$("div.SlotManager").livequery(function() {
			$(this).SlotManager();
		});
	
		
	
	});
})(jQuery);
