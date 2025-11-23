<?php

namespace App\Providers;

// --- 1. Import your Models and Observers ---
use App\Models\Resident;
use App\Observers\ResidentObserver;
// --- End of Imports ---

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        // This line registers your observer.
        // It MUST be in this format: Model::class => [Observer::class]
        Resident::class => [ResidentObserver::class],
    ];

    /**
     * Register any events for your application.
     *
     * This boot() method is required to register the observers.
     * Do not delete it or make it empty.
     */
    public function boot(): void
    {
        // This line calls the parent boot method, which handles
        // registering the $observers array.
        parent::boot(); 
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}