<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Resolver;

use DateInterval;
use RuntimeException;
use Safe\DateTime;
use function Safe\sprintf;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Uid\Uuid;

final class CookieBasedClientIdResolver implements ClientIdResolverInterface, EventSubscriberInterface
{
    private ?string $clientId = null;

    private RequestStack $requestStack;

    private string $cookieKey;

    public function __construct(RequestStack $requestStack, string $cookieKey)
    {
        $this->requestStack = $requestStack;
        $this->cookieKey = $cookieKey;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'save',
        ];
    }

    public function resolve(): string
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            throw new RuntimeException(sprintf('Only use the %s in an request cycle context', get_class($this)));
        }

        if (null === $this->clientId) {
            $this->clientId = $request->cookies->has($this->cookieKey) ? $request->cookies->get($this->cookieKey, '') : (string) Uuid::v4();
        }

        return $this->clientId;
    }

    public function save(ResponseEvent $event): void
    {
        $expire = (new DateTime())->add(new DateInterval('P2Y'));
        $event->getResponse()
            ->headers
            ->setCookie(Cookie::create($this->cookieKey, $this->resolve(), $expire))
        ;
    }
}
