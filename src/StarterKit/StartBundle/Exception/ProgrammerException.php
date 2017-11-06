<?php

namespace StarterKit\StartBundle\Exception;

/**
 * Class ProgrammerException
 * @package StarterKit\StartBundle\Exception
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

    /**
     * The invalid error for facebook
     * @var integer
     */
    const FACEBOOK_RESPONSE_EXCEPTION_CODE = 10233235;

    /**
     * This mean that there was an error in the facebook sdk
     * @var integer
     */
    const FACEBOOK_SDK_EXCEPTION_CODE = 10233236;


    /**
     * This mean that there was an error in the facebook sdk
     * @var integer
     */
    const FACEBOOK_PROVIDER_EXCEPTION = 10233237;

    /**
     * This mean that the GOOGLE USER PROVIDER received a logic error
     * @var integer
     */
    const GOOGLE_USER_PROVIDER_LOGIC_EXCEPTION = 10233238;

    /**
     * general exception for user provider
     * @var integer
     */
    const GOOGLE_USER_PROVIDER_EXCEPTION = 10233239;

    /**
     * This is the error code for no user provider implemented
     * @var integer
     */
    const NO_TOKEN_PROVIDER_IMPLEMENTED = 10233240;

    /**
     * This means that the token format was not valid and un readable
     * @var integer
     */
    const JWS_INVALID_TOKEN_FORMAT = 10233241;

    /**
     * This means that there are duplicate refresh tokens in our system.
     * @var integer
     */
    const REFRESH_TOKEN_DUPLICATE = 10233242;

    /**
     * No docs lol.
     * @var integer
     */
    const STUPID_EXCEPTION = 10233243;

}