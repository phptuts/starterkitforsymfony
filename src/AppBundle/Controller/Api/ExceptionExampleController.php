<?php

namespace AppBundle\Controller\Api;

use AppBundle\Exception\ProgrammerException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as REST;

/**
 * Class UserController
 * @package ApiBundle\Controller\Api
 * @REST\NamePrefix("api_users_")
 */
class ExceptionExampleController extends BaseRestController
{
    /**
     * @REST\Get(path="stupid_exception")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="example of an exception",
     *  section="Security"
     * )
     */
    public function testStupidExceptionApiAction()
    {
        throw new ProgrammerException('I am a stupid exception.', ProgrammerException::STUPID_EXCEPTION);
    }
}