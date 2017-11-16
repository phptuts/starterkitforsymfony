<?php

namespace StarterKit\StartBundle\Model\Response;

/**
 * Class ResponseModel
 * @package StarterKit\StartBundle\Model
 */
class ResponseModel implements ResponseModelInterface
{

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $type;


    /**
     * ResponseModel constructor.
     * @param array $data
     * @param string $type
     */
    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Create a response envelope that wraps the data.
     *
     * @return array
     */
    public function getBody()
    {
        return [
            'meta' => [
                'type' => $this->type,
                'paginated' => false
            ],
            'data' => $this->data
        ];
    }
}