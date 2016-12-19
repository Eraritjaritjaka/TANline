<?php

namespace TANline\Entities;

class Arret
{
    private $stop_name;
    private $parent_station;
    private $stop_id;

    /**
     * @param array $stops
     */
    public function hydrate(array $stop)
    {
        foreach ($stop as $key => $data)
        {
            $method = 'set' . implode(array_map('ucfirst', explode('_', $key)));
            if (method_exists($this, $method))
            {
                $this->$method($data);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getStopName()
    {
        return $this->stop_name;
    }

    /**
     * @param mixed $stop_name
     */
    public function setStopName($stop_name)
    {
        $this->stop_name = $stop_name;
    }

    /**
     * @return mixed
     */
    public function getParentStation()
    {
        return $this->parent_station;
    }

    /**
     * @param mixed $parent_station
     */
    public function setParentStation($parent_station)
    {
        $this->parent_station = $parent_station;
    }

    /**
     * @return mixed
     */
    public function getStopId()
    {
        return $this->stop_id;
    }

    /**
     * @param mixed $stop_id
     */
    public function setStopId($stop_id)
    {
        $this->stop_id = $stop_id;
    }



}