<?php

declare(strict_types=1);

namespace zenostats\librairies\invmenu\session\network\handler;

use Closure;
use zenostats\librairies\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}
