<?php

namespace Heritage\Tests\Notifications\Slack;

use Closure;
use Heritage\Notifications\Notification;
use Heritage\Notifications\Slack\SlackMessage;

class SlackChannelTestNotification extends Notification
{
    private Closure $callback;

    public function __construct(?Closure $callback = null)
    {
        $this->callback = $callback ?? function () {
            //
        };
    }

    public function toSlack($notifiable)
    {
        return tap(new SlackMessage(), $this->callback);
    }
}
