<?php

namespace Heritage\Notifications;

use Heritage\Support\Facades\Notification;
use Heritage\Support\ServiceProvider;

class SlackChannelServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('slack', function ($app) {
                return new SlackNotificationRouterChannel($app);
            });
        });
    }
}
