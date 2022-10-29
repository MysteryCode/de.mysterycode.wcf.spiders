<?php

namespace wcf\system\background\job;

use Exception;
use GuzzleHttp\Psr7\Request;
use wcf\system\io\HttpFactory;
use wcf\util\MessageUtil;
use wcf\util\StringUtil;

use function wcf\functions\exception\logThrowable;

use const PHP_QUERY_RFC1738;

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
            $client = HttpFactory::getDefaultClient();
            $request = new Request(
                'POST',
                'https://api.mysterycode.de/woltlab/registeruseragent.php',
                [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                \http_build_query(
                    [
                        'userAgent' => StringUtil::trim(MessageUtil::stripCrap($this->userAgent)),
                    ],
                    '',
                    '&',
                    PHP_QUERY_RFC1738
                )
            );
            $client->send($request);
        } catch (Exception $e) {
            logThrowable($e);
        }
    }
}
