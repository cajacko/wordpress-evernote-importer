<?php
/*
Plugin Name: Wordpress Evernote Importer
Description: Automatically import Evernote notes into Wordpress
Version:     1.0.0
Author:      Charlie Jackson
Author URI:  https://charliejackson.com
Text Domain: wordpress-evernote-importer
*/

define('WEI_APP_KEY_ID', 'wei_developer_token');
define('WEI_APP_SECRET_ID', 'wei_search_term');
define('WEI_TWITTER_QUERY', 'wei_twitter_query');
define('WEI_PLUGIN_ID', 'wordpress_evernote_importer');
define('WEI_PLUGIN_NAME', 'Wordpress Evernote Importer');
define('WEI_OPTIONS_SECTION', 'wei_main');
define('WEI_OPTIONS_SLUG', 'wei_options');
define('WEI_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WEI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WEI_POST_TYPE', 'note');
define('WEI_CRON', 'wei_get_notes');
define('WEI_ACTION_LATEST', 'wei-get-latest-notes');
define('WEI_ACTION_OLDER', 'wei-get-older-notes');
define('WEI_SCHEDULE', 'five_minutes');
define('WEI_SANDBOX', false);

require_once(WEI_PLUGIN_PATH .'setup.php');
require_once(WEI_PLUGIN_PATH .'options.php');
require_once(WEI_PLUGIN_PATH .'process.php');
