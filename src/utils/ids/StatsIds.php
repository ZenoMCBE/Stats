<?php

namespace zenostats\utils\ids;

interface StatsIds {

    public const PLAYED = "played";
    public const WIN = "win";
    public const LOSE = "lose";
    public const SCORE = "score";
    public const KILL = "kill";
    public const ASSIST = "assist";
    public const DEATH = "death";
    public const KILLSTREAK = "killstreak";
    public const BEST_KILLSTREAK = "best-killstreak";
    public const ARROW_SHOT = "arrow-shot";
    public const ARROW_HIT = "arrow-hit";
    public const ARROW_BOOST = "arrow-boost";
    public const DAMAGE_DEALED = "damage-dealed";
    public const DAMAGE_TAKEN = "damage-taken";
    public const GOLDEN_APPLE_EATEN = "golden-apple-eaten";
    public const CRIT = "crit";

    public const ALL_STATS = [
        self::PLAYED,
        self::WIN,
        self::LOSE,
        self::SCORE,
        self::KILL,
        self::ASSIST,
        self::DEATH,
        self::KILLSTREAK,
        self::BEST_KILLSTREAK,
        self::ARROW_SHOT,
        self::ARROW_HIT,
        self::ARROW_BOOST,
        self::DAMAGE_DEALED,
        self::DAMAGE_TAKEN,
        self::GOLDEN_APPLE_EATEN,
        self::CRIT
    ];
}
