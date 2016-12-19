<?php

namespace TANline\Controllers\Home;

use Doctrine\DBAL\Connection;
use Onyx\Traits;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use TANline\Entities\Arret;

class Controller
{
    use
        Traits\RequestAware,
        Traits\TwigAware,
        LoggerAwareTrait;

    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->logger = new NullLogger();
    }

    public function homeAction(): Response
    {
        echo "accueil";

        return $this->render('home.twig');
    }

    public function checkStops()
    {
        $qb = $this->db->createQueryBuilder()
            ->select('*')
            ->from('stops')
            ->groupBy('stop_name')
            ->orderBy('stop_name', 'ASC');
        $stops = $this->db->fetchAll($qb);

        return $this->render('stops.twig', ['tests' => $stops]);
    }

}
