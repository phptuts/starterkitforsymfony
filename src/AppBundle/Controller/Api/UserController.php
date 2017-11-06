<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use AppBundle\Service\S3Service;
use StarterKit\StartBundle\Controller\Api\BaseRestController;
use StarterKit\StartBundle\Form\UserImageType;
use StarterKit\StartBundle\Service\FormSerializer;
use StarterKit\StartBundle\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseRestController
{
    /**
     * @var S3Service
     */
    private $s3Service;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(S3Service $s3Service, FormSerializer $formSerializer, UserService $userService)
    {
        parent::__construct($formSerializer);
        $this->s3Service = $s3Service;
        $this->userService = $userService;
    }

    /**
     * @Security("has_role('ROLE_USER')")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Update the image for a user",
     *  section="Users",
     *  authentication=true,
     *  parameters={
     *      {
     *          "name"="image",
     *          "dataType"="file",
     *          "required"=true,
     *          "description"="The image profile image it can only be jpg, gif, png."
     *      }
     *  }
     *  )
     * @Route(path="users/{id}/image", methods={"POST"})
     * @ParamConverter(name="user", class="StarterKit\StartBundle:User")
     *
     * @param Request $request
     * @param User $user
     *
     * @return Response
     */
    public function imageAction(Request $request, User $user)
    {
        $form = $this->createForm(UserImageType::class, $user);

        $form->submit(['image' => $request->files->get('image')]);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = $this->s3Service->uploadFile(
                $user->getImage(),
                'profile_pics',
                md5($user->getId() . '_profile_id')
            );
            $user->setImageUrl($url);
            $this->userService->save($user);

            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $this->serializeFormError($form);
    }
}