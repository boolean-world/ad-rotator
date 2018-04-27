(function() {

var utils = require('../js_modules/utils.js');

var login_button = $('#login-btn');
var username_field = $('input[name="username"]');
var password_field = $('input[name="password"]');
var login_spinner = $('#login-spinner');

function toggle_login_button() {
	login_button.prop('disabled', (username_field.val().trim() === '' || password_field.val() === ''));
}

username_field.keyup(toggle_login_button);
password_field.keyup(toggle_login_button);

login_button.click(function(ev) {
	ev.preventDefault();

	if (login_button.is(':disabled')) {
		return;
	}

	login_spinner.prop('hidden', false);
	login_button.prop('disabled', true);

	var username = username_field.val().trim();
	var password = password_field.val();
	var remember_me = $('input[name="remember_me"]').is(':checked');

	$.post(`${adrotator.basepath}/xhr/login`, {
		username, password, remember_me
	}).done(function() {
		window.location.replace(`${adrotator.basepath}/`);
	}).fail(function(data) {
		var response = utils.parseFailedResponse(data);

		login_spinner.prop('hidden', true);
		login_button.prop('disabled', false);
		toggle_login_button();
		utils.showDialog('#login-card.card-body', response.text, 'danger', 'prepend');
	});
});

})();
