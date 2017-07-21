<?php

namespace AppBundle\Controller\Api;


use AppBundle\Form\User\UpdateUserType;
use AppBundle\Form\User\UserImageType;
use AppBundle\Security\Voter\UserVoter;
use AppBundle\Service\ResponseSerializerService;
use AppBundle\Service\S3Service;
use AppBundle\Service\User\UserService;
use FOS\RestBundle\Controller\Annotations as REST;
use AppBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package ApiBundle\Controller
 * @REST\NamePrefix("api_users_")
 */
class UserController extends AbstractRestController
{

    /**
     * @var UserService
     */
    private $userService;
   
    /**
     * @var S3Service
     */
    private $s3Service;

    public function __construct(ResponseSerializerService $responseSerializer, S3Service $s3Service, UserService $userService){
        parent::__construct($responseSerializer);
        $this->userService = $userService;
        $this->s3Service = $s3Service;
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
     * @REST\Post("users/{id}/image")
     * @ParamConverter(name="user", class="AppBundle:User")
     *
     * @param Request $request
     * @param User $user
     *
     * @return Response|FormInterface
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

        return $form;
    }

    /**
     * @Security("has_role('ROLE_USER')")
     *
     * <p>This updates the user.  Whatever user field you have.</pre>
     * <pre> {"displayName": "jo32", "email": "example@sdf.com" }</pre>
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Update's the user",
     *  section="Users",
     *  authentication=true
     * )
     * @REST\View()
     *
     * @param Request $request
     * @param User $user
     *
     * @REST\Patch(path="users/{id}")
     * @ParamConverter(name="user", class="AppBundle:User")
     *
     * @return FormInterface|Response
     */
    public function updateUserAction(Request $request, User $user)
    {
        $form = $this->createForm(UpdateUserType::class, $user);

        $form->submit($request->request->all());


        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $this->userService->save($user);

            return $this->serializeSingleObject($user, [User::USER_PERSONAL_SERIALIZATION_GROUP], Response::HTTP_OK);
        }

        return $form;
    }


    /**
     * @Security("has_role('ROLE_USER')")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get's a user",
     *  section="Users",
     *  authentication=true
     * )
     *
     * @REST\View()
     * @REST\Get(path="users/{id}")
     *
     * @ParamConverter(name="user", class="AppBundle:User")
     *
     * @param User $user
     *
     * @return Response
     */
    public function getUserAction(User $user)
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_CAN_VIEW_EDIT, $user);

        return $this->serializeSingleObject($user, [User::USER_PERSONAL_SERIALIZATION_GROUP], Response::HTTP_OK);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get's a list of users, admin only.",
     *  section="Users",
     *  authentication=true
     * )
     *
     * @REST\View()
     * @REST\Get(path="users")
     *
     * @REST\QueryParam(name="q", description="The search query", nullable=true)
     * @REST\QueryParam(name="page", description="The current page ", nullable=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getUsersAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $users = $this->userService->searchUser(
                $request->query->get('q'),
                $page
            );

        return $this->serializeList($users, 'users', $page, [User::USER_PERSONAL_SERIALIZATION_GROUP], Response::HTTP_OK);
    }

}