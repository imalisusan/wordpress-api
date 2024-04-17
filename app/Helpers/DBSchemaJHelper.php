<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Traits\CanLog;
use Exception;
use Illuminate\Support\Facades\DB;

class DBSchemaJHelper
{
    use CanLog;

    /**
     * @var string
     */
    protected mixed $currentDBDriver;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->currentDBDriver = config('database.default');
    }

    /**
     * Check if index exists
     */
    public static function tableHasIndex(?string $tableName, ?string $indexName): bool
    {
        try {
            if ($tableName !== null && $indexName !== null) {
                $dbDriver = (new self())->currentDBDriver;
                if ($dbDriver === 'mysql') {
                    return (new self())->mysqlCheckIfTableHasIndex($tableName, $indexName);
                }
            }

            return true;
        } catch (Exception $exception) {
            (new self())->logException($exception);

            return true;
        }
    }

    /**
     * Check if index exists
     */
    public static function runRawQuery(?string $query): bool
    {
        try {
            if ($query !== null) {
                $dbDriver = (new self())->currentDBDriver;
                if ($dbDriver === 'mysql') {
                    return (new self())->mysqlRunRawQuery($query);
                }
            }

            return true;
        } catch (Exception $exception) {
            (new self())->logException($exception);

            return true;
        }
    }

    /**
     * MySql check if index exists
     */
    private function mysqlCheckIfTableHasIndex($tableName, $indexName): bool
    {
        try {
            if ($tableName !== null && $indexName !== null) {
                $result = DB::select('SHOW INDEX FROM `' . $tableName . "` WHERE Key_name = '" . $indexName . "'");

                return isset($result[0]->Key_name);
            }

            return true;
        } catch (Exception $exception) {
            $this->logException($exception);

            return true;
        }
    }

    /**
     * MySql check if index exists
     */
    public static function dropTableIndex(string $tableName, string $columnName): void
    {
        $result = DB::select('SHOW INDEX FROM `' . $tableName . "` WHERE Column_name = '" . $columnName . "'");
        $indexes = collect($result)->pluck('Key_name');

        foreach ($indexes as $index) {
            if ((new self())->mysqlCheckIfTableHasIndex($tableName, $index)) {
                (new self())->mysqlRunRawQuery("ALTER TABLE {$tableName} DROP INDEX {$index};");
            }
        }
    }

    /**
     * Run a raw DB query
     *
     * @param  ?string  $query
     * @return mixed
     */
    private function mysqlRunRawQuery(?string $query): bool
    {
        try {
            return DB::statement($query);
        } catch (Exception $exception) {
            $this->logException($exception);

            return false;
        }
    }
}
