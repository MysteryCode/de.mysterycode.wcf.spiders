<?php

namespace wcf\system\cronjob;

use DOMElement;
use wcf\data\cronjob\Cronjob;
use wcf\system\cache\builder\SpiderCacheBuilder;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\HTTPRequest;
use wcf\util\XML;

/**
 * Refreshes list of search robots.
 *
 * @author             Marcel Werk, edited by Florian Gail
 * @copyright          2001-2015 WoltLab GmbH
 * @license            GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @see                \wcf\system\cronjob\RefreshSearchRobotsCronjob
 * @package            WoltLabSuite\Core\System\Cronjob
 */
class MysteryCodeSpiderRefreshCronjob extends AbstractCronjob
{
    /**
     * URLs of spiderList.xml-files
     *
     * @var string[]
     */
    public array $spiderLists = [
        'https://static.mysterycode.de/spiders/list.xml',
        'https://assets.woltlab.com/spiderlist/typhoon/list.xml',
    ];

    /**
     * list of spider-information fetched from the specified URLs
     *
     * @var []
     */
    public array $fetchedSpiders = [];

    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob)
    {
        parent::execute($cronjob);

        $existingSpiders = SpiderCacheBuilder::getInstance()->getData();

        foreach ($this->spiderLists as $spiderList) {
            $request = new HTTPRequest($spiderList);

            try {
                $request->execute();
                $reply = $request->getReply();
            } catch (SystemException $e) {
                continue;
            }

            $xml = new XML();
            try {
                $xml->loadXML('mysterycodeSpiderList.xml', $reply['body']);
            } catch (SystemException $e) {
                continue;
            }
            $xpath = $xml->xpath();

            // fetch spiders
            $spiders = $xpath->query('/ns:data/ns:spider');

            if (!empty($spiders)) {
                /** @var DOMElement $spider */
                foreach ($spiders as $spider) {
                    $identifier = \mb_strtolower($spider->getAttribute('ident'));
                    $name = $xpath->query('ns:name', $spider)->item(0);
                    $info = $xpath->query('ns:url', $spider)->item(0);

                    $this->fetchedSpiders[$identifier] = [
                        'spiderIdentifier' => $identifier,
                        'spiderName' => $name->nodeValue,
                        'spiderURL' => $info ? $info->nodeValue : '',
                    ];
                }
            }

            // make sure
            unset($spiders, $request, $reply, $xml, $xpath);
        }

        if (!empty($this->fetchedSpiders)) {
            $sql = "INSERT INTO			wcf" . WCF_N . "_spider
								(spiderIdentifier, spiderName, spiderURL)
				VALUES				(?, ?, ?)
				ON DUPLICATE KEY UPDATE		spiderName = VALUES(spiderName),
								spiderURL = VALUES(spiderURL)";
            $statement = WCF::getDB()->prepareStatement($sql);

            WCF::getDB()->beginTransaction();
            foreach ($this->fetchedSpiders as $parameters) {
                $statement->execute([
                    $parameters['spiderIdentifier'],
                    $parameters['spiderName'],
                    $parameters['spiderURL'],
                ]);
            }
            WCF::getDB()->commitTransaction();
        }

        // delete obsolete entries
        $sql = "DELETE FROM wcf" . WCF_N . "_spider WHERE spiderIdentifier = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        WCF::getDB()->beginTransaction();
        foreach ($existingSpiders as $spider) {
            if (!isset($this->fetchedSpiders[$spider->spiderIdentifier])) {
                $statement->execute([$spider->spiderIdentifier]);
            }
        }
        WCF::getDB()->commitTransaction();

        // clear spider cache
        SpiderCacheBuilder::getInstance()->reset();
    }
}
