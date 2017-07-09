<?php

namespace CoreBundle\Model;

use CoreBundle\Model\Response\ResponseModelInterface;

/**
 * Class ResponseModel
 * @package CoreBundle\Model
 */
class ResponseModel implements ResponseModelInterface
{
    /**
     * This means that it is a type jws response
     * @var string
     */
    const JWS_RESPONSE_TYPE = 'jws_response';

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var string
     */
    private $type;

    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Create a response envelope
     *
     * @return array
     */
    public function toArray()
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