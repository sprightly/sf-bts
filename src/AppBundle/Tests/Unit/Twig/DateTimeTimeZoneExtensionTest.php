<?php

namespace AppBundle\Tests\Unit\Twig;

use AppBundle\Twig\DateTimeTimeZoneExtension;
use Symfony\Component\DependencyInjection\Container;

class DateTimeTimeZoneExtensionTest extends \PHPUnit_Framework_TestCase
{

    /** @var  DateTimeTimeZoneExtension */
    protected $extension;

    public function setUp()
    {
        $session = $this->createMock('Symfony\Component\HttpFoundation\Session\Session');
        $session->method('get')
            ->willReturn('America/Yellowknife');

        $container = $this->createMock('Symfony\Component\DependencyInjection\Container');
        $container->method('get')
            ->willReturn($session);

        /** @var Container $container */
        $this->extension = new DateTimeTimeZoneExtension($container);
    }

    public function testFormatInTimeZone()
    {
        $this->extension->formatInTimeZone(new \DateTime('now'));
    }
}
