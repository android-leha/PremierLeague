<?php

namespace Test\MyBundle\Model;


class Team implements \JsonSerializable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $power;

    /**
     * @var Score
     */
    private $score;

    public static function playGame(Team $firstTeam, Team $secondTeam)
    {
        $firstScore = floor(pow(rand(0, 5), $firstTeam->power / $secondTeam->power));
        $secondScore = floor(pow(rand(0, 5), $secondTeam->power / $firstTeam->power));
        if ($firstScore > $secondScore) {
            $firstTeam->getScore()->pts += 3;
            $firstTeam->getScore()->w++;
            $secondTeam->getScore()->l++;
        } elseif($firstScore < $secondScore) {
            $secondTeam->getScore()->pts += 3;
            $secondTeam->getScore()->w++;
            $firstTeam->getScore()->l++;
        } else {
            $firstTeam->getScore()->pts++;
            $secondTeam->getScore()->pts++;
            $firstTeam->getScore()->d++;
            $secondTeam->getScore()->d++;
        }
        $firstTeam->getScore()->p++;
        $firstTeam->getScore()->gf += $firstScore;
        $firstTeam->getScore()->ga += $secondScore;
        return [
            'firstTeam' => $firstTeam->__toString(),
            'secondTeam' => $secondTeam->__toString(),
            'firstScore' => $firstScore,
            'secondScore' => $secondScore,
        ];
    }

    public function __construct($name, $power)
    {
        $this->name = $name;
        $this->power = $power;
        $this->score = new Score();
    }

    /**
     * @return Score
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }


    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'pts' => $this->score->pts,
            'p' => $this->score->p,
            'w' => $this->score->w,
            'd' => $this->score->d,
            'l' => $this->score->l,
            'gd' => $this->score->gd,
        ];
    }
}