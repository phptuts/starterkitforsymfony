<?php

namespace AppBundle\Controller;

use CoreBundle\Entity\Color;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Color::class);


        $color = $repo->createQueryBuilder('c')
            ->where('c.color = :color')
            ->setParameters(['color' => 'blue'])
            ->getQuery()
            ->useResultCache(true)
            ->getOneOrNullResult();

        return $this->render('AppBundle::layout.html.twig', [
            'color' => $color
        ]);
    }

}
