<?php

declare(strict_types=1);

namespace zenostats\librairies\invmenu\type\util\builder;

use zenostats\librairies\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}
