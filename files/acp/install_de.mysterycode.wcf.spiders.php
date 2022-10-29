<?php

use wcf\data\cronjob\CronjobAction;
use wcf\data\cronjob\CronjobList;
use wcf\system\cronjob\RefreshSearchRobotsCronjob;

$cronList = new CronjobList();
$cronList->getConditionBuilder()->add('cronjob.className = ?', [RefreshSearchRobotsCronjob::class]);
$cronList->readObjects();
$cron = $cronList->current();

if (!$cron->isDisabled) {
    $cronAction = new CronjobAction([$cron], 'toggle');
    $cronAction->executeAction();
}
