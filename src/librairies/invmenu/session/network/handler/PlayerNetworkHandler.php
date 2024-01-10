<?php

declare(strict_types=1);

namespace stats\librairies\invmenu\session\network\handler;

use Closure;
use stats\librairies\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}
