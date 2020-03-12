<?php
class InlineButton
{
	public $text;
	public $callback_data;
	
	public function __construct($text, $callback_data = 'null')
	{
		$this->text = $text;
		$this->callback_data = $callback_data;
		return ['text' => $this->text, 'callback_data' => $this->callback_data];
	}
}
?>