<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\Controller\Action;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Controller\Action\PageviewAction;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Setono\GoogleAnalyticsServerSideTrackingBundle\Controller\Action\PageviewAction
 */
final class PageviewActionTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_sets_url_from_query(): void
    {
        $hitBuilder = $this->prophesize(HitBuilderInterface::class);
        $hitBuilder
            ->setDocumentLocationUrl('https://example.com/jeans/cool-blue-jeans')
            ->shouldBeCalledOnce()
        ;

        $request = Request::create('https://example.com/pageview?url=https%3A%2F%2Fexample.com%2Fjeans%2Fcool-blue-jeans');

        $action = new PageviewAction($hitBuilder->reveal());
        $action($request);
    }

    /**
     * @test
     */
    public function it_sets_url_from_referrer(): void
    {
        $hitBuilder = $this->prophesize(HitBuilderInterface::class);
        $hitBuilder
            ->setDocumentLocationUrl('https://example.com/jeans/cool-blue-jeans')
            ->shouldBeCalledOnce()
        ;

        $request = Request::create('https://example.com/pageview', 'GET', [], [], [], ['HTTP_REFERER' => 'https://example.com/jeans/cool-blue-jeans']);

        $action = new PageviewAction($hitBuilder->reveal());
        $action($request);
    }

    /**
     * @test
     */
    public function it_returns_bad_request_if_url_is_not_set(): void
    {
        $hitBuilder = $this->prophesize(HitBuilderInterface::class);
        $hitBuilder
            ->setDocumentLocationUrl(Argument::any())
            ->shouldNotBeCalled()
        ;

        $request = Request::create('https://example.com/pageview');

        $action = new PageviewAction($hitBuilder->reveal());
        $response = $action($request);
        $content = $response->getContent();
        self::assertSame(400, $response->getStatusCode());
        self::assertIsString($content);
        self::assertJson($content);

        $json = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        self::assertIsArray($json);
        self::assertArrayHasKey('error', $json);
        self::assertSame('You have to pass the "url" via query parameter, i.e. https://example.com/?url=[encoded url]', $json['error']);
    }
}
