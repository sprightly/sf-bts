<?php
namespace AppBundle\Twig;

use Symfony\Component\DependencyInjection\Container;

class DateTimeTimeZoneExtension extends \Twig_Extension
{
    private $userTimeZone;

    /**
     * DateTimeTimeZoneExtension constructor.
     * @param $container Container
     */
    public function __construct(Container $container)
    {
        $this->userTimeZone = $container->get('session')->get('timezone');
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('date_time_time_zone', array($this, 'formatInTimeZone')),
        );
    }

    public function getName()
    {
        return 'DateTimeTimeZone';
    }

    public function formatInTimeZone(\DateTime $date, $format = 'd/m/Y')
    {
        $date->setTimezone(new \DateTimeZone($this->userTimeZone));

        return $date->format($format);
    }
}
