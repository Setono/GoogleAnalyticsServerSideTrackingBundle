<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Workflow;

final class SendHitWorkflow
{
    public const NAME = 'setono_google_analytics_server_side_tracking_send_hit';

    public const TRANSITION_SEND = 'send';

    public const TRANSITION_FAIL = 'fail';

    private function __construct()
    {
    }
}
