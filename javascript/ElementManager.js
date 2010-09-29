jQuery.noConflict();

jQuery(function($) {
		$("div.ExtraFields label.left").click(function() {
			$(this).parent().find(".middleColumn").toggle("slow");
		});
                
                
                
                
                

	$.fn.ElementHistory = function() {
		this.each(function() {
			$.fn.ElementHistory.init(this);
		});
	};
		
		$.fn.ElementHistory.init = function(obj) {
			
			var $container = $(obj);
                        
                        var Element = $container.attr("id").match(/(.+)[-=_](.+)/);
                        
						$("table tbody tr").mouseover(function() {
								$(this).addClass("over");
						}).mouseout(function() {
								$(this).removeClass("over");
						}).click(function(){
								$(this).find("a.defaultaction").click();
						});
						
                        $("table tbody tr a", $container).click(function() {
                                var Action = $(this).attr("class");
                                //var Version = $(this).parents("tr").attr("id").match(/(.+)[-=_](.+)/);
                                //var url = "SlotManager/"+Action+"/"+Element[2]+"/"+Version[2];
								var url = $(this).attr("href");
                                if(Action == "revertElement") {
										if (confirm("Are you sure you want to revert to this version?")) {
												$.post(url, function() {
														window.top.GB_hide();
												});
										}
                                } else {
                                        $("tr",$container).removeClass("current");
										$(this).parents("tr").addClass("current");
                                        $("iframe#ElementPreview", $container).attr("src", url);
                                }
                                return false;
                        });
                        
                        
		}
		
		
		
		$("div.ElementHistoryBrowser").ElementHistory();


                
});

