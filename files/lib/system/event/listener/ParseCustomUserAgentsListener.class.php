<?php

namespace wcf\system\event\listener;

use wcf\data\user\online\UserOnline;

class ParseCustomUserAgentsListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		/** @var UserOnline $eventObj */
		if (preg_match('/(WoltLab (Suite|Community Framework))\/([0-9a-zA-Z. ]+)/', $parameters['userAgent'], $matches)) {
			$parameters['browser'] = $matches[1] . ' ' . $matches[2];
		}
		if (preg_match('/(WSC-Connect (?:API|WSC-Connect Mobile Browser))(?: |\/)([0-9a-zA-Z.\+\- ]+)/', $parameters['userAgent'], $matches)) {
			$parameters['browser'] = $matches[1] . (!empty($matches[2]) ? ' ' . $matches[2] : '');
		}
		if (preg_match('/(shoWWelle MEDIA Android-App)(?: |\/)([0-9a-zA-Z.\+\- ]+)/', $parameters['userAgent'], $matches)) {
			$parameters['browser'] = $matches[1] . (!empty($matches[2]) ? ' ' . $matches[2] : '');
		}
	}
}
