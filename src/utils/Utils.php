<?php

namespace zenostats\utils;

use pocketmine\player\Player;

final class Utils {

    /**
     * @param array $array
     * @param int|null $page
     * @param int $separator
     * @return array
     */
    public static function arrayToPage(array $array, ?int $page, int $separator): array {
        $pageMax = ceil(count($array) / $separator);
        $min = ($page - 1) * $separator;
        return [$pageMax, array_slice($array, $min, $separator)];
    }

    /**
     * @param string|Player $player
     * @param bool $upperName
     * @return string
     */
    public static function getPlayerName(string|Player $player, bool $upperName): string {
        $name = $player instanceof Player ? $player->getName() : $player;
        return $upperName ? str_replace(" ", "_", $name) : str_replace("_", " ", $name);
    }

    /**
     * @param int $number
     * @return string
     */
    public static function formatNumber(int $number): string {
        $suffixes = [
            1000000 => 'M',
            1000 => 'k',
        ];
        foreach ($suffixes as $divisor => $suffix) {
            if ($number >= $divisor) {
                $formattedNumber = rtrim(number_format($number / $divisor, 2, thousands_separator: '.'), '0.');
                return $formattedNumber . $suffix;
            }
        }
        return number_format($number);
    }

}
