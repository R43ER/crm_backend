<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Crm;

class SubdomainMiddleware
{
    public function handle($request, Closure $next)
    {
        // Если CRM уже установлена в сессии, пропускаем поиск
        if (session()->has('crm_id')) {
            return $next($request);
        }

        $host = $request->getHost(); // Например, company.gavrelets.ru
        $parts = explode('.', $host);
        
        // Проверяем, что есть поддомен (исключая www)
        if (count($parts) > 2 && $parts[0] !== 'www') {
            $subdomain = $parts[0];

            // Поиск CRM по поддомену
            $crm = Crm::where('subdomain', $subdomain)->first();
            if ($crm) {
                // Сохраняем crm_id в сессии и передаём объект CRM в атрибуты запроса
                session(['crm_id' => $crm->id]);
                $request->attributes->add(['crm' => $crm]);
            } else {
                // Если запись не найдена, возвращаем ошибку 404
                abort(404, 'CRM не найдена');
            }
        }

        return $next($request);
    }
}
