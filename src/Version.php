<?php

namespace Origami\Api;

use Carbon\Carbon;

class Version
{
    public $date;

    public $major = 1;

    public function __construct($date)
    {
        $this->date = $this->getDate($date);
    }

    public function gt($version)
    {
        return $this->date->gt($this->getDate($version));
    }

    public function gte($version)
    {
        return $this->date->gte($this->getDate($version));
    }

    public function lt($version)
    {
        return $this->date->lt($this->getDate($version));
    }

    public function lte($version)
    {
        return $this->date->lte($this->getDate($version));
    }

    public function eq($version)
    {
        return $this->date->eq($this->getDate($version));
    }

    private function getDate($date)
    {
        if ($date instanceof Version) {
            return $date->date;
        }

        return Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
    }
}
