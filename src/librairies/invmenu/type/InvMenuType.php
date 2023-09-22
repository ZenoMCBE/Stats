<?php

declare(strict_types=1);

namespace zenostats\librairies\invmenu\type;

use zenostats\librairies\invmenu\InvMenu;
use zenostats\librairies\invmenu\type\graphic\InvMenuGraphic;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

interface InvMenuType{

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic;

	public function createInventory() : Inventory;
}
