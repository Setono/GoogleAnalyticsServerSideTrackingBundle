# Symfony Google Analytics Server Side Tracking Bundle

[![Latest Version][ico-version]][link-packagist]
[![Latest Unstable Version][ico-unstable-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]

Use this bundle to track your visitors with Google Analytics, but using server side integration instead of the usual
client side integration.

This bundle is based on the [Google Analytics measurement protocol library](https://github.com/Setono/google-analytics-measurement-protocol)
which in turn is based on the Universal Analytics solution from Google. The new GA4 tracking solution will be implemented
when it's feature complete and out of beta.

## Installation

This bundle also depends on the [Client Id Bundle](https://github.com/Setono/ClientIdBundle) and the
[Consent Bundle](https://github.com/Setono/ConsentBundle), which generates client ids and provides consent
services respectively. If you're not bound by the EU cookie laws, take a look at the [configuration](https://github.com/Setono/ConsentBundle#configuration)
of the Consent Bundle to have consent granted by default.

To install this bundle, simply run:

```shell
composer require setono/google-analytics-server-side-tracking-bundle
```

This will install the bundle and enable it if you're using Symfony Flex. If you're not using Flex, add the bundle
manually to `bundles.php` instead.

### Create migration

Hits are saved in the database, so you need to create a migration file:

```shell
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## Configuration

To enable the tracking, you need to supply Google Analytics properties to track:

```yaml
# config/packages/setono_google_analytics_server_side_tracking.yaml
setono_google_analytics_server_side_tracking:
    properties:
        - "UA-123456-78"
        - "UA-345656-81" # Notice you can add as many properties as you'd like
```

If you have your properties stored somewhere else, i.e. in a database, you can just implement the
[`Setono\GoogleAnalyticsServerSideTrackingBundle\Provider\PropertyProviderInterface`](src/Provider/PropertyProviderInterface.php)
which returns a list of properties.

You can also configure the minimum amount of seconds before hits are sent to Google:

```yaml
# config/packages/setono_google_analytics_server_side_tracking.yaml
setono_google_analytics_server_side_tracking:
    send_delay: 600 # Wait a minimum of 10 minutes before sending a hit
```

## Usage

Out of the box, the bundle will start tracking visitors just like the client side integration will, but just as with
the client side integration you may want to add custom tracking specific to your application. Here's an example of
the ecommerce event `Purchase`:

```php
<?php

use Setono\GoogleAnalyticsMeasurementProtocol\DTO\Event\PurchaseEventData;
use Setono\GoogleAnalyticsMeasurementProtocol\DTO\ProductData;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Factory\HitBuilderFactoryInterface;

final class YourService
{
    private HitBuilderFactoryInterface $hitBuilderFactory;

    public function __construct(HitBuilderFactoryInterface $hitBuilderFactory)
    {
        $this->hitBuilderFactory = $hitBuilderFactory;
    }

    public function track(): void
    {
        $hitBuilder = $this->hitBuilderFactory->createEventHitBuilder();

        $purchaseEvent = new PurchaseEventData('ORDER123', 'example.com', 431.25, 'EUR', 8.43, 2.56);

        $product1 = ProductData::createAsProductType('BLACK_T_SHIRT_981', 'Black T-shirt');
        $product1->brand = 'Gucci';
        $product1->quantity = 2;
        $product1->price = 145.23;
        $product1->variant = 'Black';
        $product1->category = 'T-Shirts';

        $product2 = ProductData::createAsProductType('BLUE_T_SHIRT_981', 'Blue T-shirt');
        $product2->brand = 'Chanel';
        $product2->quantity = 1;
        $product2->price = 148.99;
        $product2->variant = 'Blue';
        $product2->category = 'T-Shirts';

        $purchaseEvent->products[] = $product1;
        $purchaseEvent->products[] = $product2;

        $purchaseEvent->applyTo($hitBuilder);
    }
}
```

### Send hits to Google

Use a cronjob to send hits to Google regularly:

```shell
* * * * * bin/console setono:google-analytics:send-hits
```

No matter the interval in the cronjob, hits will never be sent before the `send_delay` has passed (see [configuration](#Configuration)).

[ico-version]: https://poser.pugx.org/setono/google-analytics-server-side-tracking-bundle/v/stable
[ico-unstable-version]: https://poser.pugx.org/setono/google-analytics-server-side-tracking-bundle/v/unstable
[ico-license]: https://poser.pugx.org/setono/google-analytics-server-side-tracking-bundle/license
[ico-github-actions]: https://github.com/Setono/GoogleAnalyticsServerSideTrackingBundle/workflows/build/badge.svg

[link-packagist]: https://packagist.org/packages/setono/google-analytics-server-side-tracking-bundle
[link-github-actions]: https://github.com/Setono/GoogleAnalyticsServerSideTrackingBundle/actions
