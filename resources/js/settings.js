(function() {

var utils = require('../js_modules/utils.js');

var account_submit_button = $('#account-submit');
var old_password = $('#old-password');
var new_password = $('#new-password');
var new_password_again = $('#new-password-again');
var account_spinner = $('#account-spinner');
var prevent_account_submit_toggle = false;

function toggle_account_submit_button () {
	if (prevent_account_submit_toggle) {
		return;
	}

	account_submit_button.prop('disabled', (!old_password.val() || !new_password.val() || !new_password_again.val()));
};

$('#account-form input').keyup(toggle_account_submit_button);

account_submit_button.click(function(e) {
	e.preventDefault();

	if (account_submit_button.is(':disabled')) {
		return;
	}

	$.post(`${adrotator.basepath}/xhr/update_passwd`, {
		old_password: old_password.val(),
		new_password: new_password.val(),
		new_password_again: new_password_again.val()
	}).done(function() {
		old_password.val('');
		new_password.val('');
		new_password_again.val('');
		utils.showDialog('#account-form', 'Password updated successfully.', 'success', 'prepend');
	}).fail(function(data) {
		var response = utils.parseFailedResponse(data);
		utils.showDialog('#account-form', response.text, 'danger', 'prepend');
	}).always(function() {
		prevent_account_submit_toggle = false;
		account_spinner.prop('hidden', true);
		toggle_account_submit_button();
	});

	prevent_account_submit_toggle = true;
	account_submit_button.prop('disabled', true);
	account_spinner.prop('hidden', false);
});

var security_submit_button = $('#security-submit');
var security_spinner = $('#security-spinner');
var login_attempts = $('#login-attempts');
var login_in = $('#login-in');
var prevent_security_submit_toggle = false;

function toggle_security_submit_button() {
	if (prevent_security_submit_toggle) {
		return;
	}

	security_submit_button.prop('disabled', (!login_attempts.val() || !login_in.val()));
}

$('#security-form input').keyup(toggle_security_submit_button);

security_submit_button.click(function(e) {
	e.preventDefault();

	if (security_submit_button.is(':disabled')) {
		return;
	}

	$.post(`${adrotator.basepath}/xhr/update_sec`, {
		login_attempts: login_attempts.val(),
		login_in: login_in.val()
	}).done(function() {
		utils.showDialog('#security-form', 'Settings updated successfully.', 'success', 'prepend');
	}).fail(function(data) {
		var response = utils.parseFailedResponse(data);
		utils.showDialog('#security-form', response.text, 'danger', 'prepend');
		security_submit_button.prop('disabled', false);
	}).always(function() {
		prevent_security_submit_toggle = false;
		security_spinner.prop('hidden', true);
	});

	prevent_security_submit_toggle = true;
	security_submit_button.prop('disabled', true);
	security_spinner.prop('hidden', false);
});

var other_submit_button = $('#other-submit');
var other_spinner = $('#other-spinner');
var ignored_ua = $('#ignored_user_agents');
var wordpress_campaigns = $('#wordpress_campaigns');
var redirect_basepath = $('#redirect_basepath');
var wordpress_widget_name = $('#wordpress_widget_name');
var prevent_other_submit_toggle = false;

function toggle_other_submit_button() {
	if (prevent_other_submit_toggle) {
		return;
	}

	other_submit_button.prop('disabled', false);
	prevent_other_submit_toggle = true;
}

$('#other-form input').keyup(toggle_other_submit_button);

other_submit_button.click(function(e) {
	e.preventDefault();

	if (other_submit_button.is(':disabled')) {
		return;
	}

	prevent_other_submit_toggle = false;

	$.post(`${adrotator.basepath}/xhr/update_other`, {
		ignored_regex: ignored_ua.val(),
		wordpress_campaigns: wordpress_campaigns.val(),
		redirect_basepath: redirect_basepath.val(),
		wordpress_widget_name: wordpress_widget_name.val()
	}).done(function() {
		utils.showDialog('#other-form', 'Settings updated successfully.', 'success', 'prepend');
	}).fail(function(data) {
		var response = utils.parseFailedResponse(data);
		utils.showDialog('#other-form', response.text, 'danger', 'prepend');
		other_submit_button.prop('disabled', false);
	}).always(function() {
		prevent_other_submit_toggle = false;
		other_spinner.prop('hidden', true);
	});

	prevent_other_submit_toggle = true;
	other_submit_button.prop('disabled', true);
	other_spinner.prop('hidden', false);
});

var autocomplete_list = $('#campaigns-datalist');
var autofill_fields = $('.campaign-autofill');

autofill_fields.prop('autcomplete', 'off');

autofill_fields.keyup(function() {
	var val = $(this).val();
	var name_match = val.match(/[\w\s]+$/);

	if (name_match === null) {
		return;
	}

	var name = name_match[0];
	var the_rest = val.slice(0, -name.length)
	name = name.trim();

	$.get(`${adrotator.basepath}/xhr/suggest_campaign`, {
		name
	}).done(function(data) {
		autocomplete_list.empty();
		var len = data.length;
		for (var i = 0; i < len; i++) {
			autocomplete_list.append($('<option/>').text(the_rest + data[i]));
		}
	});
})

})();
