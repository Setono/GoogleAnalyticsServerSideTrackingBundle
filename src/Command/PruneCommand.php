<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Command;

use Setono\GoogleAnalyticsServerSideTrackingBundle\Repository\HitRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PruneCommand extends Command
{
    protected static $defaultName = 'setono:google-analytics:prune';

    private HitRepositoryInterface $hitRepository;

    private int $threshold;

    /**
     * @param int $threshold the number of minutes to wait before pruning sent hits
     */
    public function __construct(HitRepositoryInterface $hitRepository, int $threshold)
    {
        parent::__construct();

        $this->hitRepository = $hitRepository;
        $this->threshold = $threshold;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $olderThan = (new \DateTimeImmutable())->sub(new \DateInterval(sprintf('PT%dM', $this->threshold)));
        $this->hitRepository->prune($olderThan);

        return 0;
    }
}
