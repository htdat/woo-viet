(function($) {
	// Get country code for display
	var country_code = woo_viet_countries.country_code;

	// Set country for field billing_country, shipping_country
	$('#billing_country, #shipping_country').val(country_code);

	// Disable select box
	$('#billing_country, #shipping_country').select2({
		disabled: true,
	});

})(jQuery)