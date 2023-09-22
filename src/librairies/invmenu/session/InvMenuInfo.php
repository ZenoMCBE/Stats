<?php

declare(strict_types=1);

namespace zenostats\librairies\invmenu\session;

use zenostats\librairies\invmenu\InvMenu;
use zenostats\librairies\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		readonly public InvMenu $menu,
		readonly public InvMenuGraphic $graphic,
		readonly public ?string $graphic_name
	){}
}
