<?php

namespace zenostats\loaders\childs;

use zenostats\loaders\Loader;
use zenostats\managers\ProvidersManager;
use zenostats\ZenoStats;

final class ProvidersLoader implements Loader {

    /**
     * @return void
     */
    public function onLoad(): void {
        $providerApi = ProvidersManager::getInstance();
        $providerApi->loadProviders();
        ZenoStats::getInstance()->getLogger()->notice("[Provider] " . $providerApi->getProviderCount() . " structure(s) de donnée(s) chargée(s) !");
    }

    /**
     * @return void
     */
    public function onUnload(): void {}

}
