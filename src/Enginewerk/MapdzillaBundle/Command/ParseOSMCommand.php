<?php

namespace Enginewerk\MapdzillaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\DomCrawler\Crawler;

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
            ->setDescription('Parses OSM file')
            ->addArgument('bbox', InputArgument::REQUIRED, 'FROM 14.5317,53.4205 TO 14.5653,53.4311')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        
        //$mapfile = $this->getContainer()->get('kernel')->getRootDir() . '/data/map.osm';
        //$xmlData = file_get_contents($mapfile);
        
        $output->write('<info>Dowloading data...</info>');
        $url = sprintf('http://api.openstreetmap.org/api/0.6/map?bbox=%s', $input->getArgument('bbox'));
        $xmlData = $this->getXMLData($url);
        $output->writeln(strlen($xmlData) . ' bytes');
        
        $this->crawler = new Crawler($xmlData);
        //$mapNodes = $crawler->filter('osm > way > tag[v=parking]');
        $mapNodes = $this
                ->getCrawler()
                ->filter('tag[v=parking]');
        
        //$mapNodes = $crawler->filter('osm > node')->siblings();

        $output->writeln('<info>Nodes</info>');    
        $this->filterNodes($mapNodes);
        
        $output->writeln('<info>Ways</info>');
        $ways = $this->filterWays($mapNodes);
        
        foreach($ways as $way) {
            $output->writeln('Way: ' . $way->getAttribute('id') . ' ');
            foreach($this->getWayNodes($way) as $wayNode) {
                $output->writeln(sprintf('lat: %s, lon: %s', $wayNode->attr('lat'), $wayNode->attr('lon') ));
            }
            $output->writeln('');
        }

    }
    
    protected function filterNodes($mapNodes)
    {
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
            }
        }
    }
    
    protected function filterWays($mapNodes)
    {
        $wayList = array();
        
        foreach ($mapNodes as $domElement) {
            if($domElement->parentNode->nodeName === 'way') {
                /*$this
                    ->getOutput()
                    ->write($domElement->parentNode->nodeName);
                
                $this
                    ->getOutput()
                    ->writeln(' ' . $domElement->parentNode->getAttribute('id'));*/
                
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
}
