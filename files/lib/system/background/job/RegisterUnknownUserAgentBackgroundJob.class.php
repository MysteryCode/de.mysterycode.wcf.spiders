<?php

namespace wcf\system\background\job;

use Exception;
use wcf\util\HTTPRequest;
use wcf\util\MessageUtil;
use wcf\util\StringUtil;

use function wcf\functions\exception\logThrowable;

class RegisterUnknownUserAgentBackgroundJob extends AbstractBackgroundJob
{
    /**
     * @var string
     */
    protected $userAgent;

    public function __construct($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @inheritDoc
     */
    public function perform()
    {
        try {
            (new HTTPRequest(
                'https://api.mysterycode.de/woltlab/registeruseragent.php',
                [],
                ['userAgent' => StringUtil::trim(MessageUtil::stripCrap($this->userAgent))]
            ))->execute();
        } catch (Exception $e) {
            logThrowable($e);
        }
    }
}
