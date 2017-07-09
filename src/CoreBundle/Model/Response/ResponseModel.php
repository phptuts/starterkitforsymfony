<?php

namespace CoreBundle\Model\Response;

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
    const CREDENTIAL_RESPONSE = 'credentials';

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var string
     */
    private $type;

    /**
     * ResponseModel constructor.
     * @param string $data this is data be serialized
     * @param string $type this is the type of response
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
    public function responseEnvelope()
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