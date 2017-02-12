<?php
namespace pocketmine\command\data;


class CommandParameter {

	const ARG_TYPE_STRING = "string";
	const ARG_TYPE_STRING_ENUM = "stringenum";
	const ARG_TYPE_BOOL = "bool";
	const ARG_TYPE_TARGET = "target";
	const ARG_TYPE_PLAYER = "target";
	const ARG_TYPE_BLOCK_POS = "blockpos";
	const ARG_TYPE_RAW_TEXT = "rawtext";
	const ARG_TYPE_INT = "int";

	const ARG_TYPE_TARGET_ALL_PLAYERS = "allPlayers";//@a
	const ARG_TYPE_TARGET_ALL_ENTITIES = "allEntities";//@e
	const ARG_TYPE_TARGET_NEAREST_PLAYER = "nearestPlayer";//@n
	const ARG_TYPE_TARGET_RANDOM_PLAYER = "randomPlayer";//@r

	public $name;
	public $type;
	public $optional;

	public function __construct($name, $type = self::ARG_TYPE_RAW_TEXT, $optional = false) {
		$this->name = $name;
		$this->type = $type;
		$this->optional = $optional;
	}

}
