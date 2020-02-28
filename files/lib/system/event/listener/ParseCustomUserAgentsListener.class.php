<?php

namespace wcf\system\event\listener;

use wcf\data\user\online\UserOnline;

class ParseCustomUserAgentsListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		/** @var UserOnline $eventObj */
		if (preg_match('/(WoltLab (?:Suite|Community Framework))\/([0-9a-zA-Z. ]+)/', $parameters['userAgent'], $matches)) {
			$parameters['browser'] = $matches[1] . ' ' . $matches[2];
		}
		else if (preg_match('/(WSC-Connect (?:API|WSC-Connect Mobile Browser))(?: |\/)([0-9a-zA-Z.\+\- ]+)/', $parameters['userAgent'], $matches)) {
			$parameters['browser'] = $matches[1] . (!empty($matches[2]) ? ' ' . $matches[2] : '');
		}
		else if (preg_match('/shoWWelle MEDIA Android-App(?: |\/)([0-9a-zA-Z.\+\- ]+)/', $parameters['userAgent'], $matches)) {
			$parameters['browser'] = 'shoWWelle MEDIA Android-App' . (!empty($matches[1]) ? ' ' . $matches[1] : '');
		}
		else if (preg_match('/Dalvik\/([0-9a-zA-Z.\+\-]+)/', $parameters['userAgent'], $matches)) {
			$parameters['browser'] = 'Dalvik' . (!empty($matches[1]) ? ' ' . $matches[1] : '');
		}
		else if (preg_match('/com\.google\.android\.apps\.searchlite\/([0-9a-zA-Z.\+\-]+)/', $parameters['userAgent'], $matches)) {
			$parameters['browser'] = 'Google Go' . (!empty($matches[1]) ? ' ' . $matches[1] : '');
		}
		else if (preg_match('/Tiny Tiny RSS\/([0-9a-zA-Z.\+\-]+)/', $parameters['userAgent'], $matches)) {
			$parameters['browser'] = 'Tiny Tiny RSS' . (!empty($matches[1]) ? ' ' . $matches[1] : '');
		}
	}
}
