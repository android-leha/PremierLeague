<?php

namespace Test\MyBundle\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\JsonResponse;
use Test\MyBundle\Model\League;
use Test\MyBundle\Model\Team;

class IndexController extends Controller
{
    /**
     * @Config\Route("/", name="home")
     * @Config\Template()
     *
     * return array
     */
    public function indexAction()
    {
        $premiereLeague = new League([
                new Team("Manchester City", 1.79),
                new Team("Chelsea", 1.88),
                new Team("Arsenal", 1.75),
                new Team("Liverpool", 1.6),
            ]);
        $this->get("session")->set("league", $premiereLeague);
        $this->get("session")->save();
        return array();
    }

    /**
     * @Config\Route("/load", name="load")
     *
     * @return JsonResponse
     */
    public function loadData() {
        $premiereLeague = $this->getLeague();

        return new JsonResponse($premiereLeague->getResultTable());
    }

    /**
     * @Config\Route("/play", name="play")
     *
     * return JsonResponse
     */
    public function playAction()
    {
        try {
            $premiereLeague = $this->getLeague();
            $results = $premiereLeague->playWeek();
            return new JsonResponse(
                [
                    'week' => $premiereLeague->getWeek(),
                    'games' => $results,
                ]
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()]);
        }
    }

    /**
     * @return League
     */
    private function getLeague()
    {
        return $this->get("session")->get("league");
    }

} 