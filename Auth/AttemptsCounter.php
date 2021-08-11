<?php

namespace Common\Service\User\Auth;

use Date;
use Phalcon\Session\AdapterInterface;

class AttemptsCounter
{
    private $wrongAttemptsLimit = 3;
    private $sessionWrongAuthAttemptsKey = 'USER.WRONG_AUTH_ATTEMPTS';

    private $session;

    /**
     * @param AdapterInterface $session
     */
    public function __construct(AdapterInterface $session)
    {
        $this->session = $session;
    }

    /** @return bool */
    public function hasAttempts()
    {
        $attemptsLeft = $this->getAttemptsLeft();

        return $attemptsLeft > 1;
    }

    /** @return int */
    public function addWrongAttempt()
    {
        $wrongAttempts = $this->getAttemptsLeft() - 1;
        $this->session->set($this->sessionWrongAuthAttemptsKey, $wrongAttempts);
        
        return $wrongAttempts;
    }

    /** @return int */
    public function getAttemptsLeft()
    {
        if (!$this->session->has($this->sessionWrongAuthAttemptsKey)) {
            $this->session->set($this->sessionWrongAuthAttemptsKey, $this->wrongAttemptsLimit);
        }

        return $this->session->get($this->sessionWrongAuthAttemptsKey);
    }
}
