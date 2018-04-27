<?php

return function(FastRoute\RouteCollector $r) {
	$r->get('/', 'IndexController');
	$r->get('/login', 'LoginPageController');
	$r->get('/logout', 'LogoutController');
	$r->get('/settings', 'SettingsController');
	$r->get('/stats/{id:[0-9]+}', 'StatsController');
	$r->get('/edit/{id:[0-9]+}', 'EditCampaignController');
	$r->get('/redirect/{slug}', 'RedirectController');
	$r->get('/imageproxy', 'ImageProxyController');
	$r->post('/xhr/login', 'LoginHandlerController');
	$r->post('/xhr/add_campaign', 'AddCampaignController');
	$r->post('/xhr/add_item', 'AddAdvertController');
	$r->get('/xhr/del_item/{id:[0-9]+}', 'DeleteAdvertController');
	$r->post('/xhr/edit_item', 'EditAdvertController');
	$r->get('/xhr/del_campaign/{id:[0-9]+}', 'DeleteCampaignController');
	$r->post('/xhr/update_passwd', 'PasswordChangeController');
	$r->post('/xhr/update_sec', 'SecuritySettingsChangeController');
	$r->post('/xhr/update_other', 'OtherSettingsChangeController');
	$r->get('/xhr/suggest_campaign', 'SuggestCampaignController');
};
