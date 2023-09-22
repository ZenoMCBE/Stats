<?php

namespace zenostats;

use JsonException;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use zenostats\managers\EloManager;
use zenostats\managers\LeagueManager;
use zenostats\managers\LoadersManager;
use zenostats\managers\StatsManager;

final class ZenoStats extends PluginBase {

    use SingletonTrait;

    /**
     * @return void
     */
    protected function onLoad(): void {
        self::setInstance($this);
    }

    /**
     * @return void
     */
    protected function onEnable(): void {
        LoadersManager::getInstance()->loadAll();

        $this->getLogger()->notice("Zeno Stats a été activé avec succès !");
    }

    /**
     * @return void
     * @throws JsonException
     */
    protected function onDisable(): void {
        LoadersManager::getInstance()->unloadAll();

        $this->getLogger()->notice("Zeno Stats a été activé avec succès !");
    }


    /**
     * @return EloManager
     */
    public function getEloManager(): EloManager {
        return EloManager::getInstance();
    }

    /**
     * @return LeagueManager
     */
    public function getLeagueManager(): LeagueManager {
        return LeagueManager::getInstance();
    }

    /**
     * @return StatsManager
     */
    public function getStatsManager(): StatsManager {
        return StatsManager::getInstance();
    }

}
