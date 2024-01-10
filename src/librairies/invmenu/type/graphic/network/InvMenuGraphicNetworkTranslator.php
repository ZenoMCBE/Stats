<?php

declare(strict_types=1);

namespace stats\librairies\invmenu\type\graphic\network;

use stats\librairies\invmenu\session\InvMenuInfo;
use stats\librairies\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}
