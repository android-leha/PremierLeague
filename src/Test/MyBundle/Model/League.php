<?php

namespace Test\MyBundle\Model;


class League
{
    private $teams = array();

    private $gamesByWeeks = array();

    private $weekNumber = 0;
    private $gamesCount;

    /**
     * @param Team[] $teams
     */
    public function __construct(array $teams)
    {
        $this->teams = $teams;

        // Round-Robin Tournament
        $teamsCount = count($teams);
        if ($teamsCount % 2 > 0) {
            $teamsCount++;
            $teams[] = new EmptyTeam();
        }
        $K = $teamsCount / 2;


        $this->gamesCount = $teamsCount * ($teamsCount - 1) / 2;
        $weeksCount = $this->gamesCount / $K;

        for ($j = 0; $j < $weeksCount; $j++) {
            $weekMatrix = array();
            $row = $this->row($j, $teamsCount);
            $weekMatrix[0][1] = $teams[0];
            $inverseWeekMatrix[0][0] = $weekMatrix[0][1];
            for ($i = 0; $i < $teamsCount; $i++) {
                if ($i === 0) {
                    continue;
                } elseif ($i < $K) {
                    $weekMatrix[$i][1] = $teams[array_shift($row)];
                    $inverseWeekMatrix[$i][0] = $weekMatrix[$i][1];
                } else {
                    $weekMatrix[$teamsCount - $i - 1][0] = $teams[array_shift($row)];
                    $inverseWeekMatrix[$teamsCount - $i - 1][1] = $weekMatrix[$teamsCount - $i - 1][0];
                }
            }
            $this->gamesByWeeks[] = $weekMatrix;
            $this->gamesByWeeks[] = $inverseWeekMatrix;
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
                if ($aScore->pts < $bScore->pts) {
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

    /**
     * @param $j
     * @param $teamsCount
     * @return array
     */
    private function row($j, $teamsCount)
    {
        $row = array();
        for ($i = 1; $i < $teamsCount; $i++) {
            $row[] = $i;
        }
        for ($k = 0; $k < $j; $k++) {
            $last = array_pop($row);
            $row = array_merge([$last], $row);
        }
        return $row;
    }

    public function getPredictions()
    {
        $results = $this->getResultTable();
        $attack = 0;
        $defence = 0;
        foreach ($results as $team) {
            $attack += $team->getAttackPower();
            $defence += $team->getDefencePower();
        }

        $out = array();
        foreach ($results as $team) {

            $out[] = array(
                'name' => (string) $team,
                'prediction' => (round(
                        ($team->getAttackPower() / $attack - $team->getDefencePower() / $defence) * 100)
                    + 100 / count($this->teams)) . '%',
            );
        }

        return $out;
    }
} 