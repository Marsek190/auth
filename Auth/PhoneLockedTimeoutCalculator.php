<?php

namespace Common\Service\User\Auth;

use Exception;
use DateTimeImmutable as DateTime;
use Phalcon\Session\AdapterInterface;

class PhoneLockedTimeoutCalculator
{
    private $minutesLeft = 2;
    private $secondsLeft = 50;
    private $sessionNewSmsTimeoutKey = 'USER.SMS_TIMEOUT_UNTIL_NEW_SMS_CODE';

    private $session;

    /**
     * @param AdapterInterface $session
     */
    public function __construct(AdapterInterface $session)
    {
        $this->session = $session;
    }

    /** @return PhoneLockedTimeout */
    public function getTimeoutUntilNewSms()
    {
        if (!$this->hasSetTimeoutUntilNewSms()) {
            $this->addTimeoutUntilNewSms();
        }

        /** @var DateTime $timeLeft */
        $timeLeft = $this->session->get($this->sessionNewSmsTimeoutKey);
        $now = new DateTime();
        $interval = $timeLeft->diff($now);

        return new PhoneLockedTimeout($interval->i, $interval->s);
    }
    
    /** @return void */
    private function addTimeoutUntilNewSms()
    {
        try {
            $timeLeft = new DateTime(sprintf('now +%s min %s sec', $this->minutesLeft, $this->secondsLeft));
            $this->session->set($this->sessionNewSmsTimeoutKey, $timeLeft);
        } catch (Exception $e) {
        }
    }

    /** @return bool */
    private function hasSetTimeoutUntilNewSms()
    {
        if (!$this->session->has($this->sessionNewSmsTimeoutKey)) {
            return false;
        }

        /** @var DateTime $timeLeft */
        $timeLeft = $this->session->get($this->sessionNewSmsTimeoutKey);
        $now = new DateTime();

        return $timeLeft > $now;
    }
}
