<?php

namespace stats\managers;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use stats\Stats;

final class ProvidersManager {

    use SingletonTrait;

    /**
     * @var Config[]
     */
    private array $providers = [];

    /**
     * @return void
     */
    public function loadProviders(): void {
        $pluginDataFolder = Stats::getInstance()->getDataFolder();
        $this->addProvider("Elo", new Config($pluginDataFolder . "Elo.json", Config::JSON));
        $this->addProvider("Stats", new Config($pluginDataFolder . "Stats.json", Config::JSON));
    }

    /**
     * @param string $name
     * @param Config $config
     * @return void
     */
    private function addProvider(string $name, Config $config): void {
        if (!$this->isAlreadyLoaded($name)) {
            $this->providers[$name] = $config;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isAlreadyLoaded(string $name): bool {
        return in_array($name, $this->providers);
    }

    /**
     * @return int
     */
    public function getProviderCount(): int {
        return count($this->providers);
    }

    /**
     * @param string $name
     * @return Config|null
     */
    public function getProvider(string $name): ?Config {
        return $this->providers[$name] ?? null;
    }

}
