<?php

namespace stats;

use pocketmine\event\EventPriority;
use pocketmine\event\plugin\PluginDisableEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use ReflectionException;
use stats\managers\{EloManager, LeagueManager, LoadersManager, StatsManager};

final class Stats extends PluginBase {

    use SingletonTrait;

    /**
     * @return void
     */
    protected function onLoad(): void {
        self::setInstance($this);
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    protected function onEnable(): void {
        LoadersManager::getInstance()->loadAll();

        $this->getServer()->getPluginManager()->registerEvent(PluginDisableEvent::class, function (PluginDisableEvent $event): void {
            if ($event->getPlugin()->getName() == $this->getName()) {
                LoadersManager::getInstance()->unloadAll();
            }
        }, EventPriority::LOWEST, $this);

        $this->getLogger()->notice("Stats activé.");
    }

    /**
     * @return void
     */
    protected function onDisable(): void {
        $this->getLogger()->notice("Stats désactivé.");
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
