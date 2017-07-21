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
     *
     * @param Request $request
     * @param $fields
     * @return mixed|null
     */
    protected function getLoginCredentials(Request $request, $fields)
    {
        $post = json_decode($request->getContent(), true);
        $postFields = array_keys($post);

        // We check that they are hitting the api login end point and that the request is a post
        // That all the required fields match the request.
        if ($request->getPathInfo() == '/api/login_check' && $request->isMethod(Request::METHOD_POST) && $postFields == $fields) {
            return $post;
        }

        return null;
    }
}