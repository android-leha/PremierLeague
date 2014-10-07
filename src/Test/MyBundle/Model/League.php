<?php

namespace Test\MyBundle\Model;


class League
{
    private $teams = array();

    private $gamesByWeeks = array();

    private $weekNumber = 0;

    /**
     * @param Team[] $teams
     */
    public function __construct(array $teams)
    {
        $this->teams = $teams;

        $teamsCount = count($teams);
        $gamesCount = $teamsCount * ($teamsCount - 1);
        $round = 0;
        for ($i = 0; $i < $gamesCount; $i++) {
            if (($i % $teamsCount) == 0) {
                $round++;
            }
            $k = $i + $round;
            $week = (string) (floor($i / 2) + 1);
            $firstTeam = $teams[$i % $teamsCount];
            $secondTeam = $teams[$k % $teamsCount];
            $this->gamesByWeeks[$week][] = array($firstTeam, $secondTeam);
        }
    }

    /**
     * return array
     * @throws \Exception
     */
    public function playWeek()
    {
        $this->weekNumber++;
        if (isset($this->gamesByWeeks[$this->weekNumber])) {
            $results = [];
            foreach ($this->gamesByWeeks[$this->weekNumber] as $week) {
                $results[] = Team::playGame($week[0], $week[1]);
            }
            return $results;
        } else {
            throw new \Exception("League ended");
        }
    }

    public function getResultTable()
    {
        usort(
            $this->teams,
            function (Team $a, Team $b) {
                $aScore = $a->getScore();
                $bScore = $b->getScore();
//                echo 's ' . $aScore->pts . ' ' . $bScore->pts;
                if ($aScore->pts < $bScore->pts) {
//                    echo 'Big' . PHP_EOL;
                    return 1;
                } elseif ($aScore->pts > $bScore->pts) {
                    return -1;
                } else {
                    if ($aScore->gd < $bScore->gd) {
                        return 1;
                    } elseif ($aScore->gd > $bScore->gd) {
                        return -1;
                    } else {
                        if ($aScore->gf < $bScore->gf) {
                            return 1;
                        } elseif ($aScore->gf > $bScore->gf) {
                            return -1;
                        } else {
                            return 0;
                        }
                    }
                }
            });
        return $this->teams;
    }

    public function getWeek()
    {
        return $this->weekNumber;
    }
} 