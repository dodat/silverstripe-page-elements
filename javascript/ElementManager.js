var j = jQuery.noConflict();

(function() {
	j(document).ready(function() {
		j("div.ExtraFields label.left").click(function() {
			j(this).parent().find(".middleColumn").toggle("slow");
		});
	});
})(jQuery);
