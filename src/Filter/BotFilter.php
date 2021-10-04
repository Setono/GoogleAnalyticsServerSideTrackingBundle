<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Filter;

use DeviceDetector\Cache\PSR6Bridge;
use DeviceDetector\Parser\Bot as BotParser;
use Psr\Cache\CacheItemPoolInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class BotFilter implements FilterInterface
{
    private RequestStack $requestStack;

    private CacheItemPoolInterface $cache;

    public function __construct(RequestStack $requestStack, CacheItemPoolInterface $cache)
    {
        $this->requestStack = $requestStack;
        $this->cache = $cache;
    }

    public function filter(HitBuilderInterface $hitBuilder): bool
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return true;
        }

        $userAgent = $request->headers->get('user-agent');
        if (null === $userAgent) {
            return true;
        }

        $botParser = new BotParser();
        $botParser->setUserAgent($userAgent);
        $botParser->discardDetails();
        $botParser->setCache(new PSR6Bridge($this->cache));
        $result = $botParser->parse();

        return null === $result;
    }
}
