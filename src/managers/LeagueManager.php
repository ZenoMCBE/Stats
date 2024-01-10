<?php

namespace stats\managers;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use stats\utils\ids\LeagueIds;
use stats\utils\Utils;

final class LeagueManager {

    use SingletonTrait;

    public const LEAGUES = [
        LeagueIds::UNRANKED => 0,
        LeagueIds::BRONZE_I => 100,
        LeagueIds::BRONZE_II => 200,
        LeagueIds::BRONZE_III => 300,
        LeagueIds::SILVER_I => 400,
        LeagueIds::SILVER_II => 500,
        LeagueIds::SILVER_III => 600,
        LeagueIds::GOLD_I => 700,
        LeagueIds::GOLD_II => 800,
        LeagueIds::GOLD_III => 900,
        LeagueIds::PLATINUM_I => 1000,
        LeagueIds::PLATINUM_II => 1100,
        LeagueIds::PLATINUM_III => 1200,
        LeagueIds::DIAMOND_I => 1300,
        LeagueIds::DIAMOND_II => 1400,
        LeagueIds::DIAMOND_III => 1500,
        LeagueIds::CHAMPION_I => 1600,
        LeagueIds::CHAMPION_II => 1700,
        LeagueIds::CHAMPION_III => 1800,
        LeagueIds::MASTER => 2000,
    ];

    public const LEAGUES_ADD = [
        LeagueIds::UNRANKED => 60,
        LeagueIds::BRONZE_I => 50,
        LeagueIds::BRONZE_II => 50,
        LeagueIds::BRONZE_III => 45,
        LeagueIds::SILVER_I => 45,
        LeagueIds::SILVER_II => 40,
        LeagueIds::SILVER_III => 40,
        LeagueIds::GOLD_I => 35,
        LeagueIds::GOLD_II => 35,
        LeagueIds::GOLD_III => 30,
        LeagueIds::PLATINUM_I => 25,
        LeagueIds::PLATINUM_II => 25,
        LeagueIds::PLATINUM_III => 20,
        LeagueIds::DIAMOND_I => 20,
        LeagueIds::DIAMOND_II => 15,
        LeagueIds::DIAMOND_III => 15,
        LeagueIds::CHAMPION_I => 10,
        LeagueIds::CHAMPION_II => 10,
        LeagueIds::CHAMPION_III => 10,
        LeagueIds::MASTER => 5,
        LeagueIds::GRAND_MASTER => 5
    ];

    public const LEAGUES_REDUCE = [
        LeagueIds::UNRANKED => 0,
        LeagueIds::BRONZE_I => 5,
        LeagueIds::BRONZE_II => 5,
        LeagueIds::BRONZE_III => 5,
        LeagueIds::SILVER_I => 5,
        LeagueIds::SILVER_II => 10,
        LeagueIds::SILVER_III => 10,
        LeagueIds::GOLD_I => 10,
        LeagueIds::GOLD_II => 10,
        LeagueIds::GOLD_III => 15,
        LeagueIds::PLATINUM_I => 15,
        LeagueIds::PLATINUM_II => 15,
        LeagueIds::PLATINUM_III => 20,
        LeagueIds::DIAMOND_I => 20,
        LeagueIds::DIAMOND_II => 20,
        LeagueIds::DIAMOND_III => 25,
        LeagueIds::CHAMPION_I => 25,
        LeagueIds::CHAMPION_II => 30,
        LeagueIds::CHAMPION_III => 30,
        LeagueIds::MASTER => 35,
        LeagueIds::GRAND_MASTER => 40
    ];

    /**
     * @param string|Player $player
     * @return string
     */
    public function getLeague(string|Player $player): string {
        $league = null;
        $eloApi = EloManager::getInstance();
        $elo = $eloApi->get($player);
        foreach (self::LEAGUES as $leagueId => $requiredElo) {
            if ($elo >= $requiredElo) {
                $league = $leagueId;
            }
        }
        if ($league == LeagueIds::MASTER) {
            return $eloApi->isFirst($player)
                ? LeagueIds::GRAND_MASTER
                : LeagueIds::MASTER;
        }
        return $league ?? LeagueIds::UNRANKED;
    }

    /**
     * @param string $league
     * @return string
     */
    public function getLeagueColor(string $league): string {
        return match ($league) {
            LeagueIds::UNRANKED => "§8",
            LeagueIds::BRONZE_I, LeagueIds::BRONZE_II, LeagueIds::BRONZE_III => "§6",
            LeagueIds::SILVER_I, LeagueIds::SILVER_II, LeagueIds::SILVER_III => "§7",
            LeagueIds::GOLD_I, LeagueIds::GOLD_II, LeagueIds::GOLD_III => "§e",
            LeagueIds::PLATINUM_I, LeagueIds::PLATINUM_II, LeagueIds::PLATINUM_III => "§b",
            LeagueIds::DIAMOND_I, LeagueIds::DIAMOND_II, LeagueIds::DIAMOND_III => "§9",
            LeagueIds::CHAMPION_I, LeagueIds::CHAMPION_II, LeagueIds::CHAMPION_III => "§d",
            LeagueIds::MASTER => "§u",
            LeagueIds::GRAND_MASTER => "§c"
        };
    }

