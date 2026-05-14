<?php

namespace App\Providers;

use App\Models\DynamicMenu;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

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
     */
    public function boot(): void
    {
        if (app()->environment('local')) {
            Mail::extend('smtp', function (array $config) {
                // We use the factory to build the transport with our custom options
                $factory = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory();
                
                // This 'verify_peer' key is the one Symfony 7 uses
                $config['verify_peer'] = false;
                
                return $factory->create(new \Symfony\Component\Mailer\Transport\Dsn(
                    $config['port'] == 465 ? 'smtps' : 'smtp',
                    $config['host'],
                    $config['username'] ?? null,
                    $config['password'] ?? null,
                    $config['port'] ?? null,
                    $config
                ));
            });
        }

        // Keep old MySQL/utf8mb4 compatibility
        Schema::defaultStringLength(191);

        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        /**
         * Minimal global composer:
         * - Always provide $isLoggedIn and $loggedUserDetails to EVERY view
         * - This is lightweight and safe for both frontend and backend
         */
        View::composer('*', function ($view) {
            $view->with('isLoggedIn', Auth::check());
            $view->with('loggedUserDetails', Auth::user());
        });

        /**
         * Sidebar-only composer:
         * - Runs ONLY when layouts/partials/sidebar.blade.php is rendered
         * - For guests: provide safe defaults (no queries that rely on auth)
         * - For logged-in users: load menus + permission arrays
         */
        View::composer(['layouts.partials.generated-navigation'], function ($view) {
            // Safe defaults so blades never crash (frontend/guest or temp)
            $menuItems       = collect();
            $parentMenuItems = collect();
            $subMenuItems    = collect();
            $permissionHave  = [];
            $arrParentID     = [];

            // If not logged in, share defaults and bail out early
            if (!Auth::check()) {
                $view->with(compact('menuItems', 'parentMenuItems', 'subMenuItems', 'permissionHave', 'arrParentID'));
                return;
            }

            // ---- MENUS (your original queries, kept intact) ----
            $menuItems = DynamicMenu::where('dynamic_menus.show_menu', 1)
                ->orderBy('parent_order', 'ASC')
                ->get();

            $parentMenuItems = DynamicMenu::where('dynamic_menus.show_menu', 1)
                ->where('dynamic_menus.parent_id', '!=', 0)
                ->where('dynamic_menus.is_parent', 1)
                ->orderBy('parent_order', 'ASC')
                ->get();

            $subMenuItems = DynamicMenu::where('dynamic_menus.show_menu', 1)
                ->where('dynamic_menus.parent_id', '!=', '0')
                ->where('dynamic_menus.is_parent', 0)
                ->orderBy('child_order', 'ASC')
                ->get();

            // ---- ROLE / PERMISSIONS (SAFE) ----
            $user   = Auth::user();


            //    works whether roles relation is loaded or not
            $roleID = data_get($user, 'roles.0.id');

            if (!empty($roleID)) {
                $permissionRows = DB::table('role_has_permissions')
                    ->select('permissions.dynamic_menu_id', 'dynamic_menus.parent_id')
                    ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                    ->join('dynamic_menus', 'permissions.dynamic_menu_id', '=', 'dynamic_menus.id')
                    ->where('role_has_permissions.role_id', $roleID)
                    ->groupBy('permissions.dynamic_menu_id')
                    ->groupBy('dynamic_menus.parent_id')
                    ->get()
                    ->toArray();

                foreach ($permissionRows as $per) {
                    $permissionHave[] = (int) $per->dynamic_menu_id;
                    $arrParentID[]    = (int) $per->parent_id;
                }

                // De-duplicate just in case
                $permissionHave = array_values(array_unique($permissionHave));
                $arrParentID    = array_values(array_unique($arrParentID));
            }

            // Share everything the sidebar expects
            $view->with(compact('menuItems', 'parentMenuItems', 'subMenuItems', 'permissionHave', 'arrParentID'));
        });

        /**
         * Example gate (unchanged)
         */
        Gate::define('viewPulse', function (User $user) {
            return in_array($user->email, [
                'ayodhya@tekgeeks.net',
            ], true);
        });


        // Add URL, method, IP, and a normalized "app/models/..." path of the subject model
        Activity::saving(function (Activity $activity) {
            // properties is a Spatie\Activitylog\ActivityProperties (arrayable)
            $props = collect($activity->properties ?? []);

            $subject = $activity->subject; // Eloquent model or null

            $modelPath = $subject
                ? Str::of($subject::class)      // e.g. "App\Models\User"
                    ->replace('App\\Models\\', 'app/models/')
                    ->replace('\\', '/')
                    ->lower()
                    ->value()
                : null;

            // Read client IP (works in dev and prod once proxies are configured)
            $clientIp = request()->ip();
            $url = request()->fullUrl();

            $activity->properties = $props->merge([
                'method'     => request()->method(),
                'ip'         => request()->ip(),
                'model_path' => $modelPath,
            ]);

            $activity->ip = $clientIp;
            $activity->url = $url;
        });
    }
}
