<?php

namespace Enginewerk\MapdzillaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $finder = $this->get('mapdzilla_finder');
        /* @var $finder \Enginewerk\MapdzillaBundle\Finder\ParkingLot */

        return array(
            'described' => count($finder->getWithDescription()),
            'undescribed' => count($finder->getWithNoDescription())
                );
    }

    /**
     * @Route("/v1/awrr/{lat}/{lon}/{radius}", defaults={"radius" = 1}, name="api1_find")
     * @Template()
     */
    public function findAction($lat, $lon, $radius)
    {
        $results = $this
                ->get('mapdzilla_finder')
                ->find($lat, $lon, $radius);

        return new JsonResponse(array('found' => count($results), 'parkings' => $results), 200);
    }
}
