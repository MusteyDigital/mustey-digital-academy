protected $listen = [
    \Illuminate\Auth\Events\Registered::class => [
        \App\Listeners\SendWelcomeEmail::class,
    ],
];
