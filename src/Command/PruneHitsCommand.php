<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Command;

use Setono\GoogleAnalyticsServerSideTrackingBundle\Repository\HitRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

final class PruneHitsCommand extends Command
{
    protected static $defaultName = 'setono:google-analytics:prune-hits';

    private HitRepositoryInterface $hitRepository;

    private int $threshold;

    /**
     * @param int $threshold the number of minutes to wait before pruning sent hits
     */
    public function __construct(HitRepositoryInterface $hitRepository, int $threshold)
    {
        $this->hitRepository = $hitRepository;
        $this->threshold = $threshold;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('threshold', null, InputOption::VALUE_REQUIRED, 'The number of minutes to wait before deleting sent hits', $this->threshold);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $threshold = $input->getOption('threshold');
        Assert::integerish($threshold);
        Assert::greaterThanEq($threshold, 0);

        $olderThan = (new \DateTimeImmutable())->sub(new \DateInterval(sprintf('PT%dM', (int) $threshold)));
        $this->hitRepository->prune($olderThan);

        return 0;
    }
}
