<?php

declare(strict_types=1);

namespace stats\librairies\invmenu\type;

use stats\librairies\invmenu\InvMenu;
use stats\librairies\invmenu\type\graphic\InvMenuGraphic;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

interface InvMenuType{

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic;

	public function createInventory() : Inventory;
}
