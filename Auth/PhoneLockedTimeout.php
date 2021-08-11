<?php

namespace Common\Service\User\Auth;

class PhoneLockedTimeout
{
    private $minutes;
    private $seconds;

    /**
     * @param int $minutes
     * @param int $seconds
     */
    public function __construct($minutes, $seconds)
    {
        $this->minutes = $minutes;
        $this->seconds = $seconds;
    }

    /** @return int */
    public function minutes()
    {
        return $this->minutes;
    }

    /** @return int */
    public function seconds()
    {
        return $this->seconds;
    }
}
