jQuery(document).ready(function(){
	jQuery( ".nm-product-share" ).replaceWith( "<a class='app-share'><i class='nm-font nm-font-thumb-up'></i> شارك هذا المنتج</a>" );
	jQuery(".app-share").click(function(){
	    jQuery(".app-share").attr("href", "gonative://share/sharePage");
	});
});

// jQuery(document).ajaxComplete(function(){ 
// 	jQuery('a[target="_blank"]').removeAttr('target');
// });