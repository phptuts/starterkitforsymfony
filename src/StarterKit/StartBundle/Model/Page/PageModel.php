<?php

namespace StarterKit\StartBundle\Model\Page;


class PageModel
{
    /**
     * @var integer
     */
    private $total;

    /**
     * @var array
     */
    private $results;

    /**
     * The current Page Number
     * @var integer
     */
    private $currentPage;

    /**
     * The Max Results Per Page
     * @var integer
     */
    private $pageSize;
    private $type;

    /**
     * PagedResultModel constructor.
     * @param int $total
     * @param array $results
     * @param int $currentPage
     * @param  int $pageSize
     */
    public function __construct(array  $results, $currentPage, $total, $pageSize, $type)
    {
        $this->total = $total;
        $this->results = $results;
        $this->currentPage = $currentPage;
        $this->pageSize = $pageSize;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * Returns the max number of pages
     *
     * @return integer
     */
    public function numberOfPages()
    {
        return ceil($this->total / $this->pageSize);
    }

    /**
     * @return boolean
     */
    public function lastPage()
    {
        return $this->numberOfPages() == $this->currentPage;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }
}