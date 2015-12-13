<?php

defined('WEKIT_VERSION') || exit('Forbidden');

class App_Dimdb_ConfigDo
{
    public function getAdminMenu($config)
    {
        $config += array(
            'app_dimdb' => array('WindDB', 'app/manage/*?app=dimdb', '', '', 'appcenter'),
        );
        return $config;
    }
}
