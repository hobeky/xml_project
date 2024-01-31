<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use SimpleXMLElement;

class XmlObjectManager
{
    public function findUserByXmlId(ArrayCollection $arrayCollection, string $xmlId): ?User
    {
        $filtered = $arrayCollection->filter(function(User $user) use ($xmlId) {
            return $user->getXmlId() == $xmlId;
        });

        $user = $filtered->first();

        return $user !== false ? $user : null;
    }

//    TODO: xmlFilePath to env variables!
    public function saveXmlFile(SimpleXMLElement $xml, string $xmlFilePath): void
    {
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        $dom->save($xmlFilePath);
    }
}
