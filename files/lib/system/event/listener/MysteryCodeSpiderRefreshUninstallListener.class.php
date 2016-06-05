<?php

namespace wcf\system\event\listener;
use wcf\data\cronjob\CronjobAction;
use wcf\data\cronjob\CronjobList;
use wcf\data\package\PackageCache;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;

class MysteryCodeSpiderRefreshUninstallListener implements IParameterizedEventListener {

	/**
	 * @see	\wcf\system\event\listener\IParameterizedEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!empty($_POST['packageID'])) {
			$packageID = intval($_POST['packageID']);
			$package = PackageCache::getInstance()->getPackage($packageID);
			
			if ($package->package == 'de.mysterycode.wcf.spiders') {
				$cronList = new CronjobList();
				$cronList->getConditionBuilder()->add('cronjob.className = ?', array('wcf\system\cronjob\RefreshSearchRobotsCronjob'));
				$cronList->readObjects();
				$cron = $cronList->current();
				
				if ($cron->isDisabled) {
					$cronAction = new CronjobAction(array($cron), 'toggle');
					$cronAction->executeAction();
				}
			}
		}
	}
}
