<?php
// https://core.telegram.org/bots/api#user
class User
{
	public $id;
	public $is_bot;
	public $is_admin;
	public $first_name;
	public $last_name;
	public $username;
	public $lang;
	public $can_join_groups;
	public $can_read_all_group_messages;
	public $supports_inline_queries;
	public $last_msg;

	public function __construct($user)
	{
		$this->id = $user['id'];
		$this->is_bot = $user['is_bot'];
		$this->is_admin = false;
		$this->first_name = $user['first_name'];
		$this->last_name = $user['last_name'];
		$this->username = $user['username'];
		$this->lang = $user['language_code'];
	}
	
}
?>