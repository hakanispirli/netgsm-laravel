<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel;

use HakanIspirli\NetgsmLaravel\Console\NetgsmSmsWorkCommand;
use HakanIspirli\NetgsmLaravel\Contracts\NetgsmClientInterface;
use HakanIspirli\NetgsmLaravel\Contracts\OtpSenderInterface;
use HakanIspirli\NetgsmLaravel\Contracts\SmsSenderInterface;
use HakanIspirli\NetgsmLaravel\Services\LaravelNetgsmClient;
use HakanIspirli\NetgsmLaravel\Services\NetgsmSmsService;
use Illuminate\Support\ServiceProvider;

final class NetgsmServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/netgsm.php', 'netgsm');

        $this->app->singleton(NetgsmClientInterface::class, LaravelNetgsmClient::class);
        $this->app->singleton(NetgsmSmsService::class);
        $this->app->alias(NetgsmSmsService::class, 'netgsm-sms');
        $this->app->alias(NetgsmSmsService::class, SmsSenderInterface::class);
        $this->app->alias(NetgsmSmsService::class, OtpSenderInterface::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/netgsm.php' => config_path('netgsm.php'),
            ], 'netgsm-config');

            $this->publishes([
                __DIR__.'/../cron/sms.sh' => base_path('cron/sms.sh'),
            ], 'netgsm-cron');

            $this->commands([
                NetgsmSmsWorkCommand::class,
            ]);
        }
    }
}
