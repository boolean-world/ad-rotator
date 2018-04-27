(function() {

var utils = require('../js_modules/utils.js');
var campaign_id = window.location.pathname.match(/\d+$/)[0];

var delete_campaign_spinner = $('#delete-campaign-spinner');
var delete_campaign_button = $('#delete-campaign');
var campaign_items_list = $('#campaign-items');
var total_campaign_items = Object.keys(adrotator.campaign_items).length;

function display_no_items_message() {
	campaign_items_list.append($('<span/>', {
		'id': 'no-items'
	}).text('There are no ads.'));
}

function add_to_campaign_items_list(id, link_url, image_url, slug) {
	campaign_items_list.prepend(
	`<div class="card listing-cards" data-id="${id}">
	  <div class="card-body row">
	    <div class="col-12 col-md-8 main-info" data-id="${id}">
	      <p>
	      	<b>Link URL</b>:
	      	<a href="${utils.safeHTML(link_url)}">${utils.safeHTML(link_url)}</a>
	      </p>
	      <p>
	      	<b>Image URL</b>:
	      	<a href="${utils.safeHTML(image_url)}">${utils.safeHTML(image_url)}</a>
	      </p>
	      <p>
	      	<b>Slug</b>:
	      	<a href="${adrotator.basepath}/redirect/${utils.safeHTML(slug)}">${utils.safeHTML(slug)}</a>
	      </p>
	    </div>
	    <div class="col-12 col-md-4">
	      <div class="float-right">
	        <button class="btn btn-secondary item-edit-button">
	          <i class="fas fa-pencil-alt"></i>
	          Edit
	        </button>
	        <button class="btn btn-primary item-delete-button">
	          <i class="fas fa-trash"></i>
	          Delete
	        </button>
	      </div>
	    </div>
	  </div>
	</div>`)
}

if (total_campaign_items > 0) {
	for (var i = 0; i < adrotator.campaign_items.length; i++) {
		add_to_campaign_items_list(
			adrotator.campaign_items[i].id, 
			adrotator.campaign_items[i].link_url,
			adrotator.campaign_items[i].image_url,
			adrotator.campaign_items[i].slug
		);
	}
}
else {
	display_no_items_message();
}

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

var editor = $('#item-edit-modal');
var item_image = $('#item-image');
var save_item_spinner = $('#save-item-spinner');
var editor_inputs = $('#item-edit-modal input');
var editor_header = editor.find('.modal-title');
var save_item_button = $('#save-item-button');
var item_image_url = $('input[name="item-image-url"]');
var item_link_url = $('input[name="item-link-url"]');
var item_slug = $('input[name="item-slug"]');
var create_new_item = $('#create-new-item');
var prevent_submit_toggle = false;

create_new_item.click(function() {
	editor_header.text('Create a new ad');
	save_item_button.attr('data-action', 'new');
});

editor.on('shown.bs.modal', function() {
	save_item_button.prop('disabled', true);
});

editor.on('hide.bs.modal', function() {
	editor_inputs.val('');
	item_image.attr('src', '').prop('hidden', true);
});

function toggle_save_item_button() {
	if (prevent_submit_toggle) {
		return;
	}

	save_item_button.prop('disabled', !(
		item_image_url.val().trim() &&
		item_link_url.val().trim() &&
		item_slug.val().trim()	
	));
}

editor_inputs.keyup(toggle_save_item_button);

function toggle_image_view() {
	var val = item_image_url.val().trim()

	if (!val || !/^https?:\/\/[a-z0-9.-]+\//.test(val)) {
		item_image.prop('hidden', true);
		return;
	}

	if (item_image.attr('src') === val) {
		return;
	}

	if (/^https:\/\/[a-z0-9.-]+\//.test(window.location.href) &&
		val.startsWith('http://')) {
		item_image.attr('src', `${adrotator.basepath}/imageproxy/?url=${window.encodeURIComponent(val)}`);
	}
	else {
		item_image.attr('src', val);
	}

	item_image.prop('hidden', false);
}

item_image_url.blur(toggle_image_view);

save_item_button.click(function(ev) {
	ev.preventDefault();

	if (save_item_button.is(':disabled')) {
		return;
	}

	var image_url = item_image_url.val().trim();
	var link_url = item_link_url.val().trim();
	var slug = item_slug.val().trim();
	var save_attr = save_item_button.attr('data-action');

	if (save_attr === 'new') {
		$.post(`${adrotator.basepath}/xhr/add_item`, {
			campaign_id, image_url, link_url, slug
		}).done(function(response) {
			editor.modal('hide');
			add_to_campaign_items_list(response.id, link_url, image_url, slug);
			total_campaign_items++;
			if (total_campaign_items === 1) {
				$('#no-items').remove();
			}
		}).fail(function(data) {
			var response = utils.parseFailedResponse(data);
			utils.showDialog('#item-edit-modal .modal-body', response.text, 'danger');
		}).always(function() {
			prevent_submit_toggle = false;
			toggle_save_item_button();
			save_item_spinner.prop('hidden', true);
		});

		prevent_submit_toggle = true;
		save_item_button.prop('disabled', true);
		save_item_spinner.prop('hidden', false);
	}
	else if (save_attr === 'edit') {
		var id = save_item_button.attr('data-id');

		$.post(`${adrotator.basepath}/xhr/edit_item`, {
			id, image_url, link_url, slug
		}).done(function() {
			var data = [link_url, image_url, slug];
			var index = 0;

			$(`.card[data-id="${id}"] .main-info a`).each(function() {
				$(this).attr('href', data[index]).text(data[index]);
				index++;
			});

			editor.modal('hide');
		}).fail(function(data) {
			var response = utils.parseFailedResponse(data);
			utils.showDialog('#item-edit-modal .modal-body', response.text, 'danger');
		}).always(function() {
			prevent_submit_toggle = false;
			toggle_save_item_button();
			save_item_spinner.prop('hidden', true);
		});

		prevent_submit_toggle = true;
		save_item_button.prop('disabled', true);
		save_item_spinner.prop('hidden', false);
	}
});

var delete_item_button = $('#delete-item');
var delete_item_modal = $('#delete-item-modal');
var delete_item_spinner = $('#delete-item-spinner');

delete_item_button.click(function() {
	var id = delete_item_button.attr('data-id');

	$.get(`${adrotator.basepath}/xhr/del_item/${id}`).done(function() {
		$(`.listing-cards[data-id="${id}"]`).remove();
		delete_item_modal.modal('hide');
		total_campaign_items--;
		if (total_campaign_items === 0) {
			display_no_items_message();
		}
	}).fail(function(data) {
		var response = utils.parseFailedResponse(data);
		utils.showDialog('#delete-item-modal .modal-body', response.text, 'danger');
	}).always(function() {
		delete_item_spinner.prop('hidden', true);
		delete_item_button.prop('disabled', false);
	});

	delete_item_spinner.prop('hidden', false);
	delete_item_button.prop('disabled', true);
});

campaign_items_list.on('click', 'button.item-delete-button', function() {
	var trigger = $(this);
	var card = trigger.parents('.card');
	var id = card.attr('data-id');

	delete_item_button.attr('data-id', id);
	delete_item_modal.modal('show');
});

campaign_items_list.on('click', 'button.item-edit-button', function() {
	var trigger = $(this);
	var card = trigger.parents('.card');
	var id = card.attr('data-id');
	var data_items = card.find('a');
	var data = [];

	data_items.each(function() {
		data.push($(this).attr('href'));
	});

	item_link_url.val(data[0]);
	item_image_url.val(data[1]);
	item_slug.val(data[2].match(/[^/]+$/)[0]);
	save_item_button.attr('data-id', id);
	save_item_button.attr('data-action', 'edit');
	editor_header.text('Edit this ad');
	toggle_image_view();
	editor.modal('show');
});

})();