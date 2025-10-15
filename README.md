# CacheExchange

A collection of PHP interfaces and adapters to make swapping out cache library dependencies quick and easy.

## Requirements

* __For Memcached adapter__: [PHP Memcached extension](https://www.php.net/manual/en/memcached.setup.php)
* __For Redis adapter__: [Redis](https://redis.io/) and [Predis](https://github.com/predis/predis)

## Running tests

1. Start the Docker container.
    ```bash
    composer run up
    ```
1. Run tests.
    ```bash
    composer run test
    ```
1. When you are done testing, stop the container.
    ```bash
    composer run down
    ```
