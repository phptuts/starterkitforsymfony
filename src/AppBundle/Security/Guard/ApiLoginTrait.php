<?php


namespace AppBundle\Security\Guard;


use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiLoginTrait
 * @package AppBundle\Security\Guard
 */
trait ApiLoginTrait
{

    /**
     * A common way of handling api login requests.
     * 1) Validate that the request is valid
     * 2) Validate that all the fields are set with a non null value
     *
     *
     * @param Request $request
     * @param array $fields
     * @return mixed|null
     */
    protected function getLoginCredentials(Request $request, $fields)
    {
        // We check that they are hitting the api login end point and that the request is a post
        if ($request->getPathInfo() != '/api/login_check' || !$request->isMethod(Request::METHOD_POST)) {
            return null;
        }

        $post = json_decode($request->getContent(), true);

        // If it does not have all the fields return null.
        if (!$this->hasAllFields($post, $fields)) {
            return null;
        }

        return $post;
    }

    /**
     * Validates that's all the fields are in the post data.
     *
     * @param array $post the json post data in an array
     * @param array $fields all the fields that should be in the post data
     * @return bool
     */
    protected function hasAllFields($post, $fields)
    {
        foreach ($fields as $field) {
            if (empty($post[$field])) {
                return false;
            }
        }

        return true;
    }

}