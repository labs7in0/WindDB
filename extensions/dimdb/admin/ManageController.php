<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('ADMIN:library.AdminBaseController');

class ManageController extends AdminBaseController
{
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
    }

    public function run()
    {
        $service = $this->_loadConfigService();
        $config = $service->getValues('site');
        $this->setOutput($config, 'config');
    }

    public function dorunAction()
    {
        list($doubankey, $omdbkey) = $this->getInput(array('doubankey', 'omdbkey'), 'post');

        $config = new PwConfigSet('site');
        $config->set('app.dimdb.doubankey', $doubankey)->set('app.dimdb.omdbkey', $omdbkey);

        $config->flush();

        $this->showMessage('ADMIN:success');
    }

    private function _loadConfigService()
    {
        return Wekit::load('config.PwConfig');
    }
}
