<?php

namespace stats\managers;

use JsonException;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use stats\datas\DataCache;
use stats\datas\DefaultDataCache;
use stats\forms\LeaderboardForms;
use stats\librairies\formapi\SimpleForm;
use stats\utils\ids\StatsIds;
use stats\utils\Utils;

final class StatsManager implements DataCache, DefaultDataCache {

    use SingletonTrait;

    /**
     * @var array
     */
    private array $cache = [];

    /**
     * @return void
     */
    public function loadCache(): void {
        $this->cache = $this->getProvider()->getAll();
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
     * @param string $stats
     * @return int
     */
    public function get(string|Player $player, string $stats): int {
        $playerName = Utils::getPlayerName($player, true);
        return array_key_exists($stats, $this->cache[$playerName]) ? intval($this->cache[$playerName][$stats]) : 0;
    }

    /**
     * @param string|Player $player
     * @param string $stats
     * @param int $amount
     * @return void
     */
    public function add(string|Player $player, string $stats, int $amount): void {
        $playerName = Utils::getPlayerName($player, true);
        if (array_key_exists($stats, $this->cache[$playerName])) {
            $this->cache[$playerName][$stats] += $amount;
        } else {
            $this->cache[$playerName][$stats] = $amount;
        }
    }

    /**
     * @param string|Player $player
     * @param string $stats
     * @param int $amount
     * @return void
     */
    public function reduce(string|Player $player, string $stats, int $amount): void {
        $playerName = Utils::getPlayerName($player, true);
        $finalAmount = max(0, $this->get($player, $stats) - $amount);
        if (array_key_exists($stats, $this->cache[$playerName])) {
            $this->cache[$playerName][$stats] -= $finalAmount;
        } else {
            $this->cache[$playerName][$stats] = $finalAmount;
        }
    }

    /**
     * @param string|Player $player
     * @param string $stats
     * @param int $amount
     * @return void
     */
    public function set(string|Player $player, string $stats, int $amount): void {
        $playerName = Utils::getPlayerName($player, true);
        $this->cache[$playerName][$stats] = $amount;
    }

    /**
     * @param string|Player $player
     * @param int $score
     * @param array $stats
     * @param bool|null $result
     * @return void
     */
    public function update(string|Player $player, int $score, array $stats, ?bool $result): void {
        $playerName = Utils::getPlayerName($player, true);
        $this->add($player, StatsIds::SCORE, $score);
        foreach ($stats as $stat => $value) {
            if (
                array_key_exists($stat, $this->cache[$playerName]) &&
                $stat !== StatsIds::BEST_KILLSTREAK
            ) {
                $this->add($player, $stat, $value);
            } else if ($stat == StatsIds::BEST_KILLSTREAK) {
                if ($this->get($player, StatsIds::BEST_KILLSTREAK) < $value) {
                    $this->set($player, StatsIds::BEST_KILLSTREAK, $value);
                }
            } else {
                $this->set($player, $stat, $value);
            }
        }
        $this->add($player, StatsIds::PLAYED, 1);
        if (!is_null($result)) {
            if ($result) {
                $this->add($player, StatsIds::WIN, 1);
            } else {
                $this->add($player, StatsIds::LOSE, 1);
            }
        }
    }

    /**
     * @param string|Player $player
     * @return int|float|null
     */
    public function calculateKdr(string|Player $player): int|float|null {
        $playerName = Utils::getPlayerName($player, true);
        $kill = max(0, $this->get($playerName, StatsIds::KILL));
        $death = max(0, $this->get($playerName, StatsIds::DEATH));
        return ($kill > 0 && $death > 0) ? round($kill / $death, 2) : null;
    }

    /**
     * @param string|Player $player
     * @return int|float|null
     */
    public function calculateKadr(string|Player $player): int|float|null {
        $playerName = Utils::getPlayerName($player, true);
        $kill = max(0, $this->get($playerName, StatsIds::KILL));
        $assist = max(0, $this->get($playerName, StatsIds::ASSIST));
        $death = max(0, $this->get($playerName, StatsIds::DEATH));
        return (($kill > 0 || $assist > 0) && $death > 0) ? round(($kill + $assist) / $death, 2) : null;
    }

    /**
     * @param string|Player $player
     * @return int|null
     */
    public function calculateKillPerGame(string|Player $player): ?int {
        $playerName = Utils::getPlayerName($player, true);
        $played = max(0, $this->get($playerName, StatsIds::PLAYED));
        $kill = max(0, $this->get($playerName, StatsIds::KILL));
        return ($played > 0 && $kill > 0) ? round($kill / $played) : null;
    }

    /**
     * @param string|Player $player
     * @return int|null
     */
    public function calculateKillAssistPerGame(string|Player $player): ?int {
        $playerName = Utils::getPlayerName($player, true);
        $played = max(0, $this->get($playerName, StatsIds::PLAYED));
        $kill = max(0, $this->get($playerName, StatsIds::KILL));
        $assist = max(0, $this->get($playerName, StatsIds::ASSIST));
        return ($played > 0 && ($kill > 0 || $assist > 0)) ? round(($kill + $assist) / $played) : null;
    }

    /**
     * @param string|Player $player
     * @return int|null
     */
    public function calculateDeathPerGame(string|Player $player): ?int {
        $playerName = Utils::getPlayerName($player, true);
        $played = max(0, $this->get($playerName, StatsIds::PLAYED));
        $death = max(0, $this->get($playerName, StatsIds::DEATH));
        return ($played > 0 && $death > 0) ? round($death / $played) : null;
    }

    /**
     * @param string|Player $player
     * @return int|null
     */
    public function calculateArrowHitByArrowShotPercentage(string|Player $player): ?int {
        $playerName = Utils::getPlayerName($player, true);
        $arrowShot = max(0, $this->get($playerName, StatsIds::ARROW_SHOT));
        $arrowHit = max(0, $this->get($playerName, StatsIds::ARROW_HIT));
        return ($arrowShot > 0 && $arrowHit > 0) ? round(($arrowHit / $arrowShot) * 100) : null;
    }

    /**
     * @param string|Player $player
     * @return int|null
     */
    public function calculateWinratePercentage(string|Player $player): ?int {
        $playerName = Utils::getPlayerName($player, true);
        $played = max(0, $this->get($playerName, StatsIds::PLAYED));
        $win = max(0, $this->get($playerName, StatsIds::WIN));
        return ($played > 0 && $win > 0) ? round(($win / $played) * 100) : null;
    }

    /**
     * @param string|Player $player
     * @return int|null
     */
    public function calculateAverageScorePerGame(string|Player $player): ?int {
        $playerName = Utils::getPlayerName($player, true);
        $played = max(0, $this->get($playerName, StatsIds::PLAYED));
        $score = max(0, $this->get($playerName, StatsIds::SCORE));
        return ($played > 0 && $score > 0) ? round($score / $played) : null;
    }

    /**
     * @param string|Player $player
     * @return int|null
     */
    public function calculateAveragePointPerGame(string|Player $player): ?int {
        $playerName = Utils::getPlayerName($player, true);
        $played = max(0, $this->get($playerName, StatsIds::PLAYED));
        $point = max(0, $this->get($playerName, StatsIds::POINT));
        return ($played > 0 && $point > 0) ? round($point / $played) : null;
    }

    /**
     * @param string|Player $player
     * @return int|null
     */
    public function calculateAverageArrowShootPerGame(string|Player $player): ?int {
        $playerName = Utils::getPlayerName($player, true);
        $played = max(0, $this->get($playerName, StatsIds::PLAYED));
        $arrowShoot = max(0, $this->get($playerName, StatsIds::ARROW_SHOT));
        return ($played > 0 && $arrowShoot > 0) ? round($arrowShoot / $played) : null;
    }

    /**
     * @param string|Player $player
     * @return int|null
     */
    public function calculateAverageArrowHitPerGame(string|Player $player): ?int {
        $playerName = Utils::getPlayerName($player, true);
        $played = max(0, $this->get($playerName, StatsIds::PLAYED));
        $arrowHit = max(0, $this->get($playerName, StatsIds::ARROW_HIT));
        return ($played > 0 && $arrowHit > 0) ? round($arrowHit / $played) : null;
    }

    /**
     * @param string|Player $player
     * @return int|null
     */
    public function calculateAverageArrowBoostPerGame(string|Player $player): ?int {
        $playerName = Utils::getPlayerName($player, true);
        $played = max(0, $this->get($playerName, StatsIds::PLAYED));
        $arrowBoost = max(0, $this->get($playerName, StatsIds::ARROW_BOOST));
        return ($played > 0 && $arrowBoost > 0) ? round($arrowBoost / $played) : null;
    }

    /**
     * @param string|Player $player
     * @return int|float|null
     */
    public function calculateDamageDealedPerGame(string|Player $player): int|float|null {
        $playerName = Utils::getPlayerName($player, true);
        $played = max(0, $this->get($playerName, StatsIds::PLAYED));
        $damageDealed = max(0, $this->get($playerName, StatsIds::DAMAGE_DEALED));
        return ($played > 0 && $damageDealed > 0) ? round($damageDealed / $played, 2) : null;
    }

    /**
     * @param string|Player $player
     * @return int|float|null
     */
    public function calculateDamageTakenPerGame(string|Player $player): int|float|null {
        $playerName = Utils::getPlayerName($player, true);
        $played = max(0, $this->get($playerName, StatsIds::PLAYED));
        $damageTaken = max(0, $this->get($playerName, StatsIds::DAMAGE_TAKEN));
        return ($played > 0 && $damageTaken > 0) ? round($damageTaken / $played, 2) : null;
    }

    /**
     * @param string|Player $player
     * @return int|float|null
     */
    public function calculateAverageGoldenAppleEatenPerGame(string|Player $player): int|float|null {
        $playerName = Utils::getPlayerName($player, true);
        $played = max(0, $this->get($playerName, StatsIds::PLAYED));
        $goldenAppleEaten = max(0, $this->get($playerName, StatsIds::GOLDEN_APPLE_EATEN));
        return ($played > 0 && $goldenAppleEaten > 0) ? round($goldenAppleEaten / $played, 2) : null;
    }

    /**
     * @param string|Player $player
     * @return int|null
     */
    public function calculateAverageCriticalHitPerGame(string|Player $player): ?int {
        $playerName = Utils::getPlayerName($player, true);
        $played = max(0, $this->get($playerName, StatsIds::PLAYED));
        $criticalHit = max(0, $this->get($playerName, StatsIds::CRIT));
        return ($played > 0 && $criticalHit > 0) ? round($criticalHit / $played) : null;
    }

    /**
     * @param string $statsId
     * @return array
     */
    public function getTopStats(string $statsId): array {
        $array = [];
        foreach ($this->cache as $player => $stat) {
            if (array_key_exists($statsId, $stat)) {
                $array[$player] = $stat[$statsId];
            }
        }
        arsort($array);
        return $array;
    }

    /**
     * @return mixed
     */
    public function getDefaultData(): array {
        return [
            StatsIds::PLAYED => 0,
            StatsIds::WIN => 0,
            StatsIds::LOSE => 0,
            StatsIds::SCORE => 0,
            StatsIds::KILL => 0,
            StatsIds::ASSIST => 0,
            StatsIds::DEATH => 0,
            StatsIds::VOID_DEATH => 0,
            StatsIds::POINT => 0,
            StatsIds::BEST_KILLSTREAK => 0,
            StatsIds::ARROW_SHOT => 0,
            StatsIds::ARROW_HIT => 0,
            StatsIds::ARROW_BOOST => 0,
            StatsIds::DAMAGE_DEALED => 0,
            StatsIds::DAMAGE_TAKEN => 0,
            StatsIds::GOLDEN_APPLE_EATEN => 0,
            StatsIds::CRIT => 0
        ];
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function unloadCache(): void {
        $provider = $this->getProvider();
        $provider->setAll($this->getCache());
        $provider->save();
    }

    /**
     * @param string $stats
     * @return string
     */
    public function getStatsNameByStats(string $stats): string {
        return match ($stats) {
            StatsIds::PLAYED => "Partie(s) jouée(s)",
            StatsIds::WIN => "Partie(s) gagnée(s)",
            StatsIds::LOSE => "Partie(s) perdue(s)",
            StatsIds::SCORE => "Score global",
            StatsIds::ELO => "Elo(s)",
            StatsIds::KILL => "Kill(s)",
            StatsIds::ASSIST => "Assistance(s)",
            StatsIds::DEATH => "Mort(s)",
            StatsIds::VOID_DEATH => "Mort(s) dans le vide",
            StatsIds::POINT => "Point(s)",
            StatsIds::BEST_KILLSTREAK => "Meilleure série de kill(s)",
            StatsIds::ARROW_SHOT => "Flèche(s) tirée(s)",
            StatsIds::ARROW_HIT => "Flèche(s) touchée(s)",
            StatsIds::ARROW_BOOST => "Boost(s) à l'arc",
            StatsIds::DAMAGE_DEALED => "Dégât(s) infligé(s)",
            StatsIds::DAMAGE_TAKEN => "Dégât(s) subit(s)",
            StatsIds::GOLDEN_APPLE_EATEN => "Gapple(s) mangée(s)",
            StatsIds::CRIT => "Coup(s) critique(s)"
        };
    }

    /**
     * @param string $stats
     * @return bool
     */
    public function isValidStats(string $stats): bool {
        return in_array($stats, $this->getAllStats());
    }

    /**
     * @return array
     */
    public function getAllStats(): array {
        return StatsIds::ALL_STATS;
    }

    /**
     * @return SimpleForm
     */
    public function getMainLeaderboardForm(): SimpleForm {
        return LeaderboardForms::getInstance()->mainCategoryLeaderboard();
    }

    /**
     * @return Config
     */
    public function getProvider(): Config {
        return ProvidersManager::getInstance()->getProvider("Stats");
    }

}
