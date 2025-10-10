<?php

namespace CacheExchange;

use CacheExchange\Adapters\Memcached;
use CacheExchange\Adapters\Redis;
use CacheExchange\Cache;
use PHPUnit\Framework\TestCase;
use CacheExchange\mocks\AdapterMock;

class CacheTest extends TestCase
{
  public function testStringKeymaker()
  {
    $cache = new Cache(new AdapterMock());

    $cache->set('key1', 'value1');
    $cache->set('key2', 'value2');

    $this->assertEquals($cache->getKeys(), ['key1', 'key2']);

    $this->assertEquals($cache->get('key1'), 'value1');
    $this->assertTrue($cache->exists('key1'));

    $this->assertEquals($cache->get('key2'), 'value2');
    $this->assertTrue($cache->exists('key2'));

    $this->assertTrue($cache->delete('key1'));
    $this->assertEquals($cache->getKeys(), ['key2']);

    $this->assertTrue($cache->clear());
    $this->assertEquals($cache->getKeys(), []);
  }

  public function testArrayKeymaker()
  {
    $cache = new Cache(new AdapterMock(), 'array');

    $keyData1 = ['one' => 1, 'two' => 2];
    $compiledKey1 = 'one=1&two=2';

    $keyData2 = ['three' => 3, 'four' => 4];
    $compiledKey2 = 'four=4&three=3';

    $keyData3 = 'nokeymaker';
    $compiledKey3 = 'nokeymaker';

    $cache->set($keyData1, 'value1');
    $cache->set($keyData2, 'value2');
    $cache->set($keyData3, 'value3', 0, false);

    $this->assertEquals($cache->getKeys(), [$compiledKey1, $compiledKey2, $compiledKey3]);

    $this->assertEquals($cache->get($keyData1), 'value1');
    $this->assertEquals($cache->get($compiledKey1, false), 'value1');
    $this->assertTrue($cache->exists($keyData1));
    $this->assertTrue($cache->exists($compiledKey1, false));

    $this->assertEquals($cache->get($keyData2), 'value2');
    $this->assertEquals($cache->get($compiledKey2, false), 'value2');
    $this->assertTrue($cache->exists($keyData2));
    $this->assertTrue($cache->exists($compiledKey2, false));

    $this->assertFalse($cache->get($keyData3));
    $this->assertEquals($cache->get($compiledKey3, false), 'value3');
    $this->assertFalse($cache->exists($keyData3));
    $this->assertTrue($cache->exists($keyData3, false));

    $this->assertTrue($cache->delete($keyData1));
    $this->assertEquals($cache->getKeys(), [$compiledKey2, $compiledKey3]);

    $this->assertTrue($cache->clear());
    $this->assertEquals($cache->getKeys(), []);
  }

  public function testCustomKeymaker()
  {
    $cache = new Cache(new AdapterMock(), fn ($key) => "prepend-$key");

    $cache->set('key1', 'value1');
    $cache->set('key2', 'value2');

    $this->assertEquals($cache->getKeys(), ['prepend-key1', 'prepend-key2']);

    $this->assertEquals($cache->get('key1'), 'value1');
    $this->assertTrue($cache->exists('key1'));

    $this->assertEquals($cache->get('key2'), 'value2');
    $this->assertTrue($cache->exists('key2'));

    $this->assertTrue($cache->delete('key1'));
    $this->assertEquals($cache->getKeys(), ['prepend-key2']);

    $this->assertTrue($cache->clear());
    $this->assertEquals($cache->getKeys(), []);
  }

  public function testMemcachedAdapter()
  {
    $memcached = new Memcached([
      ['cache-exchange-memcached', 11211]
    ]);

    $cache = new Cache($memcached);

    $cache->clear();
    // $this->assertEmpty($cache->getKeys()); // unreliable

    $this->assertTrue($cache->set('cache-exchange-test-key', 'test-data'));
    $this->assertEquals('test-data', $cache->get('cache-exchange-test-key'));
    // $this->assertContains('cache-exchange-test-key', $cache->getKeys()); // unreliable
    $this->assertTrue($cache->exists('cache-exchange-test-key'));
    $this->assertTrue($cache->delete('cache-exchange-test-key'));
    $this->assertFalse($cache->exists('cache-exchange-test-key'));

    $this->assertTrue($cache->set('cache-exchange-test-key-2', 'test-data-2', 500));
    $this->assertEquals('test-data-2', $cache->get('cache-exchange-test-key-2'));
    // $this->assertContains('cache-exchange-test-key-2', $cache->getKeys()); // unreliable
    $this->assertTrue($cache->exists('cache-exchange-test-key-2'));
    $this->assertTrue($cache->delete('cache-exchange-test-key-2'));
    $this->assertFalse($cache->exists('cache-exchange-test-key-2'));

    // $this->assertEmpty($cache->getKeys()); // unreliable

    // non-existant key
    $this->assertFalse($cache->get('cache-exchange-test-key'));
    $this->assertFalse($cache->delete('cache-exchange-test-key'));
  }

  public function testRedisAdapter()
  {
    $redis = new Redis([
      'host' => 'cache-exchange-redis',
    ]);

    $cache = new Cache($redis);

    $cache->clear();
    $this->assertEmpty($cache->getKeys());

    $this->assertTrue($cache->set('cache-exchange-test-key', 'test-data'));
    $this->assertEquals('test-data', $cache->get('cache-exchange-test-key'));
    $this->assertContains('cache-exchange-test-key', $cache->getKeys());
    $this->assertTrue($cache->exists('cache-exchange-test-key'));
    $this->assertTrue($cache->delete('cache-exchange-test-key'));
    $this->assertFalse($cache->exists('cache-exchange-test-key'));

    $this->assertTrue($cache->set('cache-exchange-test-key-2', 'test-data-2', 500));
    $this->assertEquals('test-data-2', $cache->get('cache-exchange-test-key-2'));
    $this->assertContains('cache-exchange-test-key-2', $cache->getKeys());
    $this->assertTrue($cache->exists('cache-exchange-test-key-2'));
    $this->assertTrue($cache->delete('cache-exchange-test-key-2'));
    $this->assertFalse($cache->exists('cache-exchange-test-key-2'));

    $this->assertEmpty($cache->getKeys());

    // non-existant key
    $this->assertNull($cache->get('cache-exchange-test-key'));
    $this->assertFalse($cache->delete('cache-exchange-test-key'));
  }
}
