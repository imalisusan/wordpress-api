<?php

namespace App\Helpers;

use ClickHouseDB\Client;
use Exception;

class ClickHouseSMI2ConnectionHelper
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    private static array $instances = [];

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    protected function __construct() { }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone() { }

    /**
     * Singletons should not be restore from strings.
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a ClickHouseConnectionHelper.");
    }

    /**
     * This is the static method that controls the access to the singleton
     * instance. On the first run, it creates a singleton object and places it
     * into the static field. On subsequent runs, it returns the client existing
     * object stored in the static field.
     *
     * This implementation lets you subclass the Singleton class while keeping
     * just one instance of each subclass around.
     */
    public static function getInstance(): ClickHouseSMI2ConnectionHelper
    {
        $cls = static::class;

        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    /**
     * Finally, any singleton should define some business logic, which can be
     * executed on its instance.
     */
    public function connect(): Client
    {
        $config = [
            'host' => config('database.connections.clickhouse.host'),
            'port' => config('database.connections.clickhouse.port'),
            'username' => config('database.connections.clickhouse.username'),
            'password' => config('database.connections.clickhouse.password')
        ];

        $db = new Client($config);
        $db->database(config('database.connections.clickhouse.database'));
        $db->setTimeout(config('database.connections.clickhouse.timeout_connect'));
        $db->setConnectTimeOut(config('database.connections.clickhouse.timeout_connect'));
        $db->ping(true);

        return $db;
    }
}