<?php

namespace Tests\AppBundle\Model\Response;

use AppBundle\Model\Response\ResponsePageModel;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class ResponsePageModelTest extends BaseTestCase
{

    /**
     *
     * Tests that page model combiles under a bunch of combos.
     *
     * @dataProvider dataProviderPagedResults
     * @param Paginator $paginator
     * @param $type
     * @param $page
     */
    public function testPaginatorModel(Paginator $paginator, $type, $page)
    {
        $total = $paginator->count();
        $result = $paginator->getQuery()->getResult();
        $maxPages = ceil($total / $paginator->getQuery()->getMaxResults());

        $result = [
            'meta' => [
                'type' => $type,
                'paginated' => true,
                'total' => (int)$total,
                'page' => (int)$page,
                'pageSize' => count($result),
                'totalPages' => (int)$maxPages
            ],
            'data' => $result
        ];

        $pageResponse = new ResponsePageModel($paginator, $type, $page);

        Assert::assertEquals($result, $pageResponse->getBody());
    }

    /**
     * Tests random page combos
     */
    public function dataProviderPagedResults()
    {
        $tests = [];

        for ($i = 0; $i < 10; $i += 1) {
            $count = rand(5, 300);

            $paginator = \Mockery::mock(Paginator::class);
            $paginator->shouldReceive('count')->andReturn($count);
            $query = \Mockery::mock(AbstractQuery::class);
            $query->shouldReceive('getResult')->andReturn([1,3,4,5,44]);
            $query->shouldReceive('getMaxResults')->andReturn(5);
            $paginator->shouldReceive('getQuery')->andReturn($query);

            $tests[] = [$paginator, 'numbers', 3];

        }

        return $tests;
    }
}