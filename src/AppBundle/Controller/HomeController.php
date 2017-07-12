<?php

namespace AppBundle\Controller;

use CoreBundle\Entity\Color;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeController
 * @package AppBundle\Controller
 */
class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @return Response
     */
    public function indexAction()
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
     * @Route("/change_color", name="change_color_page")
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

        $color = empty($color) ? new Color() : $color;
        $color->setColor("RED");

        $this->getDoctrine()->getManager()->persist($color);
        $this->getDoctrine()->getManager()->flush();


        return $this->render('@App/home/home.html.twig', [
            'color' => $color
        ]);
    }


}
