---
layout: page
title: "Credential Response"
category: api
date: 2017-07-22 15:12:31
order: 5
disqus: 1
---


## What is a credential Response

It's what I call a response that has everything a client need to login as that user.  It will have a jwt token with an expiration date, a refresh token with it's expiration date, and a serialized user.  The client should have to decode the token.  

```
 {
        "meta": {
            "type": "credentials",
            "paginated": false
        },
        "data": {
            "user": {
                "id": "96430bcc-6987-11e7-9d99-08002732ed09",
                "displayName": "update_user_e2e",
                "email": "update_user_e2e@email.com",
                "bio": null
            },
            "tokenModel": {
                "token": "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJ1c2VyX2lkIjoiOTY0MzBiY2MtNjk4Ny0xMWU3LTlkOTktMDgwMDI3MzJlZDA5IiwiZXhwIjoxNTA1NDE1ODcxLCJpYXQiOjE1MDAyMzE4NzF9.vKuQmpOFPneh38vFnT7BJPqT89gaIq8MEcL4SrDUHvQ8Jpq0z-JVEex8vbSKJFORFwPGnw2X4xWgx-qs39C0T06oknn2fHF-jLOafwjwCRLTDeOyrDT6JX2sNxEirfS1kzvXL_lA74JuZO8g1twmjHiFSlvk2j32ueo9VnZZdisHvYHnl2zy8mgme3A8izKQsgw2UHBsSPy6x4fe80dWnf60Wp5NPZkBRtAPitE4SLktnJEVo93aSzUPVQiDfKPdA4J0zE7UfsmkDIqMflOIZI_CSCuKGJ77q8WWcziH47P_Qv4hF93s19hI9PAb1mMv75LrVc82JrftHyRC_wk_LF1J6al7lcKNWv9paw0VLJVHz-qBRY-LOFwkUzQNMZetXab_VA_FPTeR0itHZDku5Et64clb9_TzFeveQ7Q0W2yakPsFCDK24a1SxTqzVXMKSAiecQK6oFsSTSsDEekKlkrpXshHN3LlQ_OnDAyp-J8Bzl90MAE2VlP-WFEpNnFzH3G6apTkQ31RYNaV6EFC-TOv_rMmKvM9O0E7NezSPEs15jGSVEzd_I5Q44GkEij1mPij-F1pqjvVbbD81_MZZIon8QsS9hTWjCqHUxzAvoSZ_y7nheYGzwzxWc_dz2qN8v1ragbQrLAaUST12TLIAVE22Q_JPhHmI0wQi0u95Kk",
                "expirationTimeStamp": 1505415871
            },
            "refreshTokenModel": {
                "token": "6fd9225321cc4867ff9c7f77cd748f23ce9a5186e6dbbae4f4a720aab7a7879bb9af60669e1fca45bf0d9a3033ff6f9a07a06c50996fa8406dcff2ecd2ba0955f994aa24d3b667dcf28e24f4d23fda666cf8d7a155ddef701796",
                "expirationTimeStamp": 1510599871
            }
        }
    }

```

## Clients / Api Consumers should not read the token

The reason is that some client like iphone can't change without updates.  So if you wanted to change the token you would have to wait for an app update and do it very carefully.  This allows the api to change without affecting it's clients.  

## Workflow

1) The [JWSService](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Service/Credential/JWSService.php) is called and creates an [AuthModel](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Model/Security/AuthTokenModel.php) with a jws token and expiration date.

2) We use the [UserService](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Service/User/UserService.php) to create a refresh token on the user entity and a refresh token expiration date.  Then we call getAuthRefreshModel to get a the AuthModel for the refresh token.

3) This is all used with the user object to create a [CredentialModel](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Model/Security/CredentialModel.php) in the [CredentialModelBuilderService](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Service/Credential/CredentialModelBuilderService.php).

4) The CredentialModel is used in the [CredentialResponseBuilderService](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Service/Credential/CredentialResponseBuilderService.php) to create a response model that is serialized with the [ResponseSerializerService](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Service/ResponseSerializerService.php) with the 