    /**
     * @param string $league
     * @return int
     */
    public function getLeaguePriority(string $league): int {
        return match ($league) {
            LeagueIds::UNRANKED => 0,
            LeagueIds::BRONZE_I, LeagueIds::BRONZE_II, LeagueIds::BRONZE_III => 1,
            LeagueIds::SILVER_I, LeagueIds::SILVER_II, LeagueIds::SILVER_III => 2,
            LeagueIds::GOLD_I, LeagueIds::GOLD_II, LeagueIds::GOLD_III => 3,
            LeagueIds::PLATINUM_I, LeagueIds::PLATINUM_II, LeagueIds::PLATINUM_III => 4,
            LeagueIds::DIAMOND_I, LeagueIds::DIAMOND_II, LeagueIds::DIAMOND_III => 5,
            LeagueIds::CHAMPION_I, LeagueIds::CHAMPION_II, LeagueIds::CHAMPION_III => 6,
            LeagueIds::MASTER => 7,
            LeagueIds::GRAND_MASTER => 8
        };
    }

    /**
     * @param Player $player
     * @return string
     */
    public function formatLeague(Player $player): string {
        $eloApi = EloManager::getInstance();
        $leagueApi = LeagueManager::getInstance();
        $playerLeague = $leagueApi->getLeague($player);
        if ($leagueApi->isMaster($player)) {
            if ($eloApi->isFirst($player)) {
                return $leagueApi->getLeagueColor(LeagueIds::GRAND_MASTER) . "Grand Master #1";
            } else {
                return $leagueApi->getLeagueColor(LeagueIds::MASTER) . $playerLeague . " #" . $eloApi->getPlayerPosition($player);
            }
        }
        return $leagueApi->getLeagueColor($playerLeague) . $playerLeague;
    }

    /**
     * @param string|Player $player
     * @return int
     */
    public function getEloToAddByLeague(string|Player $player): int {
        $league = $this->getLeague($player);
        $eloToAdd = self::LEAGUES_ADD[$league] ?? 0;
        $bonusEloToAdd = mt_rand(-3, 3);
        return max($eloToAdd + $bonusEloToAdd, 1);
    }

    /**
     * @param string|Player $player
     * @return int
     */
    public function getEloToReduceByLeague(string|Player $player): int {
        $elo = EloManager::getInstance()->get($player);
        $league = $this->getLeague($player);
        $eloToReduce = self::LEAGUES_REDUCE[$league] ?? 0;
        $bonusEloToReduce = mt_rand(-3, 3);
        $finalEloToReduce = max($eloToReduce + $bonusEloToReduce, 1);
        return ($elo - $finalEloToReduce) >= 0 ? $finalEloToReduce : 0;
    }

    /**
     * @param string|Player $player
     * @param string $averageTeamLeague
     * @return bool
     */
    public function isBetterThanAverageTeamLeague(string|Player $player, string $averageTeamLeague): bool {
        $playerLeague = $this->getLeague($player);
        $playerLeaguePriority = $this->getLeaguePriority($playerLeague);
        $averageTeamLeaguePriority = $this->getLeaguePriority($averageTeamLeague);
        return $playerLeaguePriority > $averageTeamLeaguePriority;
    }

    /**
     * @param string|Player $player
     * @param int $playerScore
     * @param int $averageTeamScore
     * @param string $averageTeamLeague
     * @param bool|null $result
     * @return int
     */
    public function getFinalEloToSend(string|Player $player, int $playerScore, int $averageTeamScore, string $averageTeamLeague, ?bool $result): int {
        $gameResult = match ($result) {
            true => [mt_rand(10, 15), $this->getEloToAddByLeague($player)],
            false => [mt_rand(-15, -10), -$this->getEloToReduceByLeague($player)],
            null => [0, 0]
        };
        $hasBetterScore = ($playerScore > $averageTeamScore) ? mt_rand(3, 7) : 0;
        $playerLeaguePriority = $this->getLeaguePriority($this->getLeague($player));
        $averageTeamLeaguePriority = $this->getLeaguePriority($averageTeamLeague);
        $isBetterLeague = $this->isBetterThanAverageTeamLeague($player, $averageTeamLeague) ? -2 * ($playerLeaguePriority - $averageTeamLeaguePriority) : 0;
        return $gameResult[0] + $hasBetterScore + $isBetterLeague + $gameResult[1];
    }

    /**
     * @param string|Player $player
     * @return bool
     */
    public function isMaster(string|Player $player): bool {
        $playerName = Utils::getPlayerName($player, true);
        return $this->getLeague($playerName) == LeagueIds::MASTER;
    }

    /**
     * @param int $necessaryElo
     * @return string
     */
    public function getLeagueByNecessaryElo(int $necessaryElo): string {
        $flippedArray = array_flip(self::LEAGUES);
        foreach ($flippedArray as $requiredElo => $leagueId) {
            if ($necessaryElo >= $requiredElo) {
                $league = $leagueId;
            }
        }
        return $league ?? LeagueIds::UNRANKED;
    }

}
