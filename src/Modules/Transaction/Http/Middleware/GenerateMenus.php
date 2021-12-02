<?php

namespace Modules\Transaction\Http\Middleware;

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
            $menu->add('<i class="fa fa-cubes c-sidebar-nav-icon" aria-hidden="true"></i> Transaction', [
                'route' => 'backend.transactions.index',
                'class' => 'c-sidebar-nav-item',
            ])
            ->data([
                'order'         => 11,
                'activematches' => ['admin/transaction*'],
                'permission'    => ['view_clients'],
            ])
            ->link->attr([
                'class' => 'c-sidebar-nav-link',
            ]);
        })->sortBy('order');

        return $next($request);
    }
}
