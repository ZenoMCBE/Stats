<?php

namespace zenostats\forms;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use zenostats\librairies\formapi\CustomForm;
use zenostats\librairies\formapi\Form;
use zenostats\librairies\formapi\ModalForm;
use zenostats\librairies\formapi\SimpleForm;
use zenostats\managers\EloManager;
use zenostats\managers\LeagueManager;
use zenostats\managers\StatsManager;
use zenostats\utils\Constants;
use zenostats\utils\ids\StatsIds;
use zenostats\utils\Utils;

final class LeaderboardForms {

    use SingletonTrait;

    /**
     * @return SimpleForm
     */
    public function mainCategoryLeaderboard(): SimpleForm {
        $form = new SimpleForm(function (Player $player, ?int $data = null) {
            if (!is_null($data)) {
                $form = match ($data) {
                    0 => $this->categoryStatsLeaderboard(),
                    1 => $this->searchSpecificPlayerStats(),
                    default => null
                };
                if (!is_null($form)) {
                    $this->returnForm($player, $form);
                }
            }
        });
        $form->setTitle("§l§q» §r§aClassements §l§q«§r");
        $form->setContent("§l§q» §r§fBienvenue dans le menu des §aclassements §f!");
        $form->addButton("§8Consulter les catérogies");
        $form->addButton("§8Chercher un joueur");
        return $form;
    }

    /**
     * @return SimpleForm
     */
    public function categoryStatsLeaderboard(): SimpleForm {
        $statsApi = StatsManager::getInstance();
        $form = new SimpleForm(function (Player $player, ?string $data = null) use ($statsApi) {
            if (!is_null($data)) {
                if (
                    $statsApi->isValidStats($data) ||
                    $data == "elo"
                ) {
                    $this->returnForm($player, $this->showLeaderboard($data));
                }
            }
        });
        $form->setTitle("§l§q» §r§aClassements §l§q«§r");
        $form->setContent(Constants::PREFIX . "§fVeuillez cliquer sur une catégorie pour consulter le classement de celle-ci !");
        $form->addButton("Elo(s)", label: "elo");
        foreach ($statsApi->getAllStats() as $stat) {
            $form->addButton($statsApi->getStatsNameByStats($stat), label: $stat);
        }
        return $form;
    }

    /**
     * @param string $stats
     * @return SimpleForm
     */
    public function showLeaderboard(string $stats): SimpleForm {
        $position = 1;
        $eloApi = EloManager::getInstance();
        $statsApi = StatsManager::getInstance();
        $form = new SimpleForm(function (Player $player, ?string $data = null) use ($statsApi, $stats) {
            if (is_string($data)) {
                if ($statsApi->exist($data)) {
                    $player->sendForm($this->showSpecificPlayerStats($data, $stats));
                }
            }
        });
        $top = $stats == "elo" ? $eloApi->getTop() : $statsApi->getTopStats($stats);
        $form->setTitle("§r§l§q» §r§aClassement " . $statsApi->getStatsNameByStats($stats) . " §l§q«");
        $form->setContent(Constants::PREFIX . "§fVoici le classement de la catégorie §a" . strtolower($statsApi->getStatsNameByStats($stats)) . " §f! Veuillez cliquer sur un joueur pour consulter ses statistiques !");
        foreach ($top as $player => $stat) {
            if ($position <= 50) {
                $form->addButton("§8[" . $position . "]\n§8" . Utils::getPlayerName($player, false) . " §7(§8" . $stat . "§7)", label: $player);
                $position++;
            } else break;
        }
        return $form;
    }

