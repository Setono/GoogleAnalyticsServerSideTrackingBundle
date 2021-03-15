<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Workflow;

use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\HitInterface;

final class SendHitWorkflow
{
    public const NAME = 'setono_google_analytics_server_side_tracking_send_hit';

    public const TRANSITION_SEND = 'send';

    public const TRANSITION_FAIL = 'fail';

    private function __construct()
    {
    }

    /**
     * @return array<array-key, string>
     */
    public static function getStates(): array
    {
        return [
            HitInterface::STATE_PENDING, HitInterface::STATE_SENT, HitInterface::STATE_FAILED,
        ];
    }

    public static function getConfig(): array
    {
        return [
            self::NAME => [
                'type' => 'state_machine',
                'marking_store' => [
                    'type' => 'method',
                    'property' => 'state',
                ],
                'supports' => HitInterface::class,
                'initial_marking' => HitInterface::STATE_PENDING,
                'places' => self::getStates(),
                'transitions' => [
                    self::TRANSITION_SEND => [
                        'from' => HitInterface::STATE_PENDING,
                        'to' => HitInterface::STATE_SENT,
                    ],
                    self::TRANSITION_FAIL => [
                        'from' => HitInterface::STATE_PENDING,
                        'to' => HitInterface::STATE_FAILED,
                    ],
                ],
            ],
        ];
    }
}
