(function() {

var utils = require('../js_modules/utils.js');
var campaign_id = window.location.pathname.match(/\d+$/)[0];

var delete_campaign_spinner = $('#delete-modal-spinner');
var delete_campaign_button = $('#delete-button');

delete_campaign_button.click(function() {
	if (delete_campaign_button.is(':disabled')) {
		return;
	}

	delete_campaign_spinner.prop('hidden', false);
	delete_campaign_button.prop('disabled', true);

	$.get(`${adrotator.basepath}/xhr/del_campaign/${campaign_id}`).done(function() {
		window.location.replace(`${adrotator.basepath}/`);
	}).fail(function(data) {
		var response = utils.parseFailedResponse(data);
		utils.showDialog('#delete-modal .modal-body', response.text, 'danger');
	}).always(function() {
		delete_campaign_spinner.prop('hidden', true);
		delete_campaign_button.prop('disabled', false);
	});
});

})();
