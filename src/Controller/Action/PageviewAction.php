<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Controller\Action;

use InvalidArgumentException;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class PageviewAction
{
    private HitBuilderInterface $pageviewHitBuilder;

    public function __construct(HitBuilderInterface $pageviewHitBuilder)
    {
        $this->pageviewHitBuilder = $pageviewHitBuilder;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $this->pageviewHitBuilder->setDocumentLocationUrl($this->resolveUrl($request));
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        return new JsonResponse();
    }

    private function resolveUrl(Request $request): string
    {
        /** @var mixed $url */
        $url = $request->query->get('url');
        if (is_string($url) && '' !== $url) {
            return $url;
        }

        $url = $request->headers->get('referer');
        if (is_string($url) && '' !== $url) {
            return $url;
        }

        throw new InvalidArgumentException('You have to pass the "url" via query parameter, i.e. https://example.com/?url=[encoded url]');
    }
}
