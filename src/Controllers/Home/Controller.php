<?php

namespace TANline\Controllers\Home;

use Doctrine\DBAL\Connection;
use Onyx\Traits;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

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

        return $this->render('home.twig');
    }

    public function dbCreate()
    {
        $dbTableStops = "CREATE TABLE IF NOT EXISTS stops (
                        stop_name VARCHAR(250) NOT NULL,
                        parent_station VARCHAR(250) NOT NULL,
                        stop_id VARCHAR(250) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8  COLLATE=utf8_unicode_ci";
        $this->db->query($dbTableStops);
        $dbTableStopTimes = "CREATE TABLE IF NOT EXISTS stop_times (
                        trip_id VARCHAR(250) NOT NULL,
                        stop_id VARCHAR(50) NOT NULL,
                        departure_time TIME NOT NULL,
                        stop_sequence INT(11) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8  COLLATE=utf8_unicode_ci";
        $this->db->query($dbTableStopTimes);

        return $this->render('db.create.twig');
    }

    public function dbData()
    {
        $this->dbDataFile('stops', 'stops_light_l1');
        $this->dbDataFile('stop_times', 'stop_times_l1_FM2BJ');

        return $this->render('db.data.twig');
    }

    public function dbDataFile($table, $file)
    {
        $countLinesTable = $this->dbCountLinesTable($table);

        if ($countLinesTable == 0)
        {
            /* $dbTruncateTable = "TRUNCATE " . $table;
            $this->db->query($dbTruncateTable); */

            $linesCSV = $this->dbDataParseFile($file);

            foreach ($linesCSV as $lineCSV) {
                if ($lineCSV != null)
                {
                    $fieldsLineCSV = explode(',', $lineCSV);
                    $countFieldsCSV = count($fieldsLineCSV);
                    $dbInsertData = "INSERT INTO " . $table . " VALUES ('";
                    for ($i = 0; $i < $countFieldsCSV; $i++)
                    {
                        ($i === ($countFieldsCSV - 1)) ? $dbInsertData .= $fieldsLineCSV[$i] . "'" : $dbInsertData .= $fieldsLineCSV[$i] . "', '";
                    }
                    $dbInsertData .= ")";
                    $this->db->query($dbInsertData);

                    //$dbInsertData = "INSERT INTO " . $table . " VALUES ('" . $fieldsLineCSV[0] . "', '" . $fieldsLineCSV[1] . "', '" . $fieldsLineCSV[2] . "', '" . $fieldsLineCSV[3] . "')";
                }
            }

            /* foreach ($linesCSV as $lineCSV) {
                $fieldsLineCSV = explode(',', $lineCSV);
                $qb = $this->db->createQueryBuilder();
                $qb->insert($table);
                $qb->values($fieldsLineCSV);
                $qb->execute();
            } */
        }

    }

    public function dbDataParseFile($file)
    {
        $fileCSV = file_get_contents('csv/' . $file . '.csv');
        $linesCSV = explode(PHP_EOL, $fileCSV);

        return $linesCSV;
    }

    public function dbCountLinesTable($table)
    {
        $qb = $this->db->createQueryBuilder();
        $qb->select('COUNT(*)');
        $qb->from($table);
        $dbCountLinesTable = $this->db->fetchAssoc($qb);

        /* $qb = $this->db->createQueryBuilder();
        $bidule = $qb->select('stop_name')
            ->from($table)
            ->execute();
        dump($bidule);
        foreach ($bidule as $truc) {
            dump($truc);
        } */

        return $dbCountLinesTable['COUNT(*)'];
    }

    public function checkStops()
    {
       $qb = $this->db->createQueryBuilder();
       $stops = $qb->select('stop_name')
           ->from('stops')
           ->groupBy('stop_name')
           ->orderBy('stop_name')
           ->execute();

    }
}
