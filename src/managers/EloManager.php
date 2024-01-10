<?php

namespace stats\managers;

use JsonException;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use stats\datas\DataCache;
use stats\datas\DefaultDataCache;
use stats\utils\Utils;

final class EloManager implements DataCache, DefaultDataCache {

    use SingletonTrait;

    /**
     * @var array
     */
    private array $cache = [];

    /**
     * @var array
     */
    private array $resultElo = [];

    /**
     * @return void
     */
    public function loadCache(): void {
        $providerData = $this->getProvider()->getAll();
        foreach ($providerData as $key => $value) {
            $this->cache[$key] = $value;
        }
    }

    /**
     * @return array
     */
    public function getCache(): array {
        return $this->cache;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function setDefaultData(Player $player): void {
        if (!$this->exist($player)) {
            $playerName = Utils::getPlayerName($player, true);
            $this->cache[$playerName] = $this->getDefaultData();
        }
    }

    /**
     * @param string|Player $player
     * @return bool
     */
    public function exist(string|Player $player): bool {
        $playerName = Utils::getPlayerName($player, true);
        return array_key_exists($playerName, $this->cache);
    }

    /**
     * @param string|Player $player
     * @return int
     */
    public function get(string|Player $player): int {
        $playerName = Utils::getPlayerName($player, true);
        return intval($this->cache[$playerName]) ?? 0;
    }

    /**
     * @param string|Player $player
     * @param int $amount
     * @return void
     */
    public function add(string|Player $player, int $amount): void {
        $playerName = Utils::getPlayerName($player, true);
        $this->cache[$playerName] += $amount;
    }

    /**
     * @param string|Player $player
     * @param int $amount
     * @return void
     */
    public function reduce(string|Player $player, int $amount): void {
        $playerName = Utils::getPlayerName($player, true);
        $this->cache[$playerName] -= abs($amount);
        if ($this->get($player) < 0) {
            $this->set($player, 0);
        }
    }

    /**
     * @param string|Player $player
     * @param int $amount
     * @return void
     */
    public function set(string|Player $player, int $amount): void {
        $playerName = Utils::getPlayerName($player, true);
        $this->cache[$playerName] = $amount;
    }

    /**
     * @param string|Player $player
     * @param int $score
     * @param int $averageTeamScore
     * @param string $averageTeamLeague
     * @param bool|null $result
     * @return void
     */
    public function update(string|Player $player, int $score, int $averageTeamScore, string $averageTeamLeague, ?bool $result): void {
        $leagueApi = LeagueManager::getInstance();
        $playerName = Utils::getPlayerName($player, true);
        $eloToSend = $leagueApi->getFinalEloToSend($player, $score, $averageTeamScore, $averageTeamLeague, $result);
        if ($eloToSend >= 0) {
            $this->add($player, $eloToSend);
        } else {
            $this->reduce($player, $eloToSend);
        }
        $this->resultElo[$playerName] = $eloToSend;
    }

    /**
     * @return array
     */
    public function getResultElo(): array {
        return $this->resultElo;
    }

    /**
     * @param string|Player $player
     * @return string
     */
    public function getStringResultElo(string|Player $player): string {
        $playerName = Utils::getPlayerName($player, true);
        $resultElo = $this->resultElo[$playerName];
        return $resultElo >= 0 ? "+" . $resultElo : strval($resultElo);
    }

    /**
     * @return void
     */
    public function resetResultElo(): void {
        $this->resultElo = [];
    }

    /**
     * @return int
     */
    public function getDefaultData(): int {
        return 0;
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function unloadCache(): void {
        $provider = $this->getProvider();
        $provider->setAll([]);
        foreach ($this->getCache() as $key => $value) {
            $provider->set($key, $value);
        }
        $provider->save();
    }

    /**
     * @return array
     */
    public function getTop(): array {
        $top = $this->cache;
        arsort($top);
        return $top;
    }

    /**
     * @param string|Player $player
     * @return int
     */
    public function getPlayerPosition(string|Player $player): int {
        $playerName = Utils::getPlayerName($player, true);
        $leaderboardKeys = array_keys($this->getTop());
        $position = array_search($playerName, $leaderboardKeys);
        return $position + 1;
    }

    /**
     * @param string|Player $player
     * @return bool
     */
    public function isFirst(string|Player $player): bool {
        $playerName = Utils::getPlayerName($player, true);
        return $this->getPlayerPosition($playerName) === 1;
    }

    /**
     * @return Config
     */
    public function getProvider(): Config {
        return ProvidersManager::getInstance()->getProvider("Elo");
    }

}
