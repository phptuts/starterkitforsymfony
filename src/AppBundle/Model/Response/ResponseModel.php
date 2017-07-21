<?php

namespace AppBundle\Model\Response;

/**
 * Class ResponseModel
 * @package AppBundle\Model
 */
class ResponseModel implements ResponseModelInterface
{
    /**
     * This means that it is a type jws response
     * @var string
     */
    const CREDENTIAL_RESPONSE = 'credentials';

    /**
     * This is the response for when we serialize a user
     * @var string
     */
    const USER_RESPONSE = 'user';

    /**
     * @var mixed
     */
    private $data;



    /**
     * ResponseModel constructor.
     * @param ResponseTypeInterface $data this is data be serialized
     */
    public function __construct($data)
    {
        $this->data = $data;
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
                'type' => $this->data->getResponseType(),
                'paginated' => false
            ],
            'data' => $this->data
        ];
    }
}