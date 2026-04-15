<?php

namespace pocketmine\command\defaults;

use pocketmine\command\CommandSender;

class DameCommand extends VanillaCommand
{
	public const DAME = [
		"だめだね だめよ だめなのよ\nあんだな 好きで 好き好きで\nどれだけ 強いを酒でも\n歪まない思い出が ばかみたい", //桐生一马
		"エッチのダメに、死刑！" //下江小春
	];

	public function __construct($name)
	{
		parent::__construct(
			$name,
			"EGG",
			"/dame",
			["だめ", "ダメ"]
			);
	}

	public function execute(CommandSender $sender, $commandLabel, array $args)
	{
		$randomText = self::DAME[array_rand(self::DAME)];
		$sender->sendMessage($randomText);
		return true;
	}
}