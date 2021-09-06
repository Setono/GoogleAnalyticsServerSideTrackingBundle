<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Filter;

use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This filter is only enabled when debug is enabled. It will filter requests made to the Symfony profiler
 */
final class ProfilerFilter implements FilterInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function filter(HitBuilderInterface $hitBuilder): bool
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return true;
        }

        $pathInfo = $request->getPathInfo();
        if (strpos($pathInfo, '/_wdt') !== false) {
            return false;
        }

        if (strpos($pathInfo, '/_profiler') !== false) {
            return false;
        }

        return true;
    }
}
