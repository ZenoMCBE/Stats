<?php

declare(strict_types=1);

namespace stats\librairies\invmenu\session;

use stats\librairies\invmenu\InvMenu;
use stats\librairies\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		readonly public InvMenu $menu,
		readonly public InvMenuGraphic $graphic,
		readonly public ?string $graphic_name
	){}
}
