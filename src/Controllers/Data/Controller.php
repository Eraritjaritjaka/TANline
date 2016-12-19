<?php

namespace TANline\Controllers\Data;

use Doctrine\DBAL\Connection;
use Onyx\Traits;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use TANline\Entities\Arret;
use TANline\Entities\Horaire;

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

    public function indexAction(): Response
    {
        $this->dbCreate();
        if ($this->dbCountLinesTable('stops') === '0')
        {
            $rows = $this->extractRowsFile('stops_light_l1');
            $stops = $this->convertRowsToObjects($rows, 'TANline\Entities\Arret');
            $this->injectStops($stops);
        }
        if ($this->dbCountLinesTable('stop_times') === '0')
        {
            $rows = $this->extractRowsFile('stop_times_l1_FM2BJ');
            $stop_times = $this->convertRowsToObjects($rows, 'TANline\Entities\Horaire');
            $this->injectStopTimes($stop_times);
        }

        return $this->render('home.twig');
    }

    public function dbCreate()
    {
        $dbTableStops = "CREATE TABLE IF NOT EXISTS stops (
                        id INT AUTO_INCREMENT NOT NULL,
                        stop_name VARCHAR(250) NOT NULL,
                        parent_station VARCHAR(250) NOT NULL,
                        stop_id VARCHAR(250) NOT NULL,
                        PRIMARY KEY (id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8  COLLATE=utf8_unicode_ci";
        $this->db->query($dbTableStops);
        $dbTableStopTimes = "CREATE TABLE IF NOT EXISTS stop_times (
                        id INT AUTO_INCREMENT NOT NULL,
                        trip_id VARCHAR(250) NOT NULL,
                        stop_id VARCHAR(50) NOT NULL,
                        departure_time TIME NOT NULL,
                        stop_sequence INT(11) NOT NULL,
                        PRIMARY KEY (id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8  COLLATE=utf8_unicode_ci";
        $this->db->query($dbTableStopTimes);
    }

    public function dbCountLinesTable($table)
    {
        $qb = $this->db->createQueryBuilder();
        $qb->select('COUNT(*)');
        $qb->from($table);
        $dbCountLinesTable = $this->db->fetchAssoc($qb);

        return $dbCountLinesTable['COUNT(*)'];
    }

    private function extractRowsFile($file)
    {
        $rows = [];
        $header = null;
        $file = fopen("csv/" . $file . ".csv", "r");
        while ($row = fgetcsv($file)) {
            if ($header === null) {
                $header = $row;
                continue;
            }
            $rows[] = array_combine($header, $row);
        }

        return $rows;
    }

    private function convertRowsToObjects($lines, $class)
    {
        $stops = [];
        foreach ($lines as $line)
        {
            $stop = new $class();
            $stop->hydrate($line);
            $stops[] = $stop;
        }

        return $stops;
    }

    private function injectStops($arrets)
    {
        foreach ($arrets as $arret)
        {
            $injectData = "INSERT INTO stops (stop_name, parent_station, stop_id) VALUES (";
            $injectData .= "'" . $arret->getStopName() . "', ";
            $injectData .= "'" . $arret->getParentStation() . "', ";
            $injectData .= "'" . $arret->getStopId() . "')";
            $this->db->query($injectData);
        }
    }

    private function injectStopTimes($horaires)
    {
        foreach ($horaires as $horaire)
        {
            $injectData = "INSERT INTO stop_times (trip_id, stop_id, departure_time, stop_sequence) VALUES (";
            $injectData .= "'" . $horaire->getTripId() . "', ";
            $injectData .= "'" . $horaire->getStopId() . "', ";
            $injectData .= "'" . $horaire->getDepartureTime() . "', ";
            $injectData .= "'" . $horaire->getStopSequence() . "')";
            $this->db->query($injectData);
        }
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

}
