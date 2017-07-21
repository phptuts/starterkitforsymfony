<?php


namespace AppBundle\Controller\Api;


use AppBundle\Exception\ProgrammerException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ExceptionExampleController extends AbstractRestController
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