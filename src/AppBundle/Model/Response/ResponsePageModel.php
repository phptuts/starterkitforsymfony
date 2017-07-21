<?php


namespace AppBundle\Model\Response;


use Doctrine\ORM\Tools\Pagination\Paginator;

class ResponsePageModel implements ResponseModelInterface
{
    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $page;

    public function __construct(Paginator $paginator, $type, $page)
    {
        $this->paginator = $paginator;
        $this->type = $type;
        $this->page = $page;
    }

    /**
     * Returns an array representing the response model
     *
     * @return array
     */
    public function getBody()
    {

        $total = $this->paginator->count();
        $result = $this->paginator->getQuery()->getResult();
        $maxPages = ceil($total / $this->paginator->getQuery()->getMaxResults());

        return [
            'meta' => [
                'type' => $this->type,
                'paginated' => true,
                'total' => (int)$total,
                'page' => (int)$this->page,
                'pageSize' => count($result),
                'totalPages' => (int)$maxPages
            ],
            'data' => $result
        ];
    }


}