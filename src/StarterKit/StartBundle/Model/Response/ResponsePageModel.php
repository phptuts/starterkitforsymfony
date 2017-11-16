<?php

namespace StarterKit\StartBundle\Model\Response;

use StarterKit\StartBundle\Model\Page\PageModel;

/**
 * Class ResponsePageModel
 * @package StarterKit\StartBundle\Model\Response
 */
class ResponsePageModel implements ResponseModelInterface
{
    /**
     * @var PageModel
     */
    private $pageModel;

    /**
     * ResponsePageModel constructor.
     * @param PageModel $pageModel
     */
    public function __construct(PageModel $pageModel)
    {
        $this->pageModel = $pageModel;
    }

    /**
     * Returns an array representing the response model
     *
     * @return array
     */
    public function getBody()
    {

        return [
            'meta' => [
                'type' => $this->pageModel->getType(),
                'paginated' => true,
                'total' => (int)$this->pageModel->getTotal(),
                'page' => (int)$this->pageModel->getCurrentPage(),
                'pageSize' => (int)$this->pageModel->getPageSize(),
                'numberOfPages' => (int)$this->pageModel->numberOfPages(),
                'lastPage' => (bool)$this->pageModel->lastPage()
            ],
            'data' => $this->pageModel->getResults()
        ];
    }
}