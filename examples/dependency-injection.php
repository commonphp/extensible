<?php

use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionType;
use Psr\Log\LoggerInterface;

#[ExtensionType(singleton: CacheDriverInterface::class, allowPreloading: false, requireInterface: CacheDriverInterface::class)]
class CacheDriver extends AbstractExtension {}

interface CacheDriverInterface {
    public function get(string $key): mixed;
    public function set(string $key, mixed $value): void;
}

#[CacheDriver('redis')]
class RedisCache implements CacheDriverInterface {
    public function __construct(private LoggerInterface $logger) {}

    public function get(string $key): mixed {
        $this->logger->info("Fetching key: $key");
        return null;
    }

    public function set(string $key, mixed $value): void {
        $this->logger->info("Setting key: $key");
    }
}

$store->typeRegistry->register(CacheDriver::class);
$store->registry->register(CacheDriver::class, RedisCache::class);

$cache = $store->get(RedisCache::class);
$cache->set("foo", "bar");
