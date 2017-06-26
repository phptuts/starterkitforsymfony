<?php

namespace AppBundle\Controller;

use CoreBundle\Entity\Color;
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
            ->where('c.color = :color')
            ->setParameters(['color' => 'Blue'])
            ->getQuery()
            ->useResultCache(true)
            ->getOneOrNullResult();

        return $this->render('AppBundle::layout.html.twig', [
            'color' => $color
        ]);
    }
}
