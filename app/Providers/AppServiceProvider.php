<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Tender;
use App\Policies\TenderPolicy;

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
        // Registrar las políticas de autorización
        $this->registerPolicies();
    }

    /**
     * Registrar las políticas de la aplicación
     */
    protected function registerPolicies(): void
    {
        \Illuminate\Support\Facades\Gate::policy(Tender::class, TenderPolicy::class);
    }
}
