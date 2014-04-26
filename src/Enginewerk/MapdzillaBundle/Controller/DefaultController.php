<?php

namespace Enginewerk\MapdzillaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array();//('<h1>Mapdzilla</h1><p>To Aarrghhh type /aarrghhh/{lat}/{lon}/{radius} OR /awrr/{lat}/{lon}/{radius}</p>');
    }
    
    /**
     * @Route("/aarrghhh/{lat}/{lon}/{radius}", defaults={"radius" = 1})
     * @Template()
     */
    public function serachAction($lat, $lon, $radius)
    {
        $data = array(
            array(
                'll' => array(
                    'lat' => "53.4309816",
                    'lon' => "14.5526560"
                    ),
                'zone' => 'A',
                'capacity' => 45
                ),
            array(
                'll' => array(
                    'lat' => "53.4309816",
                    'lon' => "14.5526560"
                    ),
                'zone' => 'A',
                'capacity' => 45
                )
        );
        
        return new JsonResponse($data, 200);
    }
    
    /**
     * @Route("/awrr/{lat}/{lon}/{radius}", defaults={"radius" = 1})
     * @Template()
     */
    public function findAction($lat, $lon, $radius)
    {
        $results = $this
                ->get('mapdzilla_finder')
                ->find($lat, $lon, $radius);
                
        return new JsonResponse($results, 200);
    }
}
