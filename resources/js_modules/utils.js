exports.showDialog = function(elem_str, text, type, dir='append') {
	elem_str = elem_str.trim();
	var elem = $(`${elem_str}>.alert`);
	if (elem.length === 0) {
		elem = $('<div/>').text(text).append($('<button/>', {
			'type': 'button',
			'class': 'close',
			'data-dismiss': 'alert'
		}).html('&times;'));

		if (dir === 'prepend') {
			$(elem_str).prepend(elem);
		}
		else if (dir === 'append') {
			$(elem_str).append(elem);
		}
		else {
			throw 'Invalid direction';
		}
	}
	else {
		elem.contents()[0].data = text;
	}

	elem.attr('class', `alert alert-${type}`)
};

exports.parseFailedResponse = function(data) {
	var response = {};

	try {
		response = JSON.parse(data.responseText);
	}
	catch (e) {
		// do nothing.
	}

	if (response['code'] === 'not_logged_in') {
		window.location.replace(`${adrotator.basepath}/login`);
		return;
	}
	else if (response['code'] === 'already_logged_in') {
		window.location.replace(`${adrotator.basepath}/`);
		return;
	}

	if (response['text'] === undefined) {
		if (data.status >= 500) {
			response['text'] = 'An error occured while processing this request. Please try later.'
		}
		else {
			response['text'] = 'Submission failed. Please check your internet connection.'
		}
	}

	return response;
};

exports.safeHTML = function(a) {
	return String(a).replace(/&/g, '&amp;')
					.replace(/</g, '&lt;')
					.replace(/>/g, '&gt;')
					.replace(/"/g, '&quot;')
					.replace(/'/g, '&apos;');
};
