<?php
require_once('curl.php');
class api
{
	private $url = 'https://api.telegram.org/bot';
	private $furl = 'https://api.telegram.org/file/bot';
	private $token;
	private $db;

	public function __construct($token, $db = Null)
	{
		$this->token = $token;
		$this->db = $db;
	}

	private function request($method, $params = array())
	{
		$c = new curl();
		$r = $c->request($this->url.$this->token."/".$method, 'POST', $params);

		$j = json_decode($r, true);
		if($j)
			return $j;
		else
			return $r;
	}

	public function getFile($file_id)
	{
		$params = array
		(
			'file_id' => $file_id
		);
		return $this->request('getFile', $params);
	}

	public function sendMessage($id, $text, $reply_markup=null)
	{
		$params = array
		(
			'chat_id' => $id,
			'text' => $text,
			'reply_markup' => ($reply_markup == null ? null : json_encode($reply_markup))
		);
		return $this->request('sendMessage', $params);
	}

	public function editMessageText($id, $mid, $text='', $reply_markup=null)
	{
		$params = array
		(
			'text' => $text,
			'chat_id' => $id,
			'message_id' => $mid,
			'reply_markup' => ($reply_markup == null ? null : json_encode($reply_markup))
		);
		return $this->request('editMessageText', $params);
	}

	public function answerCallbackQuery($id, $text=null, $show_alert=null)
	{
		$params = array
		(
			'callback_query_id' => $id,
			'text' => $text,
			'show_alert' => $show_alert
		);
		return $this->request('answerCallbackQuery', $params);
	}

	//https://core.telegram.org/bots/api#sendphoto
	public function sendPhoto($id, $caption='', $photo, $reply_markup=null, $disable_notification=false, $parse_mode='HTML')
	{
		$params = array
		(
			'chat_id' => $id,
			'caption' => $caption,
			'photo' => $photo,
			'disable_notification' => $disable_notification,
			'parse_mode' => $parse_mode,
			'reply_markup' => ($reply_markup == null ? null : json_encode($reply_markup))
		);
		return $this->request('sendPhoto', $params);
	}

	//https://core.telegram.org/bots/api#sendchataction
	public function sendChatAction($id, $action)
	{
		$params = array
		(
			'chat_id' => $id,
			'action' => $action
		);
		return $this->request('sendChatAction', $params);
	}

	public function setWebhook($url)
	{
		$params = array
		(
			'url' => $url
		);
		return $this->request('setWebhook', $params);
	}

	public function downloadFile($file_path, $file_target) {
    $rh = fopen($this->furl.$this->token.'/'.$file_path, 'rb');
    $wh = fopen($file_target, 'w+b');
    if (!$rh || !$wh) {
        return false;
    }

    while (!feof($rh)) {
        if (fwrite($wh, fread($rh, 4096)) === FALSE) {
            return false;
        }
        echo ' ';
        flush();
    }

    fclose($rh);
    fclose($wh);

    return true;
}
}
?>