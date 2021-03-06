<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Setono\GoogleAnalyticsMeasurementProtocol\Client\ClientInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Repository\HitRepositoryInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Workflow\SendHitWorkflow;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;
use Webmozart\Assert\Assert;

final class SendHitsCommand extends Command
{
    protected static $defaultName = 'setono:google-analytics:send-hits';

    private ?WorkflowInterface $workflow = null;

    private ?ObjectManager $manager = null;

    private ClientInterface $client;

    private HitRepositoryInterface $hitRepository;

    private Registry $workflowRegistry;

    private ManagerRegistry $managerRegistry;

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

        return 0;
    }

    private function getWorkflow(object $obj): WorkflowInterface
    {
        if (null === $this->workflow) {
            Assert::true($this->workflowRegistry->has($obj, SendHitWorkflow::NAME));

            $this->workflow = $this->workflowRegistry->get($obj, SendHitWorkflow::NAME);
        }

        return $this->workflow;
    }

    private function getManager(object $obj): ObjectManager
    {
        if (null === $this->manager) {
            $manager = $this->managerRegistry->getManagerForClass(get_class($obj));
            Assert::notNull($manager);

            $this->manager = $manager;
        }

        return $this->manager;
    }
}
