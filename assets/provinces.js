(function($) {
	$('#billing_country_field').on('change', function() {

		// Get country code
		var country_code = $(this).find('#billing_country').val();

		// Match country code with Vietnam
		if(country_code == 'VN') {
			$('.woocommerce-billing-fields__field-wrapper').addClass('active');
		} else {
			$('.woocommerce-billing-fields__field-wrapper').removeClass('active');
		}
	})
})(jQuery)