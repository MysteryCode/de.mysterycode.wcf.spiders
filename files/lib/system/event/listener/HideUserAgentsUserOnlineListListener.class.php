<?php

namespace wcf\system\event\listener;

use wcf\util\StringUtil;

class HideUserAgentsUserOnlineListListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		/** @var \wcf\page\UsersOnlineListPage $eventObj */
		
		$excludedAgents = explode("\n", StringUtil::unifyNewlines(BLACKLIST_USER_AGENTS));
		if (!empty($excludedAgents)) {
			foreach ($excludedAgents as $agent) {
				$eventObj->objectList->getConditionBuilder()->add('userAgent NOT LIKE ?', [str_replace('*', '%', $agent)]);
			}
		}
	}
}
