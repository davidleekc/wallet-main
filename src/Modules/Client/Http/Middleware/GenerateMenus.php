<?php

namespace Modules\Client\Http\Middleware;

use Closure;

class GenerateMenus
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*
         *
         * Module Menu for Admin Backend
         *
         * *********************************************************************
         */
        \Menu::make('admin_sidebar', function ($menu) {

            // Tags
            $menu->add('<i class="fa fa-users c-sidebar-nav-icon" aria-hidden="true"></i> Client', [
                'route' => 'backend.clients.index',
                'class' => 'c-sidebar-nav-item',
            ])
            ->data([
                'order'         => 10,
                'activematches' => ['admin/client*'],
                'permission'    => ['view_clients'],
            ])
            ->link->attr([
                'class' => 'c-sidebar-nav-link',
            ]);
        })->sortBy('order');

        return $next($request);
    }
}
