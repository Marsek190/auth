<?php

namespace Common\Service\User\Auth;

use Common\Models\LogicLayer\User\Exceptions\AuthorizationException;

/**
 * настоящий класс инкапсулирует в себе логику (метод handle) для обработки неверных попыток введения смс-кода
 */
class WrongAttemptsHandler
{
    private $attemptsCounter;
    private $lockedTimeoutCalculator;
    private $authExceptionManager;
    private $attemptsGuard;

    /**
     * @param AttemptsCounter $attemptsCounter
     * @param PhoneLockedTimeoutCalculator $lockedTimeoutCalculator
     * @param AuthExceptionManager $authExceptionManager
     * @param AttemptsGuard $attemptsGuard
     */
    public function __construct(
        AttemptsCounter $attemptsCounter,
        PhoneLockedTimeoutCalculator $lockedTimeoutCalculator,
        AuthExceptionManager $authExceptionManager,
        AttemptsGuard $attemptsGuard
    ) {
        $this->attemptsCounter = $attemptsCounter;
        $this->lockedTimeoutCalculator = $lockedTimeoutCalculator;
        $this->authExceptionManager = $authExceptionManager;
        $this->attemptsGuard = $attemptsGuard;
    }

    /**
     * @return void
     * @throws AuthorizationException
     */
    public function handle()
    {
        if ($this->attemptsGuard->isPhoneBanned()) {
            $this->authExceptionManager->throwPhoneIsBanned();
        }

        if (!$this->attemptsCounter->hasAttempts()) {
            $timeout = $this->lockedTimeoutCalculator->getTimeoutUntilNewSms();

            $this->authExceptionManager->throwPhoneIsLocked($timeout);
        }

        $attemptsLeft = $this->attemptsCounter->addWrongAttempt();

        $this->authExceptionManager->throwInvalidCode($attemptsLeft);
    }
}
