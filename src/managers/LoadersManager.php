<?php

namespace stats\managers;

use JsonException;
use pocketmine\utils\SingletonTrait;
use stats\loaders\childs\DatasLoader;
use stats\loaders\childs\ProvidersLoader;
use stats\loaders\Loader;
use stats\Stats;

final class LoadersManager {

    use SingletonTrait;

    /**
     * @return void
     */
    public function loadAll(): void {
        $loaders = [
            new ProvidersLoader(),
            new DatasLoader(),
        ];
        foreach ($loaders as $loader) {
            if (isset(class_implements($loader)[Loader::class])) {
                $loader->onLoad();
            }
        }
        Stats::getInstance()->getLogger()->notice("[Loader] " . count($loaders) ." chargeur(s) enregistré(s) !");
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function unloadAll(): void {
        $loaders = [
            new DatasLoader()
        ];
        foreach ($loaders as $loader) {
            if (isset(class_implements($loader)[Loader::class])) {
                $loader->onUnload();
            }
        }
        Stats::getInstance()->getLogger()->notice("[Loader] " . count($loaders) ." chargeur(s) oublié(s) !");
    }

}
