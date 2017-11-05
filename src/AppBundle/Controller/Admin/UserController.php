<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;

class UserController extends Controller
{

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @Route("users", name="admin_users")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {

        $users = $this->userService->searchUser(
                $request->query->get('q'),
                $request->query->get('page', 1)
            );

        $maxPages = ceil($users->count() / UserRepository::PAGE_LIMIT);
        $currentPage = $request->query->get('page', 1);


        return $this->render('@App/admin/users/index.html.twig', [
            'users' => $users,
            'limit' => UserRepository::PAGE_LIMIT,
            'maxPages' => $maxPages,
            'currentPage' => $currentPage
        ]);
    }

    /**
     * Updates the user's email
     *
     * @param Request $request
     * @param User $user
     *
     * @Route(path="/users/{id}/email", methods={"PATCH"}, name="admin_user_email")
     * @ParamConverter(name="user", class="AppBundle:User")
     *
     * @return Response
     */
    public function updateUserEmailAction(Request $request, User $user)
    {
        $email = $request->request->get('email');


        if ($this->userService->doesEmailExist($email) && $user->getEmail() != $email) {
            return new JsonResponse(['message' => 'Email already exists.'], Response::HTTP_BAD_REQUEST);
        }

        $user->setEmail($email);

        $this->userService->save($user);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Updates the user password
     *
     * @param Request $request
     * @param User $user
     *
     * @Route(path="/users/{id}/password", methods={"PATCH"}, name="admin_user_password")
     * @ParamConverter(name="user", class="AppBundle:User")
     *
     * @return Response
     */
    public function updatePasswordAction(Request $request, User $user)
    {
        $user->setPlainPassword($request->request->get('password'));

        $this->userService->saveUserForResetPassword($user);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Toggles the user's access
     *
     * @param Request $request
     * @param User $user
     * @param string $access
     *
     * @Route(path="/users/{id}/admin-toggle/{access}", methods={"PATCH"}, name="admin_user_role_admin")
     * @ParamConverter(name="user", class="AppBundle:User")
     *
     * @return Response
     */
    public function roleAdminAction(Request $request, User $user, $access)
    {
        $user->setRoles(['ROLE_USER']);

        if ($access == 1) {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $this->userService->save($user);

        return new Response('', Response::HTTP_NO_CONTENT);
    }


    /**
     * Enables the user
     *
     * @param Request $request
     * @param User $user
     * @param int $enable 1
     *
     * @Route(path="/users/{id}/enable/{enable}", methods={"PATCH"}, name="admin_user_enable")
     * @ParamConverter(name="user", class="AppBundle:User")
     *
     * @return Response
     */
    public function updateEnableUserAction(Request $request, User $user, $enable)
    {
        $user->setEnabled($enable == 1);

        $this->userService->save($user);

        return new Response('', Response::HTTP_NO_CONTENT);

    }
}