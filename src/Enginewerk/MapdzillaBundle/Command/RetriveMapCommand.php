<?php

namespace Enginewerk\MapdzillaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Description of RetriveMapCommand
 *
 * @author Paweł Czyżewski <pawel.czyzewski@enginewerk.com>
 */
class RetriveMapCommand extends ContainerAwareCommand
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
            ->setName('mapdzilla:retrive-map')
            ->setDescription('Parses OSM file')
            ->addArgument('bbox', InputArgument::REQUIRED, 'FROM 14.4944,53.4093,14.5268,53.4167')
        ;
        //14.4976,53.4254,14.5339,53.4416
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $bbox = explode(',', $input->getArgument('bbox'));
        $coorindates = $this->getGridCoordinates($bbox);

        $progress = $this->getHelperSet()->get('progress');
        $progress->start($output, count($coorindates));

        foreach ($coorindates as $coordinate) {

            $url = sprintf(
                    'http://api.openstreetmap.org/api/0.6/map?bbox=%s,%s',
                    $coordinate['l-lon'] . ',' . $coordinate['l-lat'],
                    $coordinate['r-lon'] . ',' . $coordinate['r-lat']

                    );
            //$output->writeln($url);
            $this->getXMLData($url);
            $progress->advance();
        }

    }

    protected function getGridCoordinates($bbox)
    {
        $leftTopLatitude = $bbox[1]; // Szerokość
        $leftTopLongitude= $bbox[0];

        $rightBottomLatitude = $bbox[3];
        $rightBottomLongitude = $bbox[2];

        //$latStep = abs($leftTopLatitude - $rightBottomLatitude) / 0.04;
        //$lonStep = abs($leftTopLongitude - $rightBottomLongitude) / 0.02; // 18.44
        $latLonList = array();
        for ($lat = $leftTopLatitude; $lat <= $rightBottomLatitude; $lat+=0.04) {
            //$this->output->writeln($lat . ':' . $lon);
            for ($lon = $leftTopLongitude; $lon <= $rightBottomLongitude; $lon+=0.02) {
                $latLonList[] = array('l-lat' => $lat, 'l-lon' => $lon, 'r-lat' => $lat + 0.04, 'r-lon' => $lon + 0.02);
                //$this->output->writeln(implode(',', array('l-lon' => $lon, 'l-lat' => $lat, 'r-lon' => $lon + 0.02, 'r-lat' => $lat + 0.04)));
            }
        }

        return $latLonList;
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
