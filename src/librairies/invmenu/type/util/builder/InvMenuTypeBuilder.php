<?php

declare(strict_types=1);

namespace stats\librairies\invmenu\type\util\builder;

use stats\librairies\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}
