<?php
/*
Plugin Name: Ad Rotator Widget
*/
add_action('widgets_init', 'ad_rotator_register');
function ad_rotator_register() {
    register_widget('AdRotatorWidget');
}

foreach ([
	'app/Exceptions/JsonParseException.php',
	'app/Library/Json.php',
	'app/Library/Configuration.php'
] as $file) {
	require realpath(__DIR__."/../$file");
}

class AdRotatorWidget extends WP_Widget {
	private $pdo;
	private $config;
	private $base_path;
	private $campaigns = [];
	private $initialized = false;

	function __construct() {
		$this->config = new App\Library\Configuration();
		$widget_name = $this->config->get('prefs.wordpress_widget_name', 'ad_rotator_widget');
		parent::__construct($widget_name, 'Ad Rotator Widget');
	}

	function initialize() {
		if (!$this->initialized) {
			$this->campaigns = explode(',', $this->config->get('prefs.wordpress_campaigns', ''));
			if (!$this->campaigns) {
				$this->initialized = true;
				return;
			}

			$db_config = $this->config->get('database');
			$dsn = "mysql:host={$db_config["host"]};dbname={$db_config["database"]};charset={$db_config["charset"]}";

			$this->pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
				PDO::ATTR_EMULATE_PREPARES => false
			]);

			$this->base_path = $this->config->get('prefs.redirect_basepath', null);
			if ($this->base_path === null) {
				$env_base_path = $this->config->get('environment.basepath');
				$this->base_path = "$env_base_path/redirect";
			}
		}

		$this->initialized = true;
	}

	function getRandomAd() {
		$this->initialize();

		if (!$this->campaigns) {
			return false;
		}

		$bound_params = substr(str_repeat("?,", count($this->campaigns)), 0, -1);
		$query = "SELECT id FROM ads WHERE campaign_id IN
				 (SELECT id FROM campaigns WHERE campaign_name IN ($bound_params))";

		$stmt = $this->pdo->prepare($query);
		$stmt->execute($this->campaigns);
		$ad_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
		$random_ad_id = $ad_ids[array_rand($ad_ids)];

		$stmt = $this->pdo->prepare('SELECT slug, link_url, image_url FROM ads WHERE id = ?');
		$stmt->execute([$random_ad_id]);

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function widget($args, $instance) {
		echo $args['before_widget'];

		$result = $this->getRandomAd();
		if ($result) {
			foreach ($result as &$item) {
				$item = htmlspecialchars($item);
			}

			echo <<< EOM
			<a href="{$this->base_path}/${result["slug"]}" rel="nofollow" target="_blank">
			  <img src="${result["image_url"]}">
			</a>
EOM;
		}

		echo $args['after_widget'];
	}
}
