<?php

namespace Kanboard\Plugin\EmailUnassignedTask\Action;

use Kanboard\Model\Task;
use Kanboard\Action\Base;


/**
 * Email unassigned task near due date
 *
 * @package action
 * @author  Arien Nayrat
 */
class TaskEmailUnassigned extends Base
{
    /**
     * Get automatic action description
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return t('Send email when a task is not assigned');
    }

    /**
     * Get the list of compatible events
     *
     * @access public
     * @return array
     */
    public function getCompatibleEvents()
    {
        return array(
            Task::EVENT_DAILY_CRONJOB,
        );
    }

    /**
     * Get the required parameter for the action (defined by the user)
     *
     * @access public
     * @return array
     */
    public function getActionRequiredParameters()
    {
        return array(
            'user_id' => t('User that will receive the email'),
	    'category_id' => t('Category'),
            'subject' => t('Email subject'),
            'days' => t('Days before due date'),
        );
    }

    /**
     * Get the required parameter for the event
     *
     * @access public
     * @return string[]
     */
    public function getEventRequiredParameters()
    {
        return array('tasks');
    }

    /**
     * Check if the event data meet the action condition
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool
     */
    public function hasRequiredCondition(array $data)
    {
        return count($data['tasks']) > 0;
    }

    /**
     * Execute the action (Email unassigned task near due date)
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        $results = array();
	$max = $this->getParam('days') * 86400;
	$cat= $this->getParam('category_id');
        $user = $this->user->getById($this->getParam('user_id'));
        if (! empty($user['email'])) {
            foreach ($data['tasks'] as $task) {
	        $delay = $task['date_due'] - time();
		    if ( ($delay < $max) && (!empty($task['date_due'])) && (empty($task['owner_id'])) )   {
                if (! empty($cat) && $cat == $task['category_id']) {
                    $results[] = $this->sendEmail($task['id'], $user);
                    }
                elseif ( empty($cat) ) {
                    $results[] = $this->sendEmail($task['id'], $user);
                    } 

                }
            }
        }

        return in_array(true, $results, true);
    }

    /**
     * Send email
     *
     * @access private
     * @param  integer $task_id
     * @param  array   $user
     * @return boolean
     */
    private function sendEmail($task_id, array $user)
    {
        $task = $this->taskFinder->getDetails($task_id);

        $this->emailClient->send(
            $user['email'],
            $user['name'] ?: $user['username'],
            $this->getParam('subject'),
            $this->template->render('notification/task_create', array('task' => $task, 'application_url' => $this->config->get('application_url')))
        );

        return true;
    }
}
