<?php

namespace TANline\Entities;

class Horaire
{
    private $trip_id;
    private $stop_id;
    private $departure_time;
    private $stop_sequence;

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
    public function getTripId()
    {
        return $this->trip_id;
    }

    /**
     * @param mixed $trip_id
     */
    public function setTripId($trip_id)
    {
        $this->trip_id = $trip_id;
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

    /**
     * @return mixed
     */
    public function getDepartureTime()
    {
        return $this->departure_time;
    }

    /**
     * @param mixed $departure_time
     */
    public function setDepartureTime($departure_time)
    {
        $this->departure_time = $departure_time;
    }

    /**
     * @return mixed
     */
    public function getStopSequence()
    {
        return $this->stop_sequence;
    }

    /**
     * @param mixed $stop_sequence
     */
    public function setStopSequence($stop_sequence)
    {
        $this->stop_sequence = $stop_sequence;
    }

}