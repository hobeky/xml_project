<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Exception\XmlFileNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;
use SimpleXMLElement;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\SerializerInterface;

final class XmlHandlerService
{
    private readonly string $xmlAbsoluteFilePath;
    const USER_RECORD_TAG = 'user';
    const USERS_COLLECTION_TAG = 'users';


    public function __construct(
        private readonly SerializerInterface   $serializer,
        private readonly ParameterBagInterface $parameterBag,
        private readonly XmlObjectManager      $manager
    )
    {
        $this->xmlAbsoluteFilePath = $this->parameterBag->get('kernel.project_dir') . $this->parameterBag->get('relative_path_to_xml_file');

        $this->checkIfXmlFileExists();
    }

    public function indexLogic(): ArrayCollection
    {
        return $this->convertXmlToUsers();
    }

    public function createNewUser(User $user): void
    {
        $xml = simplexml_load_file($this->xmlAbsoluteFilePath);

        if ($xml === false) {
            throw XmlFileNotFoundException::fileNotFound($this->xmlAbsoluteFilePath);
        }

        $user->setXmlId(uniqid());
        $context = ['xml_root_node_name' => self::USER_RECORD_TAG];
        $userXml = $this->serializer->serialize($user, 'xml', $context);
        $userXml = new SimpleXMLElement($userXml);
        $this->appendSimpleXmlElement($xml, $userXml);

        $this->manager->saveXmlFile($xml, $this->xmlAbsoluteFilePath);
    }


    public function findUser(string $id): User
    {
        $users = $this->convertXmlToUsers();
        $foundUser = $this->manager->findUserByXmlId($users, $id);

        if (!$foundUser) {
            throw UserNotFoundException::noUserWithId($id);
        }

        return $foundUser;
    }


    /**
     * @throws UserNotFoundException
     */
    public function editUserLogic(string $id, User $updatedUserData): void
    {
        $users = $this->convertXmlToUsers();
        $userToEdit = $this->manager->findUserByXmlId($users, $id);

        if (!$userToEdit) {
            throw UserNotFoundException::noUserWithId($id);
        }

//        TODO: refactor this!
        $userToEdit->setName($updatedUserData->getName());
        $userToEdit->setSureName($updatedUserData->getSureName());
        $userToEdit->setSex($updatedUserData->getSex());
        $userToEdit->setDateOfBirth($updatedUserData->getDateOfBirth());

        $context = ['xml_root_node_name' => self::USERS_COLLECTION_TAG];
        $updatedXml = $this->serializer->serialize([self::USER_RECORD_TAG => $users->toArray()], 'xml', $context);

        $this->manager->saveXmlFile(new SimpleXMLElement($updatedXml), $this->xmlAbsoluteFilePath);
    }



    public function deleteUser(string $id): void
    {
        $users = $this->convertXmlToUsers();
        $userToDelete = $this->manager->findUserByXmlId($users, $id);

        if (!$userToDelete) {
            throw UserNotFoundException::noUserWithId($id);
        }

        $users->removeElement($userToDelete);
        $context = ['xml_root_node_name' => self::USERS_COLLECTION_TAG];
        $updatedXml = $this->serializer->serialize([self::USER_RECORD_TAG => $users->toArray()], 'xml', $context);

        $this->manager->saveXmlFile(new SimpleXMLElement($updatedXml), $this->xmlAbsoluteFilePath);
    }


    private function appendSimpleXmlElement(SimpleXMLElement $to, SimpleXMLElement $from): void
    {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }


    /**
     * @throws XmlFileNotFoundException
     */
    private function checkIfXmlFileExists(): void
    {
        if (!file_exists($this->xmlAbsoluteFilePath)) {
            throw XmlFileNotFoundException::fileNotFound($this->xmlAbsoluteFilePath);
        }
    }

    /**
     * @return ArrayCollection<int, User>
     */
    private function convertXmlToUsers(): ArrayCollection
    {
        $xml = file_get_contents($this->xmlAbsoluteFilePath);

        if ($xml === false) {
            throw XmlFileNotFoundException::fileNotFound($this->xmlAbsoluteFilePath);
        }

        $xmlEncoder = new XmlEncoder();
        $data = $xmlEncoder->decode($xml, 'xml');
        $objects = $this->serializer->denormalize($data[self::USER_RECORD_TAG], User::class . '[]', 'xml');

        return new ArrayCollection($objects);
    }
}
