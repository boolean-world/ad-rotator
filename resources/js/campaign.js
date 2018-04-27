(function() {

var utils = require('../js_modules/utils.js');

var new_campaign_field = $('input[name="campaign_name"]');
var delete_campaign_spinner = $('#delete-campaign-spinner');
var save_campaign_spinner = $('#new-campaign-spinner');
var save_campaign_button = $('#save-new-campaign');
var new_campaign_modal = $('#new-campaign-modal');
var delete_button = $('#delete-campaign');
var delete_modal = $('#delete-modal');
var campaign_list = $('#campaigns');
var total_campaigns = adrotator.campaigns.length;

new_campaign_modal.on('shown.bs.modal', function () {
 	new_campaign_field.trigger('focus');
 	save_campaign_button.prop('disabled', true);
});

new_campaign_modal.on('hide.bs.modal', function () {
 	new_campaign_field.val('');
 	new_campaign_modal.find('.alert').remove();
});

function display_no_items_message() {
	campaign_list.append($('<span/>', {
		'id': 'no-items'
	}).text('There are no campaigns.'));
}

function toggle_save_button() {
	save_campaign_button.prop('disabled', (new_campaign_field.val().trim() === ''));
};

new_campaign_field.keyup(toggle_save_button);

function add_to_campaign_list(name, id, clicks, ads) {
	campaign_list.prepend(
	`<div class="card listing-cards" data-id="${id}">
	  <div class="card-body row">
	    <div class="col-12 col-md-7 main-info">
	      <h3>${utils.safeHTML(name)}</h3>
	      <p>${clicks} clicks, ${ads} ads</p>
	    </div>
	    <div class="col-12 col-md-5">
	      <div class="float-right">
	        <a class="btn btn-secondary" href="${adrotator.basepath}/edit/${id}">
	          <i class="fas fa-pencil-alt"></i>
	          Edit
	        </a>
	        <a class="btn btn-secondary" href="${adrotator.basepath}/stats/${id}">
	          <i class="fas fa-chart-pie"></i>
	          Stats
	        </a>
	        <button class="btn btn-primary campaign-delete-button">
	          <i class="fas fa-trash"></i>
	          Delete
	        </button>
	      </div>
	    </div>
	  </div>
	</div>`)
}

if (total_campaigns > 0) {
	for (var i = 0; i < adrotator.campaigns.length; i++) {
		add_to_campaign_list(
			adrotator.campaigns[i].campaign_name, 
			adrotator.campaigns[i].id, 
			adrotator.clicks[adrotator.campaigns[i].id] || 0,
			adrotator.ads[adrotator.campaigns[i].id] || 0
		);
	}
}
else {
	display_no_items_message();
}

save_campaign_button.click(function(ev) {
	ev.preventDefault();

	if (save_campaign_button.is(':disabled')) {
		return;
	}

	save_campaign_spinner.prop('hidden', false);
	save_campaign_button.prop('disabled', true);

	var name = new_campaign_field.val().trim();

	$.post(`${adrotator.basepath}/xhr/add_campaign`, {
		name
	}).done(function(response) {
		add_to_campaign_list(response.name, response.id, 0, 0);
		new_campaign_modal.modal('hide');
		total_campaigns++;
		if (total_campaigns === 1) {
			$('#no-items').remove();
		}
	}).fail(function(data) {
		var response = utils.parseFailedResponse(data);
		utils.showDialog('#new-campaign-modal .modal-body', response.text, 'danger');
	}).always(function() {
		save_campaign_spinner.prop('hidden', true);
		toggle_save_button();
	});
});

delete_button.click(function() {
	var id = delete_button.attr('data-id');

	delete_campaign_spinner.prop('hidden', false);
	delete_button.prop('disabled', true);

	$.get(`${adrotator.basepath}/xhr/del_campaign/${id}`).done(function() {
		$(`.listing-cards[data-id="${id}"]`).remove();
		delete_modal.modal('hide');
		total_campaigns--;
		if (total_campaigns === 0) {
			display_no_items_message();
		}
	}).fail(function(data) {
		var response = utils.parseFailedResponse(data);
		utils.showDialog('#delete-modal .modal-body', response.text, 'danger');
	}).always(function() {
		delete_campaign_spinner.prop('hidden', true);
		delete_button.prop('disabled', false);
	});
});

campaign_list.on('click', 'button.campaign-delete-button', function() {
	var trigger = $(this);
	var card = trigger.parents('.card');
	var id = card.attr('data-id');
	var name = card.find('h3').text();

	$('#delete-modal .modal-body').html(`Do you want to delete <b>${utils.safeHTML(name)}</b>?`);
	delete_button.attr('data-id', id);
	delete_modal.modal('show');
});

})();
