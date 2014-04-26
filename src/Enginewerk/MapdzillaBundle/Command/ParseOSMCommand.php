<?php

namespace Enginewerk\MapdzillaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\DomCrawler\Crawler;
use Enginewerk\MapdzillaBundle\Entity\Node;
use Enginewerk\MapdzillaBundle\Entity\Way;

/**
 * Description of ParseOSMCommand
 *
 * @author Paweł Czyżewski <pawel.czyzewski@enginewerk.com>
 */
class ParseOSMCommand extends ContainerAwareCommand
{
    /**
     *
     * @var OutputInterface 
     */
    protected $output;
    
    /**
     *
     * @var Crawler
     */
    protected $crawler;


    protected function configure()
    {
        $this
            ->setName('mapdzilla:parse-osm')
            ->setDescription('Parses OSM file');
            //->addArgument('bbox', InputArgument::REQUIRED, 'FROM 14.5317,53.4205 TO 14.5653,53.4311')
        ;
        //14.4976,53.4254,14.5339,53.4416
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        
        //$mapfile = $this->getContainer()->get('kernel')->getRootDir() . '/data/map.osm';
        //$xmlData = file_get_contents($mapfile);
        
        $files = scandir($this->getContainer()->get('kernel')->getRootDir() . '/data/');
        foreach ($files as $mapfile) {
            $filepath = $this->getContainer()->get('kernel')->getRootDir() . '/data/' . $mapfile;
            if (is_file($filepath)) {
                $this->processData(file_get_contents($filepath));
            }
        }

    }
    
    protected function processData($xmlData) 
    {
        $this->output->write('<info>Dowloading data...</info>');
        /*$url = sprintf('http://api.openstreetmap.org/api/0.6/map?bbox=%s', $input->getArgument('bbox'));
        $xmlData = $this->getXMLData($url);*/
        $this->output->writeln(number_format(strlen($xmlData), 2, ',', ' ') . ' bytes');
        
        $this->crawler = new Crawler($xmlData);
        //$mapNodes = $crawler->filter('osm > way > tag[v=parking]');
        $mapNodes = $this
                ->getCrawler()
                ->filter('tag[v=parking]');
        
        //$mapNodes = $crawler->filter('osm > node')->siblings();

        $this->output->writeln('<info>Nodes</info>');    
        $nodes = $this->filterNodes($mapNodes);
        
        foreach ($nodes as $node) {
            $this->updateNode($node);
        }
        
        $this
                ->getDoctrine()
                ->getEntityManager()
                ->flush();
        
        $this->output->writeln('<info>Ways</info>');
        $ways = $this->filterWays($mapNodes);
        
        foreach($ways as $wayDom) {
            $this->output->writeln('Way: ' . $wayDom->getAttribute('id') . ' ');
            
            $way = $this->updateWay($wayDom);
            
            /**
             * @var Symfony\Component\DomCrawler\Crawler $wayNode
             */
            foreach($this->getWayNodes($wayDom) as $wayNode) {
                $this->output->writeln(sprintf('lat: %s, lon: %s', $wayNode->attr('lat'), $wayNode->attr('lon') ));
                $this->updateNode($wayNode->getNode(0), $way);
            }
            
            $this
                ->getDoctrine()
                ->getEntityManager()
                ->flush();
            
            $this->output->writeln('');
        }
    }

        protected function filterNodes($mapNodes)
    {
        $nodes = array();
        
        foreach ($mapNodes as $domElement) {
            if($domElement->parentNode->nodeName === 'node') {
                
                $parentNode = $domElement->parentNode;

                $this
                    ->getOutput()
                    ->writeln(sprintf(
                            'Node: %s, lat: %s, lon: %s', 
                            $parentNode->getAttribute('id'), 
                            $parentNode->getAttribute('lat'), 
                            $parentNode->getAttribute('lon')
                            )
                        );
                $nodes[] = $parentNode;
            }
        }
        
        return $nodes;
    }
    
    protected function filterWays($mapNodes)
    {
        $wayList = array();
        
        foreach ($mapNodes as $domElement) {
            if($domElement->parentNode->nodeName === 'way') {
                $wayList[] = $domElement->parentNode;
            }
        }
        
        return $wayList;
    }
    
    protected function getWayNodes(\DOMElement $domElement)
    {
        $nodeIdList = array();
        foreach($domElement->childNodes as $childNode) {
            
            /*$this
                    ->getOutput()
                    ->write($childNode->nodeName);*/
            
            if($childNode->nodeName === 'nd') {
                /*$this
                    ->getOutput()
                    ->write($childNode->nodeName);*/
                
               $nodeIdList[] = $this->findById($childNode->getAttribute('ref'));
            }
        }
        
        return $nodeIdList;
    }

    protected function findById($id)
    {
        return $this->getCrawler()->filterXPath("//*[@id='" .$id  . "']");
    }


    /**
     * 
     * @return OutputInterface
     */
    protected function getOutput()
    {
        return $this->output;
    }
    
    /**
     * 
     * @return Crawler
     */
    protected function getCrawler()
    {
        return $this->crawler;
    }
    
    protected function getXMLData($url)
    {
        $cacheFileName = md5($url);
        $cacheFilePath = $this->getContainer()->get('kernel')->getRootDir() . '/data/' . $cacheFileName;

        if (file_exists($cacheFilePath)) {
            $response = file_get_contents($cacheFilePath);
        } else {
            $response = file_get_contents($url);
            file_put_contents($cacheFilePath, $response);
        }

        return $response;
    }
    
    /**
     * 
     * @param type $wayNode
     * @return \Enginewerk\MapdzillaBundle\Entity\Way
     */
    protected function updateWay($wayNode)
    {
        $wayId = $wayNode->getAttribute('id');
        
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getEntityManager();

        $way = $doctrine
                ->getRepository('EnginewerkMapdzillaBundle:Way')
                ->findByOsmWayId($wayId);
        
        if (!$way) {
            $way = new Way();
            $way->setOsmWayId($wayId);
            
            $em->persist($way);
        }
        
        return $way;
    }
    
    /**
     * 
     * @param type $domNode
     * @param \Enginewerk\MapdzillaBundle\Entity\Way $way
     * @return \Enginewerk\MapdzillaBundle\Entity\Node
     */
    protected function updateNode(\DOMElement $domNode, $way = null)
    {
        $nodeId = $domNode->getAttribute('id');
        $lat = $domNode->getAttribute('lat');
        $lon = $domNode->getAttribute('lon');
        
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getEntityManager();

        $node = $doctrine
                ->getRepository('EnginewerkMapdzillaBundle:Node')
                ->findByOsmNodeId($nodeId);
        
        if (!$node) {
            $node = new Node();
            $node->setOsmNodeId($nodeId);
            $node->setLat($lat);
            $node->setLon($lon);
            $node->setWay($way);
            
            $em->persist($node);
        }
        
        return $node;
    }
    
    /**
     * 
     * @return type
     */
    protected function getDoctrine() 
    {
        return $this
                ->getContainer()
                ->get('doctrine');
    }
}
