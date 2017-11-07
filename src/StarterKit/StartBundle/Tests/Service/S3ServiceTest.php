<?php


namespace StarterKit\StartBundle\Tests\Service;

use Aws\Result;
use Aws\S3\S3Client;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Factory\S3ClientFactory;
use StarterKit\StartBundle\Service\S3Service;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\BaseTestCase;

class S3ServiceTest extends BaseTestCase
{
    /**
     * @var S3Client|Mock
     */
    protected $s3Client;

    /**
     * @var S3Service
     */
    protected $s3Service;

    public function setUp()
    {
        parent::setUp();
        $s3ClientFactory = \Mockery::mock(S3ClientFactory::class);
        $this->s3Client = \Mockery::mock(S3Client::class);
        $s3ClientFactory->shouldReceive('createClient')->once()->andReturn($this->s3Client);
        $this->s3Service = new S3Service($s3ClientFactory, 'bucket_name', 'dev');
    }

    /**
     * Tests that upload works and that we get can back a url to put into the db
     */
    public function testUpload()
    {
        $uploadedFile = \Mockery::mock(UploadedFile::class);
        $uploadedFile->shouldReceive('getRealPath')->andReturn('path');
        $uploadedFile->shouldReceive('guessClientExtension')->andReturn('png');

        $result = \Mockery::mock(Result::class);
        $result->shouldReceive('get')->with('ObjectURL')->andReturn('url');

        $this->s3Client->shouldReceive('putObject')->with([
            'ACL' => 'public-read',
            'Bucket' => 'bucket_name',
            'SourceFile' => 'path',
            'Key' => 'dev/profile_pics/user1.png'
        ])->andReturn($result);


        Assert::assertEquals('url',$this->s3Service->uploadFile($uploadedFile, 'profile_pics', 'user1'));


    }
}