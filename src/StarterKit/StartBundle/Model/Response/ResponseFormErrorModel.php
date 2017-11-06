<?php


namespace StarterKit\StartBundle\Model\Response;


class ResponseFormErrorModel implements ResponseModelInterface
{

    /**
     * The array of form errors
     *
     * @var array
     */
    private $errors;

    public function __construct($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return [
            'meta' => [
                'type' => 'formErrors',
                'paginated' => false,
            ],
            'data' => $this->errors
        ];
    }

}