<?php
require_once('lib/api.php');
require_once('src/database.php');

define('PATH', realpath('./'));
$config = json_decode(file_get_contents(PATH."/config.json"));
// telegram instance
$telegram = new api($config->telegram->token);
$domain = $config->server->domain;
$root = $config->server->root;
$index_page = $config->server->index_page;

$url = $domain.$root.$index_page;
var_dump($telegram->setWebhook($url));
// setup db and set admin
$admin = $config->telegram->admin_id;
$db = new database('src/users.db');
if (!$db->user_exists($admin))
	$db->add_admin($admin);

function getRequest()
{
	$postdata = file_get_contents("php://input");
	$json = json_decode($postdata, true);
	if($json)
		return $json;
	return $postdata;
}
