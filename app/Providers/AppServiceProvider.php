<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use URL, DB, Log;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // URL::forceScheme('https');

        $this->logSqlStatement();
    }

    /**
     * 將每一句 SQL 紀錄在 log
     *
     * @return void
     */
    protected function logSqlStatement()
    {
        DB::listen(function ($query) {
            $bindings  = $query->bindings;
            $exec_time = $query->time;
            $query     = $query->sql;

            // 整理 binding 格式
            foreach ($bindings as $i => $binding) {
                if ($binding instanceof \DateTime) {
                    $bindings[$i] = $binding->format('Y-m-d H:i:s');
                } elseif (is_string($binding)) {
                    $bindings[$i] = str_replace("'", "\\'", $binding);
                }
            }

            // now we create full SQL query - in case of failure, we log this
            $query    = str_replace(['%', '?', "\n"], ['%%', "'%s'", ' '], $query);
            $full_sql = vsprintf($query, $bindings);
            $exec_time = number_format(($exec_time / 1000.0), 4); // 單位轉成秒 (second)

            Log::info(
                $full_sql,
                [$exec_time]
            );
        });
    }
}
