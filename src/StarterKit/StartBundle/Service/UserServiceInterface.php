<?php

namespace StarterKit\StartBundle\Service;


use StarterKit\StartBundle\Entity\BaseUser;
use StarterKit\StartBundle\Model\Page\PageModel;

interface UserServiceInterface
{
    /**
     * Finds a user by email
     *
     * @param $email
     * @return BaseUser|null
     */
    public function findUserByEmail($email);

    /**
     * Finds a user by their id
     *
     * @param $id
     * @return BaseUser|null|object
     */
    public function findUserById($id);


    /**
     * Finds a user by their facebook user id
     *
     * @param $facebookUserId
     * @return BaseUser|null|object
     */
    public function findByFacebookUserId($facebookUserId);


    /**
     * Finds a user by their google user id
     *
     * @param $googleUserId
     * @return BaseUser|null|object
     */
    public function findByGoogleUserId($googleUserId);


    /**
     * Return a paginator of users
     *
     * @param $searchTerm
     * @param int $page
     * @param int $limit
     * @return PageModel
     */
    public function searchUser($searchTerm, $page = 1, $limit = 10);

    /**
     * Returns true if the email exists
     *
     * @param string $email
     * @return bool
     */
    public function doesEmailExist($email);


    /**
     * Finds the user by the forgot password token
     *
     * @param string $token
     * @return BaseUser|null
     */
    public function findUserByForgetPasswordToken($token);


    /**
     * Return a user with a matching refresh token
     *
     * @param $token
     * @return BaseUser|null
     */
    public function findUserByValidRefreshToken($token);



    /**
     * Saves the user with password that will be encoded
     *
     * @param BaseUser $user
     */
    public function saveUserWithPlainPassword(BaseUser $user);


    /**
     * Saves a new user to our database
     *
     * @param BaseUser $user
     * @param string $source
     *
     * @return BaseUser
     */
    public function registerUser(BaseUser $user, $source = UserService::SOURCE_TYPE_WEBSITE);


    /**
     * Creates a forget password token and sends a forget password email to the user
     * @param BaseUser $user
     *
     * @return BaseUser
     */
    public function forgetPassword(BaseUser $user);


    /**
     * Makes sure the forget password token and forget password token expiration time are set to null
     *
     * @param BaseUser $user
     */
    public function saveUserForResetPassword(BaseUser $user);


    /**
     * Saves the user with an updated refresh token
     *
     * @param BaseUser $user
     *
     * @return BaseUser
     */
    public function updateUserRefreshToken(BaseUser $user);


    /**
     * Saves an entity
     *
     * @param $entity
     */
    public function save($entity);

    /**
     * Returns the concrete user class
     *
     * @return string
     */
    public function getUserClass();

}