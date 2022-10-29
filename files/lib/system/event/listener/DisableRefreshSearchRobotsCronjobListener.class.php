<?php

namespace wcf\system\event\listener;

use wcf\data\cronjob\CronjobAction;
use wcf\data\cronjob\CronjobList;
use wcf\system\cronjob\RefreshSearchRobotsCronjob;

class DisableRefreshSearchRobotsCronjobListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        /** @var RefreshSearchRobotsCronjob $eventObj */

        $cronList = new CronjobList();
        $cronList->getConditionBuilder()->add('cronjob.className = ?', [RefreshSearchRobotsCronjob::class]);
        $cronList->readObjects();
        $cron = $cronList->current();

        if (!$cron->isDisabled) {
            $cronAction = new CronjobAction([$cron], 'toggle');
            $cronAction->executeAction();
        }
    }
}
