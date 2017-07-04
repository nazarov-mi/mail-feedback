
$.fn.feedbackForm = function () {
	
	this.on('submit', function (e) {
		e.preventDefault();
		submitForm($(this));

		return false;
	});

	resetForm(this);

	function resetForm($el) {
		$el.removeClass('fb-form_success fb-form_error');
		$el.find('.fb-input_error').removeClass('fb-input_error');
		
		$el.find('.fb-label, .fb-status')
			.removeClass('fb-status_success fb-status_error')
			.html('')
			.hide();
	}

	function startProcessing($el) {
		resetForm($el)

		$el.addClass('fb-form_loading');
		$el.find('.fb-disabled').prop('disabled', true);
	}

	function finishProcessing($el, text, status) {
		$el.removeClass('fb-form_loading');

		$el.addClass(status ? 'fb-form_success' : 'fb-form_error');
		$el.find('.fb-disabled').prop('disabled', false);
		
		if (text) {
			$el.find('.fb-status')
				.addClass(status ? 'fb-status_success' : 'fb-status_error')
				.html(text)
				.show();
		}
	}

	function setFormErrors($el, errors) {
		$.each(errors, function (name, error) {
			$el.find('.fb-input[name~=' + name + ']').addClass('fb-input_error');
			$el.find('.fb-label[rel~=' + name + ']').html(error).show();
		});
	}

	function submitForm($el) {
		var data = $el.serialize()
		  , name = $el.attr('name');

		startProcessing($el);

		$.ajax({
			type: 'POST',
			url: 'mail.php',
			cache: false,
			dataType: 'json',
			data: 'act=' + name + '&' + data,
			success: function (data) {
				finishProcessing($el, data.text, data.status);

				if (data.errors) {
					setFormErrors($el, data.errors);
				}
			},
			error: function () {
				finishProcessing($el, undefined, false);
			}
		});
	}
}

$(document).ready(function () {
	$('.fb-form').feedbackForm();
});