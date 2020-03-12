<?php
class Keyboard
{
	private $buttons = [];
	public $resize_keyboard = false;
	public $one_time = false;
	public $selective = true;
	
	public function __construct($buttons=[])
	{
		$this->buttons = $buttons;
	}

	public function addRow($row)
	{
		array_push($this->buttons, $row);
	}

	public function replyMarkup() 
	{
		return array (
			'keyboard' => $this->buttons,
			'resize_keyboard' => $this->resize_keyboard,
			'one_time_keyboard' => $this->one_time,
			'selective' => $this->selective
		);
	}
}
?>