    /**
     * @param string $playerName
     * @param string|null $statsIds
     * @return ModalForm
     */
    public function showSpecificPlayerStats(string $playerName, ?string $statsIds): ModalForm {
        $eloApi = EloManager::getInstance();
        $leagueApi = LeagueManager::getInstance();
        $statsApi = StatsManager::getInstance();
        $form = new ModalForm(function (Player $player, ?bool $data = null) use ($statsIds) {
            if ($data && !is_null($statsIds)) {
                $this->returnForm($player, $this->showLeaderboard($statsIds));
            }
        });
        $form->setTitle("§l§q» §r§aStatistiques de " . Utils::getPlayerName($playerName, false) . " §l§q«§r");
        $content = "§l§q» §r§aPartie(s) §l§q«§r\n";
        $content .= "§fPartie(s) jouée(s)§8: §f" . $statsApi->get($playerName, StatsIds::PLAYED) . "\n";
        $content .= "§fVictoire(s)§8: §f" . $statsApi->get($playerName, StatsIds::WIN) . "\n";
        $content .= "§fDéfaite(s)§8: §f" . $statsApi->get($playerName, StatsIds::LOSE) . "\n";
        $content .= "§fPourcentage de victoire(s)§8: §f%" . $statsApi->calculateWinratePercentage($playerName) . "%%%%\n\n";

        $content .= "§l§q» §r§aKill(s) §l§q«§r\n";
        $content .= "§fKill(s)§8: §f" . $statsApi->get($playerName, StatsIds::KILL) . "\n";
        $content .= "§fAssistance(s)§8: §f" . $statsApi->get($playerName, StatsIds::ASSIST) . "\n";
        $content .= "§fMort(s)§8: §f" . $statsApi->get($playerName, StatsIds::DEATH) . "\n";
        $content .= "§fK/D§8: §f" . $statsApi->calculateKdr($playerName) . "\n";
        $content .= "§fK+A/D§8: §f" . $statsApi->calculateKadr($playerName) . "\n";
        $content .= "§fKill(s) par partie jouée§8: §f" . $statsApi->calculateKillPerGame($playerName) . "\n";
        $content .= "§fKill(s) + Assist(s) par partie jouée§8: §f" . $statsApi->calculateKillAssistPerGame($playerName) . "\n";
        $content .= "§fMort(s) par partie jouée§8: §f" . $statsApi->calculateDeathPerGame($playerName) . "\n\n";

        $content .= "§l§q» §r§aDégât(s) §l§q«§r\n";
        $content .= "§fDégât(s) infligé(s)§8: §f" . $statsApi->get($playerName, StatsIds::DAMAGE_DEALED) . "\n";
        $content .= "§fDégât(s) subit(s)§8: §f" . $statsApi->get($playerName, StatsIds::DAMAGE_TAKEN) . "\n";
        $content .= "§fDégât(s) infligé(s) par partie jouée§8: §f" . $statsApi->calculateDamageDealedPerGame($playerName) . "\n";
        $content .= "§fDégât(s) subit(s) par partie jouée§8: §f" . $statsApi->calculateDamageTakenPerGame($playerName) . "\n\n";

        $content .= "§l§q» §r§aFlèche(s) §l§q«§r\n";
        $content .= "§fFlèche(s) tirée(s)§8: §f" . $statsApi->get($playerName, StatsIds::ARROW_SHOT) . "\n";
        $content .= "§fFlèche(s) touchée(s)§8: §f" . $statsApi->get($playerName, StatsIds::ARROW_HIT) . "\n";
        $content .= "§fPourcentage de flèche(s) touchée(s)§8: §f%" . $statsApi->calculateArrowHitByArrowShotPercentage($playerName) . "%%%%\n";
        $content .= "§fFlèche(s) tirée(s) par partie jouée§8: §f" . $statsApi->calculateAverageArrowShootPerGame($playerName) . "\n";
        $content .= "§fFlèche(s) touchée(s) par partie jouée§8: §f" . $statsApi->calculateAverageArrowHitPerGame($playerName) . "\n";
        $content .= "§fBoost(s) à l'arc§8: §f" . $statsApi->get($playerName, StatsIds::ARROW_BOOST) . "\n";
        $content .= "§fBoost(s) à l'arc par partie jouée§8: §f" . $statsApi->calculateAverageArrowBoostPerGame($playerName) . "\n\n";

        $content .= "§l§q» §r§aAutre(s) §l§q«§r\n";
        $content .= "§fElo§8: §f" . $eloApi->get($playerName) . "\n";
        $content .= "§fLigue§8: " . $leagueApi->getLeagueColor($leagueApi->getLeague($playerName)) . $leagueApi->getLeague($playerName) . "\n";
        $content .= "§fScore total§8: §f" . $statsApi->get($playerName, StatsIds::SCORE) . "\n";
        $content .= "§fScore par partie jouée§8: §f" . $statsApi->calculateAverageScorePerGame($playerName) . "\n";
        $content .= "§fGapple(s) mangée(s)§8: §f" . $statsApi->get($playerName, StatsIds::GOLDEN_APPLE_EATEN) . "\n";
        $content .= "§fGapple(s) mangée(s) par partie jouée§8: §f" . $statsApi->calculateAverageGoldenAppleEatenPerGame($playerName) . "\n";
        $content .= "§fCoup(s) critique(s)§8: §f" . $statsApi->get($playerName, StatsIds::CRIT) . "\n";
        $content .= "§fCoup(s) critique(s) par partie jouée§8: §f" . $statsApi->calculateAverageCriticalHitPerGame($playerName) . "\n";
        $form->setContent($content);
        $form->setButton1("§8Revenir au classement");
        $form->setButton2("§8Quitter");
        return $form;
    }

    /**
     * @return CustomForm
     */
    public function searchSpecificPlayerStats(): CustomForm {
        $form = new CustomForm(function (Player $player, ?array $data = null) {
            if (!is_null($data)) {
                if ($data[0] !== "") {
                    if (StatsManager::getInstance()->exist($data[0])) {
                        $this->returnForm($player, $this->showSpecificPlayerStats(Utils::getPlayerName($data[0], true), null));
                    } else {
                        $player->sendMessage(Constants::PREFIX . "§cLe joueur " . $data[0] . " n'existe pas.");
                    }
                } else {
                    $player->sendMessage(Constants::PREFIX . "§cLe nom que vous avez spécifié est invalide.");
                }
            }
        });
        $form->setTitle("§l§q» §r§aStatistiques d'un joueur §l§q«§r");
        $form->addInput("§fNom du joueur :");
        return $form;
    }

    /**
     * @param Player $player
     * @param Form $form
     * @return void
     */
    private function returnForm(Player $player, Form $form): void {
        $player->sendForm($form);
    }

}
