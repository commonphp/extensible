<?php

use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionStore;
use Neuron\Extensibility\ExtensionType;

// Create the extension type for a "database driver" in this example
#[Attribute(Attribute::TARGET_CLASS)]
#[ExtensionType(singleton: true, allowPreloading: false, requireInterface: DatabaseDriverInterface::class)]
class DatabaseDriver extends AbstractExtension
{

}

// An interface that all database drivers must implement
interface DatabaseDriverInterface
{
    public function fetchAll($query, array $parameters): array;
}

// Driver for mysql
#[DatabaseDriver('mysql')]
class MysqlDriver implements DatabaseDriverInterface
{
    public function fetchAll($query, array $parameters): array
    {
        return [];
    }
}

// Driver for sqlite
#[DatabaseDriver('sqlite')]
class SqliteDriver implements DatabaseDriverInterface
{
    public function fetchAll($query, array $parameters): array
    {
        return [];
    }
}

$instantiator = new MyInstantiator();
$log = new MyLogger();
$eventDispatcher = new MyEventDispatcher();

// Create the store
$store = new ExtensionStore($instantiator, $log, $eventDispatcher);

// Register the database driver type
$store->typeRegistry->register(DatabaseDriver::class);

// Register the known database drivers
$store->registry->register(DatabaseDriver::class, MysqlDriver::class);
$store->registry->register(DatabaseDriver::class, SqliteDriver::class);

// Get the mysql driver
$mysql = $store->get(MysqlDriver::class);

// Get the sqlite driver
$sqlite = $store->get(SqliteDriver::class);
