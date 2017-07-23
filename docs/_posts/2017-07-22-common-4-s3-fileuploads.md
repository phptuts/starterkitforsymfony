---
layout: page
title: "Amazon S3 File Uploads"
category: common
date: 2017-07-22 15:12:31
order: 4
disqus: 1
---

We use S3 service to store files.  It is very popular and affordable.  The way it works is you create a bucket that 
the website will use to store all the files.  Once the bucket is created you can create folder in the bucket to store
 the files.  You can also assign permission and do a bunch more.  See the helpful link for S3.

### [S3 Client Factory](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Factory/S3ClientFactory.php)

We use the php library amazon provides to assemble s3 client.  We need a secret, key, the region the bucket is on, and 
amazon api version.  

### [S3 Service](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Service/S3Service.php)

The main function is the uploadFile function.  This takes in the file we are uploading, the folder path in the bucket
 and the filename.  The environment name will be the first folder in the bucket.  So if you are on the dev 
 environment the file will be uploaded to the dev folder.  
 
 An example would be the profile picture
 
 ```
 $url = $this->s3Service->uploadFile($user->getImage(), 'profile_pics', md5($user->getId() . '_profile_id'));
```

In our dev environment example this would be uploaded to the dev/profile_pics/ folder.

### Helpful Links

- [S3 Documentation PHP](https://aws.amazon.com/documentation/sdk-for-php/)
- [Symfony UploadedFile](http://api.symfony.com/master/Symfony/Component/HttpFoundation/File/UploadedFile.html)


