(function($) {
	$('#billing_country_field').on('change', function() {

		// Get country name
		var country = $(this).find('#select2-billing_country-container').attr('title');

		// Match country name with Vietnam
		if(country == 'Vietnam') {
			$('.woocommerce-billing-fields__field-wrapper').addClass('active');
		} else {
			$('.woocommerce-billing-fields__field-wrapper').removeClass('active');
		}
	})
})(jQuery)