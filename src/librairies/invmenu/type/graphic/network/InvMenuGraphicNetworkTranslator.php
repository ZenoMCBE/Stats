<?php

declare(strict_types=1);

namespace zenostats\librairies\invmenu\type\graphic\network;

use zenostats\librairies\invmenu\session\InvMenuInfo;
use zenostats\librairies\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}
