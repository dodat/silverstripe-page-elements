jQuery.noConflict();

jQuery(function($) {
		$("div.ExtraFields label.left").click(function() {
			$(this).parent().find(".middleColumn").toggle("slow");
		});
});
