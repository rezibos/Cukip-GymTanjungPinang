<?php

namespace App\Providers;

use App\Contracts\PaymentGateway;
use App\Models\GymProfile;
use App\Services\MidtransGateway;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, MidtransGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.footer', function (ViewContract $view): void {
            $profile = rescue(
                fn (): GymProfile => GymProfile::current(),
                rescue: null,
                report: false,
            );

            $view->with('footerProfile', $profile);
        });
    }
}
