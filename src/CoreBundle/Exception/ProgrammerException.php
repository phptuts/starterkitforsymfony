<?php

namespace CoreBundle\Exception;

/**
 * Class ProgrammerException
 * @package CoreBundle\Exception
 */
class ProgrammerException extends \Exception
{
    /**
     * This is used when duplicate forget password tokens are in the database
     * @var integer
     */
    const FORGET_PASSWORD_TOKEN_DUPLICATE_EXCEPTION_CODE = 10233233;

    /**
     * This is used when plain password is not on the entity of the user
     * @var integer
     */
    const NO_PLAIN_PASSWORD_ON_USER_ENTITY_EXCEPTION_CODE = 10233234;
}