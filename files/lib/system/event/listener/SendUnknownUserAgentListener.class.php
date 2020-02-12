<?php

namespace wcf\system\event\listener;

use wcf\data\user\online\UserOnline;
use wcf\page\AbstractPage;
use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\RegisterUnknownUserAgentBackgroundJob;
use wcf\system\WCF;
use wcf\util\UserUtil;

class SendUnknownUserAgentListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		/** @var AbstractPage $eventObj */
		
		if (USERS_ONLINE_SEND_UNKNOWN_USERAGENTS && !WCF::getUser()->userID && !WCF::getSession()->spiderID && !WCF::getSession()->getVar('userAgentSpiderDetectionHelper')) {
			$userAgent = UserUtil::getUserAgent();
			$profile = new UserOnline(WCF::getUser());
			$profile->userAgent = $userAgent;
			
			if ($userAgent == $profile->getBrowser() && !preg_match('/(WoltLab (Suite|Community Framework)|WSC-Connect|shoWWelle)/', $userAgent)) {
				BackgroundQueueHandler::getInstance()->enqueueIn([
					new RegisterUnknownUserAgentBackgroundJob($userAgent)
				]);
			}
			WCF::getSession()->register('userAgentSpiderDetectionHelper', $userAgent);
		}
		
	}
}
