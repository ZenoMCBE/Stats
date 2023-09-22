<?php

namespace zenostats\managers;

use JsonException;
use pocketmine\utils\SingletonTrait;
use zenostats\loaders\childs\DatasLoader;
use zenostats\loaders\childs\ProvidersLoader;
use zenostats\loaders\Loader;
use zenostats\ZenoStats;

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
        ZenoStats::getInstance()->getLogger()->notice("[Loader] " . count($loaders) ." chargeur(s) enregistré(s) !");
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
        ZenoStats::getInstance()->getLogger()->notice("[Loader] " . count($loaders) ." chargeur(s) oublié(s) !");
    }

}
