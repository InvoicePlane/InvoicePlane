<?php

namespace FI\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AfterMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (config('app.debug')) {
            $queries = DB::getQueryLog();

            $logContent = "\r\nREQUEST URL: " . $request->fullUrl() . "\r\n";
            $logContent .= "QUERY COUNT: " . count($queries) . "\r\n\r\n";

            $queryNum = 1;

            foreach ($queries as $query) {
                foreach ($query['bindings'] as $i => $binding) {
                    if ($binding instanceof \DateTime) {
                        $query['bindings'][$i] = $binding->format('Y-m-d H:i:s');
                    } else {
                        if (is_string($binding)) {
                            $query['bindings'][$i] = "'$binding'";
                        }
                    }
                }

                // Insert bindings into query
                $query['query'] = str_replace(['%', '?'], ['%%', '%s'], $query['query']);

                $query['query'] = vsprintf($query['query'], $query['bindings']);

                $logContent .= 'QUERY #' . $queryNum . "\r\n";
                $logContent .= 'SQL: ' . $query['query'] . "\r\n";
                $logContent .= 'TIME: ' . ($query['time'] / 1000) . " seconds\r\n\r\n";

                $queryNum++;
            }

            Log::info($logContent);
        }

        return $response;
    }
}
