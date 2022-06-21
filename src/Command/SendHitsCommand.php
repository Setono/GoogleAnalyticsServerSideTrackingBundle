<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\GoogleAnalyticsMeasurementProtocol\Client\ClientInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Repository\HitRepositoryInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Workflow\SendHitWorkflow;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;

final class SendHitsCommand extends Command
{
    use LockableTrait;

    use ORMManagerTrait;

    protected static $defaultName = 'setono:google-analytics:send-hits';

    private ?WorkflowInterface $workflow = null;

    private ?ObjectManager $manager = null;

    private ClientInterface $client;

    private HitRepositoryInterface $hitRepository;

    private Registry $workflowRegistry;

    private int $delay;

    public function __construct(
        ClientInterface $client,
        HitRepositoryInterface $hitRepository,
        Registry $workflowRegistry,
        ManagerRegistry $managerRegistry,
        int $delay
    ) {
        parent::__construct();

        $this->client = $client;
        $this->hitRepository = $hitRepository;
        $this->workflowRegistry = $workflowRegistry;
        $this->managerRegistry = $managerRegistry;
        $this->delay = $delay;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        while ($this->hitRepository->hasConsentedPending($this->delay)) {
            $bulkIdentifier = uniqid('bulk-', true);
            $this->hitRepository->assignBulkIdentifierToPendingConsented($bulkIdentifier, $this->delay);

            $manager = null;

            $hits = $this->hitRepository->findByBulkIdentifier($bulkIdentifier);
            foreach ($hits as $hit) {
                $workflow = $this->getWorkflow($hit);

                try {
                    if (!$workflow->can($hit, SendHitWorkflow::TRANSITION_SEND)) {
                        continue;
                    }

                    $this->client->sendHit((string) $hit->getQuery());

                    $workflow->apply($hit, SendHitWorkflow::TRANSITION_SEND);
                } catch (\Throwable $e) {
                    $workflow->apply($hit, SendHitWorkflow::TRANSITION_FAIL);
                } finally {
                    $manager = $this->getManager($hit);
                    $manager->flush();
                }
            }

            if (null !== $manager) {
                $manager->clear();
            }
        }

        $this->release();

        return 0;
    }

    private function getWorkflow(object $obj): WorkflowInterface
    {
        if (null === $this->workflow) {
            $this->workflow = $this->workflowRegistry->get($obj, SendHitWorkflow::NAME);
        }

        return $this->workflow;
    }
}
