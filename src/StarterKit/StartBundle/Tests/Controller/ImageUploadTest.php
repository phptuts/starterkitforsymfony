<?php


namespace StarterKit\StartBundle\Tests\Controller;


use Mockery\Mock;
use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Controller\UserController;
use StarterKit\StartBundle\Service\AuthResponseService;
use StarterKit\StartBundle\Service\FormSerializer;
use StarterKit\StartBundle\Service\S3Service;
use StarterKit\StartBundle\Service\UserService;
use StarterKit\StartBundle\Tests\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageUploadTest extends BaseApiTestCase
{
    /**
     * @var S3Service|Mock
     */
    protected $s3Service;

    /**
     * @var UserService|Mock
     */
    protected $userService;

    /**
     * @var FormSerializer|Mock
     */
    protected $formSerializer;

    /**
     * @var AuthResponseService|Mock
     */
    protected $authResponseService;

    /**
     * @var UserController
     */
    protected $userController;

    public function setUp()
    {
        parent::setUp();

        $this->s3Service = \Mockery::mock(S3Service::class);
        $this->userService = \Mockery::mock(UserService::class);
        $this->authResponseService = \Mockery::mock(AuthResponseService::class);
        $this->formSerializer = \Mockery::mock(FormSerializer::class);

        $this->userController = new UserController(
            $this->getContainer()->get('StarterKit\StartBundle\Service\FormSerializer'),
            $this->userService,
            $this->authResponseService,
            $this->s3Service
            );

        $this->userController->setContainer($this->getContainer());
    }

    public function testUploadImage()
    {
        $user = new User();
        $user->setEmail('blue@gmail.com');

        $this->setObjectId($user, 444);

        $image = new UploadedFile(
            dirname(__FILE__)  . '/../Mock/valid_image.png',
            'valid_image.png',
            'image/png',
            filesize(dirname(__FILE__)  . '/../Mock/valid_image.png'),
            null,
            true
        );

        $request = Request::create('/api/users/444', Request::METHOD_POST);
        $request->files->set('image',  $image);

        $this->s3Service
            ->shouldReceive('uploadFile')
            ->with(\Mockery::type(UploadedFile::class), 'profile_pics', md5(444 .'_profile_id'))
            ->once()
            ->andReturn('url');


        $this->userService
            ->shouldReceive('findUserById')
            ->with(444)
            ->once()
            ->andReturn($user);

        $this->userService
            ->shouldReceive('save')
            ->with(\Mockery::on(function (User $user) {
                Assert::assertEquals('url', $user->getImageUrl());
                return true;
            }))
            ->once();

        $response = $this->userController->imageAction($request, 444);

        Assert::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testImageTooLarge()
    {
        $user = new User();
        $user->setEmail('blue@gmail.com');

        $this->setObjectId($user, 444);

        $image = new UploadedFile(
            dirname(__FILE__)  . '/../Mock/image_10Mb.jpg',
            'valid_image.png',
            'image/png',
            filesize(dirname(__FILE__)  . '/../Mock/image_10Mb.jpg'),
            null,
            true
        );

        $request = Request::create('/api/users/444', Request::METHOD_POST);
        $request->files->set('image',  $image);

        $this->s3Service
            ->shouldReceive('uploadFile')
            ->withAnyArgs()
            ->never();


        $this->userService
            ->shouldReceive('findUserById')
            ->with(444)
            ->once()
            ->andReturn($user);

        $this->userService
            ->shouldReceive('save')
            ->withAnyArgs()
            ->never();

        $response = $this->userController->imageAction($request, 444);
        Assert::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        Assert::assertNotEmpty($json['data']['children']['image']['errors'][0]);

    }
}