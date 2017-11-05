<?php

namespace AppBundle\Controller\Main;

use AppBundle\Entity\User;
use AppBundle\Form\User\ChangePasswordType;
use AppBundle\Form\User\UpdateUserType;
use AppBundle\Service\S3Service;
use AppBundle\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AccountSettingController
 * @package AppBundle\Controller
 */
class AccountSettingController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var S3Service
     */
    private $s3Service;

    public function __construct(UserService $userService, S3Service $s3Service)
    {
        $this->userService = $userService;
        $this->s3Service = $s3Service;
    }

    /**
     * @Route("/account-settings/information", name="update_user")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     *
     * @return Response
     */
    public function updateUserAction(Request $request)
    {
        $form = $this->createForm(UpdateUserType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $form->getData();
            if (!empty($user->getImage())) {
                $url = $this->s3Service->uploadFile($user->getImage(), 'profile_pics', md5($user->getId() . '_profile_id'));
                $user->setImageUrl($url);
            }

            $this->userService->save($user);
            $this->addFlash('success', 'Your profile was successfully updated!');
        }

        return $this->render('@App/main/account-settings/update-user.html.twig', [
            'updateUserForm' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @Security("has_role('ROLE_USER')")
     * @Route("/account-settings/change-password", name="change_password")
     * @return Response
     */
    public function changePasswordAction(Request $request)
    {
        $form = $this->createForm(ChangePasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $this->getUser();
            $user->setPlainPassword($form->get('newPassword')->getData());
            $this->userService->saveUserWithPlainPassword($user);
            $this->addFlash('success', 'Your password was updated!');
        }

        return $this->render('@App/main/account-settings/change-password.html.twig', [
            'changePasswordForm' => $form->createView()
        ]);
    }
}