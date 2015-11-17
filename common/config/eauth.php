<?php

    return [
        'class' => 'nodge\eauth\EAuth',
        'popup' => true, // Use the popup window instead of redirecting.
        'cache' => false, // Cache component name or false to disable cache. Defaults to 'cache' on production environments.
        'cacheExpire' => 0, // Cache lifetime. Defaults to 0 - means unlimited.
        'httpClient' => array(
//             uncomment this to use streams in safe_mode
//        'useStreamsFallback' => true,
        ),
        'services' => array( // You can change the providers and their classes.
            'facebook' => array(
                // register your app here: https://developers.facebook.com/apps/
                'class' => 'common\components\FacebookOAuth2Service',
                'clientId' => '',
                'clientSecret' => '',
            ),
            'vkontakte' => array(
                // register your app here: https://vk.com/editapp?act=create&site=1
                'class' => 'common\components\VKontakteOAuth2Service',
                'clientId' => '',
                'clientSecret' => '',
            ),
            'odnoklassniki' => array(
                // register your app here: http://dev.odnoklassniki.ru/wiki/pages/viewpage.action?pageId=13992188
                // ... or here: http://www.odnoklassniki.ru/dk?st.cmd=appsInfoMyDevList&st._aid=Apps_Info_MyDev
                'class' => 'common\components\OdnoklassnikiOAuth2Service',
                'clientId' => '',
                'clientSecret' => '',
                'clientPublic' => '',
            ),
        ),
    ];
