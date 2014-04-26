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
        return new Response('To Aarrghhh type /aarrghhh/{lat}/{lon}/{radius}');
    }
    
    /**
     * @Route("/aarrghhh/{lat}/{lon}/{radius}", defaults={"radius" = 1})
     * @Template()
     */
    public function serachAction($lat, $lon)
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
}
