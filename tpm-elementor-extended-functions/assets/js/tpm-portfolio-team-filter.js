jQuery(document).ready(function() {
	jQuery(".port-team-flip-btn").click(function(){
		var parentdiv = jQuery(this).parent(".elementor-portfolio-team-member-item_img");

		jQuery(this).parent(".elementor-portfolio-team-member-item_img").find(".team-member-img").toggle();
		jQuery(this).parent(".elementor-portfolio-team-member-item_img").find(".team-free-img").toggle();
	});
});