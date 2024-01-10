<?php

namespace stats\loaders\childs;

use JsonException;
use stats\datas\DataCache;
use stats\loaders\Loader;
use stats\managers\EloManager;
use stats\managers\StatsManager;
use stats\Stats;

final class DatasLoader implements Loader {

    /**
     * @return void
     */
    public function onLoad(): void {
        $classes = [
            EloManager::getInstance(),
            StatsManager::getInstance(),
        ];
        foreach ($classes as $class) {
            if (isset(class_implements($class)[DataCache::class])) {
                $class->loadCache();
            }
        }
        Stats::getInstance()->getLogger()->notice("[Data] " . count($classes) . " fichier(s) de donnée(s) chargé(s) !");
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function onUnload(): void {
        $classes = [
            EloManager::getInstance(),
            StatsManager::getInstance(),
        ];
        foreach ($classes as $class) {
            if (isset(class_implements($class)[DataCache::class])) {
                $class->unloadCache();
            }
        }
        Stats::getInstance()->getLogger()->notice("[Data] " . count($classes) . " fichier(s) de donnée(s) déchargé(s) !");
    }

}
