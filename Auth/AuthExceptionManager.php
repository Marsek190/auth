<?php

namespace Common\Service\User\Auth;

use Common\Models\LogicLayer\User\Exceptions\AuthorizationException;

class AuthExceptionManager
{
    /**
     * @param int $attemptsLeft
     * @throws AuthorizationException
     */
    public function throwInvalidCode($attemptsLeft)
    {
        $error = sprintf(
            'Неверный код. %s %s %s',
            $this->pluralize($attemptsLeft, ['Осталась', 'Осталось', 'Осталось']),
            $attemptsLeft,
            $this->pluralize($attemptsLeft, ['попытка', 'попытки', 'попыток'])
        );

        throw new AuthorizationException($error, 100);
    }

    /**
     * @param PhoneLockedTimeout $lockedTimeout
     * @throws AuthorizationException
     */
    public function throwPhoneIsLocked(PhoneLockedTimeout $lockedTimeout)
    {
        $minutesLeftPhrase = $this->pluralize($lockedTimeout->minutes(), ['минуту', 'минуты', 'минут']);
        $secondsLeftPhrase = $this->pluralize($lockedTimeout->seconds(), ['секунду', 'секунды', 'секунд']);

        $error = 'Следующий код вы сможете получить через ';

        if ($lockedTimeout->minutes() > 0) {
            $error .= sprintf('%s %s ', $lockedTimeout->minutes(), $minutesLeftPhrase);
        }

        if ($lockedTimeout->seconds() > 0) {
            $error .= sprintf('%s %s', $lockedTimeout->seconds(), $secondsLeftPhrase);
        }

        throw new AuthorizationException(trim($error), 100);
    }

    /**
     * @param int $number
     * @param array $endings
     * @return string
     */
    private function pluralize($number, array $endings)
    {
        $cases = [2, 0, 1, 1, 1, 2];

        return sprintf($endings[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]], $number);
    }
}
