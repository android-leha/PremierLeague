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
        $premiereLeague = new League(
            [
                new Team("Manchester City", 1.79),
                new Team("Chelsea", 1.83),
                new Team("Arsenal", 1.75),
                new Team("Liverpool", 1.65),
            ]
        );
        $this->get("session")->set("league", $premiereLeague);
        $this->get("session")->save();
        return array();
    }

    /**
     * @Config\Route("/load", name="load")
     *
     * @return JsonResponse
     */
    public function loadData()
    {
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
     * @Config\Route("/all", name="all")
     *
     * return JsonResponse
     */
    public function allAction()
    {
        $results = array();
        try {
            $premiereLeague = $this->getLeague();
            while (true) {
                $results[] = $premiereLeague->playWeek();
            }
        } catch (\Exception $e) {
        }
        return new JsonResponse(
            [
                'weeks' => $results,
            ]
        );
    }

    /**
     * @Config\Route("/predictions", name="predictions")
     *
     * return JsonResponse
     */
    public function predictionsAction()
    {
        $premiereLeague = $this->getLeague();
        return new JsonResponse($premiereLeague->getPredictions());
    }

    /**
     * @return League
     */
    private function getLeague()
    {
        return $this->get("session")->get("league");
    }

} 