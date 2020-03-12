<?php
class RemoveKeyboard
{
	public function __construct($selective = true)
	{
		return array ( 'remove_keyboard' => true, 'selective' => $selective);
	}
}
?>