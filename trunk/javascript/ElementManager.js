jQuery.noConflict();

jQuery(function($) {
		$("div.ExtraFields label.left").click(function() {
			$(this).parent().find(".middleColumn").toggle("slow");
		});
                
                
                
                
                

	$.fn.ElementHistory = function() {
	//	this.each(function() {
			$.fn.ElementHistory.init(this);
	//	});
	};
		
		$.fn.ElementHistory.init = function(obj) {
			
			var $container = $(obj);
                        
                        var Element = $container.attr("id").match(/(.+)[-=_](.+)/);
                        
			
                        $("table tbody tr a", $container).click(function() {
                                var Action = $(this).attr("class");
                                var Version = $(this).parents("tr").attr("id").match(/(.+)[-=_](.+)/);
                                var url = "SlotManager/"+Action+"/"+Element[2]+"/"+Version[2];
                                if(Action == "revertElement") {
                                                if (confirm("are you sure?")) {
                                                $.post(url);
                                                }
                                } else {
                                                console.log(url);
                                                $("iframe#ElementPreview", $container).attr("src", url);
                                }
                                return false;
                        });
                        
                        
		}
		
		
		
		$("div.ElementHistoryBrowser").ElementHistory();


                
});

