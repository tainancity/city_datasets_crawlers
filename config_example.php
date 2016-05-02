<?php
return array(
    'sites' => array(
        'tainan' => array(
            'class' => 'Silex\ckan\CkanClient',
            'params' => array(
                'baseUrl' => '{scheme}://data.tainan.gov.tw/api/',
                'scheme' => 'http',
                'apiKey' => ''
            )
        ),
        'nantou' => array(
            'class' => 'Silex\ckan\CkanClient',
            'params' => array(
                'baseUrl' => '{scheme}://data.nantou.gov.tw/api/',
                'scheme' => 'http',
                'apiKey' => '',
            ),
        ),
        'hccg' => array(
            'class' => 'Silex\ckan\CkanClient',
            'params' => array(
                'baseUrl' => '{scheme}://opendata.hccg.gov.tw/api/',
                'scheme' => 'http',
                'apiKey' => '',
            ),
        ),
        'taoyuan' => array(
            'class' => 'Silex\ckan\CkanClient',
            'params' => array(
                'baseUrl' => '{scheme}://ckan.tycg.gov.tw/api/', //ckan {scheme}://ckan.tycg.gov.tw/api/
                'scheme' => 'http',
                'apiKey' => '',
            ),
        ),
        'taipei' => array(
            'class' => 'Silex\ckan\CkanClient',
            'params' => array(
                'baseUrl' => 'http://data.taipei/opendata/datalist/apiAccess',
                //'baseUrl' => 'http://163.29.157.32:8080/api/',
                'scheme' => 'http',
                'apiKey' => '',
            ),
        ),
    ),
);