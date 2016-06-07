<?php

namespace Kanboard\Plugin\EmailUnassignedTask;

use Kanboard\Core\Plugin\Base;
use Kanboard\Plugin\EmailUnassignedTask\Action\TaskEmailUnassigned;

class Plugin extends Base
{
    public function initialize()
    {
        $this->actionManager->register(new TaskEmailUnassigned($this->container));
    }
    public function getPluginName()
    {
        return 'Email Unassigned Task';
    }
    public function getPluginDescription()
    {
        return t('This plugin send email if task are not assigned');
    }
    public function getPluginAuthor()
    {
        return 'Adrien Nayrat';
    }
    public function getPluginVersion()
    {
        return '1.0.0';
    }
    public function getPluginHomepage()
    {
        return 'https://github.com/anayrat/kb_EmailUnassignedTask/';
    }
    
}
