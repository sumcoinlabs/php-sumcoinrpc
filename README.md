# Simple Bitcoin JSON-RPC client based on GuzzleHttp

[![Latest Stable Version](https://poser.pugx.org/denpa/php-bitcoinrpc/v/stable)](https://packagist.org/packages/denpa/php-bitcoinrpc)
[![License](https://poser.pugx.org/denpa/php-bitcoinrpc/license)](https://packagist.org/packages/denpa/php-bitcoinrpc)
[![Build Status](https://travis-ci.org/denpamusic/php-bitcoinrpc.svg)](https://travis-ci.org/denpamusic/php-bitcoinrpc)
[![Code Climate](https://codeclimate.com/github/denpamusic/php-bitcoinrpc/badges/gpa.svg)](https://codeclimate.com/github/denpamusic/php-bitcoinrpc)
[![Code Coverage](https://codeclimate.com/github/denpamusic/php-bitcoinrpc/badges/coverage.svg)](https://codeclimate.com/github/denpamusic/php-bitcoinrpc/coverage)
[![Join the chat at https://gitter.im/php-bitcoinrpc/Lobby](https://badges.gitter.im/php-bitcoinrpc/Lobby.svg)](https://gitter.im/php-bitcoinrpc/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Installation
Run ```php composer.phar require denpa/php-bitcoinrpc``` in your project directory or add following lines to composer.json
```javascript
"require": {
    "denpa/php-bitcoinrpc": "^2.1"
}
```
and run ```php composer.phar install```.

## Requirements
PHP 7.1 or higher  
_For PHP 5.6 and 7.0 use [php-bitcoinrpc v2.0.x](https://github.com/sumcoinlabs/php-sumcoinrpc/tree/2.0.x)._

## Usage
Create new object with url as parameter
```php
/**
 * Don't forget to include composer autoloader by uncommenting line below
 * if you're not already done it anywhere else in your project.
 **/
// require 'vendor/autoload.php';

use Denpa\Sumcoin\Client as SumcoinClient;

$sumcoind = new SumcoinClient('http://rpcuser:rpcpassword@localhost:3332/');
```
or use array to define your sumcoind settings
```php
/**
 * Don't forget to include composer autoloader by uncommenting line below
 * if you're not already done it anywhere else in your project.
 **/
// require 'vendor/autoload.php';

use Denpa\Sumcoin\Client as SumcoinClient;

$sumcoind = new SumcoinClient([
    'scheme'        => 'http',                 // optional, default http
    'host'          => 'localhost',            // optional, default localhost
    'port'          => 3332,                   // optional, default 3332
    'user'          => 'rpcuser',              // required
    'password'      => 'rpcpassword',          // required
    'ca'            => '/etc/ssl/ca-cert.pem'  // optional, for use with https scheme
    'preserve_case' => false,                  // optional, send method names as defined instead of lowercasing them
]);
```
Then call methods defined in [Sumcoin Core API Documentation](https://bitcoin.org/en/developer-reference#bitcoin-core-apis) with magic:
```php
/**
 * Get block info.
 */
$block = $sumcoind->getBlock('000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f');

$block('hash')->get();     // 000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f
$block['height'];          // 0 (array access)
$block->get('tx.0');       // 4a5e1e4baab89f3a32518a88c31bc87f618f76673e2cc77ab2127b7afdeda33b
$block->count('tx');       // 1
$block->has('version');    // key must exist and CAN NOT be null
$block->exists('version'); // key must exist and CAN be null
$block->contains(0);       // check if response contains value
$block->values();          // array of values
$block->keys();            // array of keys
$block->random(1, 'tx');   // random block txid
$block('tx')->random(2);   // two random block txid's
$block('tx')->first();     // txid of first transaction
$block('tx')->last();      // txid of last transaction

/**
 * Send transaction.
 */
$result = $sumcoind->sendToAddress('mmXgiR6KAhZCyQ8ndr2BCfEq1wNG2UnyG6', 0.1);
$txid = $result->get();

/**
 * Get transaction amount.
 */
$result = $sumcoind->listSinceBlock();
$bitcoin = $result->sum('transactions.*.amount');
$satoshi = \Denpa\Sumcoin\to_satoshi($sumcoin);
```
To send asynchronous request, add Async to method name:
```php
$sumcoind->getBlockAsync(
    '000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f',
    function ($response) {
        // success
    },
    function ($exception) {
        // error
    }
);
```

You can also send requests using request method:
```php
/**
 * Get block info.
 */
$block = $sumcoind->request('getBlock', '000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f');

$block('hash');            // 000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f
$block['height'];          // 0 (array access)
$block->get('tx.0');       // 4a5e1e4baab89f3a32518a88c31bc87f618f76673e2cc77ab2127b7afdeda33b
$block->count('tx');       // 1
$block->has('version');    // key must exist and CAN NOT be null
$block->exists('version'); // key must exist and CAN be null
$block->contains(0);       // check if response contains value
$block->values();          // get response values
$block->keys();            // get response keys
$block->first('tx');       // get txid of the first transaction
$block->last('tx');        // get txid of the last transaction
$block->random(1, 'tx');   // get random txid

/**
 * Send transaction.
 */
$result = $sumcoind->request('sendtoaddress', 'mmXgiR6KAhZCyQ8ndr2BCfEq1wNG2UnyG6', 0.06);
$txid = $result->get();

```
or requestAsync method for asynchronous calls:
```php
$sumcoind->requestAsync(
    'getBlock',
    '000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f',
    function ($response) {
        // success
    },
    function ($exception) {
        // error
    }
);
```

## Multi-Wallet RPC
You can use `wallet($name)` function to do a [Multi-Wallet RPC call](https://en.bitcoin.it/wiki/API_reference_(JSON-RPC)#Multi-wallet_RPC_calls):
```php
/**
 * Get wallet2.dat balance.
 */
$balance = $sumcoind->wallet('wallet2.dat')->getbalance();

echo $balance->get(); // 0.10000000
```

## Exceptions
* `Denpa\Sumcoin\Exceptions\BadConfigurationException` - thrown on bad client configuration.
* `Denpa\Sumcoin\Exceptions\BadRemoteCallException` - thrown on getting error message from daemon.
* `Denpa\Sumcoin\Exceptions\ConnectionException` - thrown on daemon connection errors (e. g. timeouts)


## Helpers
Package provides following helpers to assist with value handling.
#### `to_sumcoin()`
Converts value in sigma to sumcoin.
```php
echo Denpa\Sumcoin\to_sumcoin(100000); // 0.00100000
```
#### `to_satoshi()`
Converts value in sumcoin to sigma.
```php
echo Denpa\Sumcoin\to_sigma(0.001); // 100000
```
#### `to_ubtc()`
Converts value in sumcoin to usum/sums.
```php
echo Denpa\Sumcoin\to_usum(0.001); // 1000.0000
```
#### `to_msum()`
Converts value in sumcoin to msum.
```php
echo Denpa\Sumcoin\to_msum(0.001); // 1.0000
```
#### `to_fixed()`
Trims float value to precision without rounding.
```php
echo Denpa\Sumcoin\to_fixed(0.1236, 3); // 0.123
```

## License

This product is distributed under MIT license.



❤Thanks for your support!❤
