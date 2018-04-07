(function($) {
	$('#billing_city_field').on('change', function() {
		// Find value customer choose
		var customer_city = $(this).find('#select2-billing_city-container').attr('title');

		if(customer_city != '') {
			$.ajax({
				url: woovietAjax.ajaxurl,
				type: 'POST',
				data: {
					action: 'get_customer_city_choose',
					customer_city: customer_city,
				},
			})
			.done(function(response) {
				$('body').trigger('update_checkout');
				console.log(response);
			});
			
		}
	})
})(jQuery)