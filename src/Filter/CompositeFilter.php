<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Filter;

use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;

final class CompositeFilter implements FilterInterface
{
    /** @var array<array-key, FilterInterface> */
    private array $filters = [];

    public function add(FilterInterface $filter): void
    {
        $this->filters[] = $filter;
    }

    public function filter(HitBuilderInterface $hitBuilder): bool
    {
        foreach ($this->filters as $filter) {
            if (!$filter->filter($hitBuilder)) {
                return false;
            }
        }

        return true;
    }
}
