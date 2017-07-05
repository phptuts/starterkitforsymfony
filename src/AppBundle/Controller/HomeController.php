<?php

namespace AppBundle\Controller;

use CoreBundle\Entity\Color;
use CoreBundle\Entity\User;
use Facebook\Exceptions\FacebookAuthenticationException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeController
 * @package AppBundle\Controller
 */
class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /**
         * This is a example of using cache regions
         */
        $repo = $this->getDoctrine()->getRepository(Color::class);

        // This query should come from redis cache region
        $color = $repo->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameters(['id' => 1])
            ->getQuery()
            ->setCacheable(true)
            ->useResultCache(true)
            ->setCacheRegion('region_colors')
            ->getOneOrNullResult();

        return $this->render('@App/home/home.html.twig', [
            'color' => $color
        ]);
    }

    /**
     * @Route("/blah", name="blah_page")
     *
     * @return Response
     */
    public function blahAction() {

        $repo = $this->getDoctrine()->getRepository(Color::class);

        $color = $repo->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameters(['id' => 1])
            ->getQuery()
            ->setCacheable(true)
            ->useResultCache(true)
            ->setCacheRegion('region_colors')
            ->getOneOrNullResult();

        $color->setColor("Green");

        $this->getDoctrine()->getManager()->persist($color);
        $this->getDoctrine()->getManager()->flush();


        return $this->render('@App/home/home.html.twig', [
            'color' => $color
        ]);
    }

    /**
     * @Route("/blah_1/{token}", name="blah_page_1")
     *
     * @return Response
     */
    public function blah2Action($token) {


        $client = $this->get('startsymfony.core.google_client_factory')->getGoogleClient();

        dump($client->verifyIdToken($token));


      //  dump(file_get_contents($fbUrl));

        die();
    }
}
