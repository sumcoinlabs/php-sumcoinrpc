<?php

namespace Denpa\Sumcoin\Tests;

use Denpa\Sumcoin;
use Denpa\Sumcoin\Exceptions\BadConfigurationException;
use Denpa\Sumcoin\Exceptions\Handler as ExceptionHandler;

class FunctionsTest extends TestCase
{
    /**
     * Test sigma to sum converter.
     *
     * @param int    $sigma
     * @param string $sumcoin
     *
     * @return void
     *
     * @dataProvider sigmaSumProvider
     */
    public function testToSum(int $sigma, string $sumcoin) : void
    {
        $this->assertEquals($sumcoin, Sumcoin\to_sumcoin($sigma));
    }

    /**
     * Test sumcoin to sigma converter.
     *
     * @param int    $sigma
     * @param string $sumcoin
     *
     * @return void
     *
     * @dataProvider sigmaSumProvider
     */
    public function testToSigma(int $sigma, string $sumcoin) : void
    {
        $this->assertEquals($sigma, Sumcoin\to_sigma($sumcoin));
    }

    /**
     * Test sumcoin to usum/sums converter.
     *
     * @param int    $usum
     * @param string $sumcoin
     *
     * @return void
     *
     * @dataProvider sumsSumProvider
     */
    public function testToBits(int $usum, string $sumcoin) : void
    {
        $this->assertEquals($usum, Sumcoin\to_usum($sumcoin));
    }

    /**
     * Test sumcoin to msum converter.
     *
     * @param float  $msum
     * @param string $sumcoin
     *
     * @return void
     *
     * @dataProvider msumSumProvider
     */
    public function testToMsum(float $msum, string $sumcoin) : void
    {
        $this->assertEquals($msum, Sumcoin\to_msum($sumcoin));
    }

    /**
     * Test float to fixed converter.
     *
     * @param float  $float
     * @param int    $precision
     * @param string $expected
     *
     * @return void
     *
     * @dataProvider floatProvider
     */
    public function testToFixed(
        float $float,
        int $precision,
        string $expected
    ) : void {
        $this->assertSame($expected, Sumcoin\to_fixed($float, $precision));
    }

    /**
     * Test url parser.
     *
     * @param string      $url
     * @param string      $scheme
     * @param string      $host
     * @param int|null    $port
     * @param string|null $user
     * @param string|null $password
     *
     * @return void
     *
     * @dataProvider urlProvider
     */
    public function testSplitUrl(
        string $url,
        string $scheme,
        string $host,
        ?int $port,
        ?string $user,
        ?string $pass
    ) : void {
        $parts = Sumcoin\split_url($url);

        $this->assertEquals($parts['scheme'], $scheme);
        $this->assertEquals($parts['host'], $host);
        foreach (['port', 'user', 'pass'] as $part) {
            if (!is_null(${$part})) {
                $this->assertEquals($parts[$part], ${$part});
            }
        }
    }

    /**
     * Test url parser with invalid url.
     *
     * @return array
     */
    public function testSplitUrlWithInvalidUrl() : void
    {
        $this->expectException(BadConfigurationException::class);
        $this->expectExceptionMessage('Invalid url');

        Sumcoin\split_url('cookies!');
    }

    /**
     * Test exception handler helper.
     *
     * @return void
     */
    public function testExceptionHandlerHelper() : void
    {
        $this->assertInstanceOf(ExceptionHandler::class, Sumcoin\exception());
    }

    /**
     * Provides url strings and parts.
     *
     * @return array
     */
    public function urlProvider() : array
    {
        return [
            ['https://localhost', 'https', 'localhost', null, null, null],
            ['https://localhost:8000', 'https', 'localhost', 8000, null, null],
            ['http://localhost', 'http', 'localhost', null, null, null],
            ['http://localhost:8000', 'http', 'localhost', 8000, null, null],
            ['http://testuser@127.0.0.1:8000/', 'http', '127.0.0.1', 8000, 'testuser', null],
            ['http://testuser:testpass@localhost:8000', 'http', 'localhost', 8000, 'testuser', 'testpass'],
        ];
    }

    /**
     * Provides sigma and sumcoin values.
     *
     * @return array
     */
    public function sigmaSumProvider() : array
    {
        return [
            [1000, '0.00001000'],
            [2500, '0.00002500'],
            [-1000, '-0.00001000'],
            [100000000, '1.00000000'],
            [150000000, '1.50000000'],
        ];
    }

    /**
     * Provides sigma and usum/sums values.
     *
     * @return array
     */
    public function sumsSumProvider() : array
    {
        return [
            [10, '0.00001000'],
            [25, '0.00002500'],
            [-10, '-0.00001000'],
            [1000000, '1.00000000'],
            [1500000, '1.50000000'],
        ];
    }

    /**
     * Provides sigma and msum values.
     *
     * @return array
     */
    public function msumSumProvider() : array
    {
        return [
            [0.01, '0.00001000'],
            [0.025, '0.00002500'],
            [-0.01, '-0.00001000'],
            [1000, '1.00000000'],
            [1500, '1.50000000'],
        ];
    }

    /**
     * Provides float values with precision and result.
     *
     * @return array
     */
    public function floatProvider() : array
    {
        return [
            [1.2345678910, 0, '1'],
            [1.2345678910, 2, '1.23'],
            [1.2345678910, 4, '1.2345'],
            [1.2345678910, 8, '1.23456789'],
        ];
    }
}
