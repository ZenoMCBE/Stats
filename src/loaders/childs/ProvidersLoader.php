<?php

namespace stats\loaders\childs;

use stats\loaders\Loader;
use stats\managers\ProvidersManager;
use stats\Stats;

final class ProvidersLoader implements Loader {

    /**
     * @return void
     */
    public function onLoad(): void {
        $providerApi = ProvidersManager::getInstance();
        $providerApi->loadProviders();
        Stats::getInstance()->getLogger()->notice("[Provider] " . $providerApi->getProviderCount() . " structure(s) de donnée(s) chargée(s) !");
    }

    /**
     * @return void
     */
    public function onUnload(): void {}

}
