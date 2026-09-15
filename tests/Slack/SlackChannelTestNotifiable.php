<?php

namespace Heritage\Tests\Notifications\Slack;

use Heritage\Notifications\Notifiable;

class SlackChannelTestNotifiable
{
    use Notifiable;

    protected $route;

    public function __construct($route = null)
    {
        $this->route = $route;
    }

    public function routeNotificationForSlack()
    {
        return $this->route;
    }
}
