<?php

declare(strict_types=1);

namespace Denpa\Bitcoin;

use Denpa\Bitcoin\Exceptions\BadConfigurationException;
use Denpa\Bitcoin\Exceptions\Handler as ExceptionHandler;

if (!function_exists('to_bitcoin')) {
    /**
     * Converts from satoshi to bitcoin.
     *
     * @param int $satoshi
     *
     * @return string
     */
    function to_sumcoin(int $sigma) : string
    {
        return bcdiv((string) $sigma, (string) 1e8, 8);
    }
}

if (!function_exists('to_sigma')) {
    /**
     * Converts from sumcoin to sigma.
     *
     * @param string|float $sumcoin
     *
     * @return string
     */
    function to_sigma($sumcoin) : string
    {
        return bcmul(to_fixed((float) $sumcoin, 8), (string) 1e8);
    }
}

if (!function_exists('to_usum')) {
    /**
     * Converts from sumcoin to usum/sums.
     *
     * @param string|float $sumcoin
     *
     * @return string
     */
    function to_usum($sumcoin) : string
    {
        return bcmul(to_fixed((float) $bitcoin, 8), (string) 1e6, 4);
    }
}

if (!function_exists('to_msum')) {
    /**
     * Converts from sumcoin to msum.
     *
     * @param string|float $sumcoin
     *
     * @return string
     */
    function to_msum($sumcoin) : string
    {
        return bcmul(to_fixed((float) $sumcoin, 8), (string) 1e3, 4);
    }
}

if (!function_exists('to_fixed')) {
    /**
     * Brings number to fixed precision without rounding.
     *
     * @param float $number
     * @param int   $precision
     *
     * @return string
     */
    function to_fixed(float $number, int $precision = 8) : string
    {
        $number = $number * pow(10, $precision);

        return bcdiv((string) $number, (string) pow(10, $precision), $precision);
    }
}

if (!function_exists('split_url')) {
    /**
     * Splits url into parts.
     *
     * @param string $url
     *
     * @return array
     */
    function split_url(string $url) : array
    {
        $allowed = ['scheme', 'host', 'port', 'user', 'pass'];

        $parts = (array) parse_url($url);
        $parts = array_intersect_key($parts, array_flip($allowed));

        if (!$parts || empty($parts)) {
            throw new BadConfigurationException(
                ['url' => $url],
                'Invalid url'
            );
        }

        return $parts;
    }
}

if (!function_exists('exception')) {
    /**
     * Gets exception handler instance.
     *
     * @return \Denpa\Bitcoin\Exceptions\Handler
     */
    function exception() : ExceptionHandler
    {
        return ExceptionHandler::getInstance();
    }
}

set_exception_handler([ExceptionHandler::getInstance(), 'handle']);
