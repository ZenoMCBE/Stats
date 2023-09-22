<?php

namespace zenostats\loaders\childs;

use JsonException;
use zenostats\datas\DataCache;
use zenostats\loaders\Loader;
use zenostats\managers\EloManager;
use zenostats\managers\StatsManager;
use zenostats\ZenoStats;

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
        ZenoStats::getInstance()->getLogger()->notice("[Data] " . count($classes) . " fichier(s) de donnée(s) chargé(s) !");
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
        ZenoStats::getInstance()->getLogger()->notice("[Data] " . count($classes) . " fichier(s) de donnée(s) déchargé(s) !");
    }

}
