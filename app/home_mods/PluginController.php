<?php

class PluginController extends ControllerBase
{

    public function indexAction()
    {
        $this->view->setVars(array(
            'plugins' => PluginManager::getList(),
        ));
    }

}