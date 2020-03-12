<?php
define('PATH', realpath('./'));
// Require all files from lib
$files = glob('lib/*.php');

foreach ($files as $file) {
    require_once($file);   
}
// Require all files from src
$files = glob('src/*.php');

foreach ($files as $file) {
    require_once($file);   
}

$data = getRequest();
$config = json_decode(file_get_contents(PATH."/config.json"));
$url = $config->server->domain . $config->server->root;
$api = new api($config->telegram->token);
$db = new database('src/users.db');
parse_message($data['message']);


function parse_message($msg)
{
	global $api, $db;
	$user = new User($msg['from']);
	if (!$db->user_exists($user->id))
		$db->add_user($user);
	$j = file_get_contents('menu.json');
	$data = json_decode($j,true);
	$buttons = $data['buttons'];
	if ($db->user_admin($user->id)) {
		if (parse_admin_message($msg, $user))
			return;
	}
	$i = 0;
	if ($msg['text'] == '/start' or $msg['text'] == 'Menu') {
		send_text($buttons[$i], $api, $user->id);
		return;
	}
	while ($i < count($buttons)) {
		if ($msg['text'] == $buttons[$i]['name']) {
			send_text($buttons[$i], $api, $user->id);
			break;
		} else {
			if (isset($buttons[$i]['btn']))
				search_text($buttons[$i]['btn'], $msg['text'], $api, $user->id);
		}
		$i++;
	}
}

function parse_admin_message($msg, $user) {
	global $api, $db;
	if (isset($msg['text'])) {
		switch ($msg['text']) {
			case '/umenu':
				$db->set_last_msg($user->id, 'upload menu');
				$api->sendMessage($user->id, 'Send me menu in json format');
				return true;
			case '/uimage':
				$db->set_last_msg($user->id, 'upload image');
				$api->sendMessage($user->id, 'Send me image with caption. Image will be save with name as your caption');
				return true;
			default:
				break;
		}
	} elseif (isset($msg['document']) or isset($msg['photo'])) {
		$last_msg = explode(' ', $db->get_last_msg($user->id));
		switch ($last_msg[0]) {
			case 'upload':
				switch ($last_msg[1]) {
					case 'menu':
						$fid = $msg['document']['file_id'];
						$r = $api->getFile($fid)['result']['file_path'];
						$api->downloadFile($r, 'menu.json');
						$db->set_last_msg($user->id, '');
						$api->sendMessage($user->id, 'Menu was updated. Use /start command for new menu');
						return true;
					case 'image':
						$fid = $msg['photo'][0]['file_id'];
						$fname = $msg['caption'];
						$r = $api->getFile($fid)['result']['file_path'];
						$api->downloadFile($r, 'images/'.$fname);
						$db->set_last_msg($user->id, '');
						$api->sendMessage($user->id, 'Image was uploaded');
						return true;
				}
				break;
			default:
				break;
		}
	}
	return false;
}

function send_text($arr, $api, $id) {
	$text = $arr['text'];
	$k = new keyboard();
	if (isset($arr['btn'])) {
		for ($i=0; $i < count($arr['btn']); $i++) { 
			$k->addRow([$arr['btn'][$i]['name']]);
		}
	}
	$k->addRow(['Menu']);
	if (isset($arr['image'])) {
		global $url;
		// Using time() for telegram non-cache function. More info: https://stackoverflow.com/questions/42719409/telegram-bot-image-from-url-undesired-cache
		$image = $url . 'images/'.$arr['image'] . '?a='.time();
		error_log(var_dump($api->sendPhoto($id, $text, $image, $k->replyMarkup())));
	} else
		$api->sendMessage($id, $text, $k->replyMarkup());
}

function search_text($arr, $name, $api, $id) {
	$i = 0;
	while ($i <= count($arr) - 1) {
		if ($arr[$i]['name'] == $name) {
			send_text($arr[$i], $api, $id);
			break;
		} else {
			if (isset($arr[$i]['btn']))
				search_text($arr[$i]['btn'], $name, $api, $id);
		}
		$i++;
	}
}

function getRequest()
{
	$postdata = file_get_contents("php://input");
	$json = json_decode($postdata, true);
	if($json)
		return $json;
	return $postdata;
}
?>