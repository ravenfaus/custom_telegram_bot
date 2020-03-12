<?php
class InlineKeyboard
{
	private $buttons;
	
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
			'inline_keyboard' => $this->buttons,
		);
	}
}
?>