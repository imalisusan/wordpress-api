<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Database\Schema\Blueprint;

class DatabaseMigrationHelper
{
    /**
     * Slug column
     */
    public static function slugColumn(Blueprint $table, ?int $columnLength = 100): void
    {
        $table->string('slug', $columnLength)->unique(CraydelHelperFunctions::makeRandomString(15));
    }

    /**
     * Audit trail table columns
     */
    public static function addAuditTrailColumns(Blueprint $table): void
    {
        $table->tinyInteger('is_active')->default(1);
        $table->tinyInteger('is_deleted')->default(0);
        $table->string('created_by')->nullable();
        $table->string('updated_by')->nullable();
        $table->string('deleted_by')->nullable();
        $table->dateTimeTz('created_at')->nullable();
        $table->dateTimeTz('updated_at')->nullable();
        $table->dateTimeTz('deleted_at')->nullable();
    }
}
