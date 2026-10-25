<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Schema::hasTable / hasColumn yerine kullanılan önbellekli şema bilgisi.
 *
 * MySQL'de her Schema::hasTable/hasColumn çağrısı information_schema'ya ayrı bir sorgu atar.
 * Paylaşımlı hosting'de bu sorgular yavaştır ve istek başına yüzlercesi DB bağlantısını
 * uzun süre meşgul eder (max_user_connections hatası). Burada tüm tablo/sütun listesi
 * tek sorguda okunur, istek içinde bellekte ve istekler arasında cache'te tutulur.
 *
 * Cache `php artisan migrate` sonunda (MigrationsEnded) ve `cache:clear` ile temizlenir.
 */
final class SchemaInfo
{
    private const CACHE_PREFIX = 'schema_info:v1:';

    /** @var array<string, array<string, true>>|null tablo => [sütun => true] */
    private static ?array $tables = null;

    /** @var array<string, bool> Cache'te olmayıp canlı kontrol edilen tablo/sütunlar (istek içi). */
    private static array $liveChecked = [];

    public static function hasTable(string $table): bool
    {
        $table = strtolower($table);
        if (isset(self::load()[$table])) {
            return true;
        }

        return self::liveCheck('t:'.$table, fn (): bool => Schema::hasTable($table));
    }

    public static function hasColumn(string $table, string $column): bool
    {
        $table = strtolower($table);
        $column = strtolower($column);
        if (isset(self::load()[$table][$column])) {
            return true;
        }

        return self::liveCheck('c:'.$table.'.'.$column, fn (): bool => Schema::hasColumn($table, $column));
    }

    /** @param  list<string>  $columns */
    public static function hasColumns(string $table, array $columns): bool
    {
        foreach ($columns as $column) {
            if (! self::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    public static function flush(): void
    {
        self::$tables = null;
        self::$liveChecked = [];
        try {
            Cache::forget(self::cacheKey());
        } catch (Throwable) {
            // cache erişilemezse bellek temizliği yeterli
        }
    }

    /** @return array<string, array<string, true>> */
    private static function load(): array
    {
        if (self::$tables !== null) {
            return self::$tables;
        }

        try {
            self::$tables = Cache::rememberForever(self::cacheKey(), fn (): array => self::introspect());
        } catch (Throwable) {
            self::$tables = self::introspect();
        }

        return self::$tables;
    }

    /**
     * Cache'te bulunmayan bir tablo/sütun istendiğinde (ör. migration sonrası cache temizlenmediyse)
     * bir kez canlı kontrol yapılır; varsa cache yenilenir. Böylece sonuç her zaman doğrudur.
     */
    private static function liveCheck(string $key, callable $check): bool
    {
        if (array_key_exists($key, self::$liveChecked)) {
            return self::$liveChecked[$key];
        }

        $exists = (bool) $check();
        self::$liveChecked[$key] = $exists;
        if ($exists) {
            $live = self::$liveChecked;
            self::flush();
            self::$liveChecked = $live;
        }

        return $exists;
    }

    /** @return array<string, array<string, true>> */
    private static function introspect(): array
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $tables = [];

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $rows = $connection->select(
                'select table_name as t, column_name as c from information_schema.columns where table_schema = ?',
                [$connection->getDatabaseName()]
            );
            foreach ($rows as $row) {
                $row = (array) $row;
                $t = strtolower((string) ($row['t'] ?? $row['T'] ?? $row['TABLE_NAME'] ?? ''));
                $c = strtolower((string) ($row['c'] ?? $row['C'] ?? $row['COLUMN_NAME'] ?? ''));
                if ($t !== '' && $c !== '') {
                    $tables[$t][$c] = true;
                }
            }

            return $tables;
        }

        foreach (Schema::getTableListing(schemaQualified: false) as $table) {
            $t = strtolower((string) $table);
            $tables[$t] = [];
            foreach (Schema::getColumnListing($table) as $column) {
                $tables[$t][strtolower((string) $column)] = true;
            }
        }

        return $tables;
    }

    private static function cacheKey(): string
    {
        $connection = config('database.default');

        return self::CACHE_PREFIX.$connection.':'.(string) config("database.connections.{$connection}.database");
    }
}
