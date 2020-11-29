<?php

namespace Kentron\Facade\Mail\Template;

use Kentron\Service\File;
use Kentron\Template\TError;

use Kentron\Facade\Mail\Entity\{
    MailAttachmentCollectionEntity,
    MailAttachmentEntity,
    MailEmbedCollectionEntity,
    MailEmbedEntity
};

abstract class AMail
{
    use TError;

    public const ATTACHMENT_IMAGE = 1;

    public const EMBED_IMAGE = 1;

    /**
     * Count of all emails successfully delivered
     *
     * @var int
     */
    protected $mailSentCount;

    /**
     * Username for the mailer transport
     *
     * @var string
     */
    protected $username;

    /**
     * Password for the mailer transport
     *
     * @var string
     */
    protected $password;

    /**
     * SMTP domain
     *
     * @var string
     */
    protected $smtp = "smtp.gmail.com";

    /**
     * Port number
     *
     * @var int
     */
    protected $port = 465;

    /**
     * Mail protocol
     *
     * @var string
     */
    protected $method = "ssl";

    /**
     * The target email
     *
     * @var string
     */
    protected $toEmail;

    /**
     * Origin email address
     *
     * @var string
     */
    protected $fromEmail;

    /**
     * Origin display name
     *
     * @var string
     */
    protected $fromName;

    /**
     * The subject of the email
     *
     * @var string
     */
    protected $subject;

    /**
     * The main body of the email
     *
     * @var string
     */
    protected $body;

    /**
     * Content type of the email body
     *
     * @var string
     */
    protected $contentType;

    /**
     * list of paths and CIDs of the files to be attached
     *
     * @var MailAttachmentCollectionEntity
     */
    protected $attachmentCollectionEntity;

    /**
     * list of paths and CIDs of the files to be embedded
     *
     * @var MailEmbedCollectionEntity
     */
    protected $embedCollectionEntity;

    abstract public function send (?string $to = null, ?string $subject = null, ?string $body = null): bool;
    abstract public function attach (MailAttachmentEntity $mailAttachmentEntity): bool;
    abstract public function embed (MailEmbedEntity $mailEmbedEntity): bool;

    final public function attachFile (string $filePath, ?string $fileName = null): bool
    {
        if (!$this->validateFile($filePath)) {
            return false;
        }

        $fileName = $fileName ?? basename($filePath);

        /** @var MailAttachmentEntity $attachmentEntity */
        $attachmentEntity = $this->getAttachmentCollectionEntity()->getNewEntity();
        $this->attachmentCollectionEntity->addEntity($attachmentEntity);

        $attachmentEntity->setPath($filePath);
        $attachmentEntity->setName($fileName);

        return $this->attach($attachmentEntity);
    }

    final public function embedFile (string $filePath, ?string $fileName = null, ?string $cid = null): bool
    {
        if (!$this->validateFile($filePath)) {
            return false;
        }

        $fileName = $fileName ?? basename($filePath);

        /** @var MailEmbedEntity $embedEntity */
        $embedEntity = $this->getEmbedCollectionEntity()->getNewEntity();
        $this->embedCollectionEntity->addEntity($embedEntity);

        $embedEntity->setPath($filePath);
        $embedEntity->setName($fileName);

        if (is_string($cid)) {
            $embedEntity->setCid($cid);
        }

        return $this->embed($embedEntity);
    }

    final public function getAttachmentCollectionEntity (): MailAttachmentCollectionEntity
    {
        if (is_null($this->attachmentCollectionEntity)) {
            $this->attachmentCollectionEntity = new MailAttachmentCollectionEntity();
        }

        return $this->attachmentCollectionEntity;
    }

    final public function getEmbedCollectionEntity (): MailEmbedCollectionEntity
    {
        if (is_null($this->embedCollectionEntity)) {
            $this->embedCollectionEntity = new MailEmbedCollectionEntity();
        }

        return $this->embedCollectionEntity;
    }

    final public function getLastEmbeddedCid (): ?string
    {
        /** @var MailEmbedEntity $embedEntity */
        $embedEntity = $this->embedCollectionEntity->getLastEntity();
        return $embedEntity->getCid();
    }

    /**
     *
     * Methods
     *
     */
    final public function setUsername (string $username): void
    {
        $this->username = $username;
    }

    final public function setPassword (string $password): void
    {
        $this->password = $password;
    }

    final public function setSmtp (string $smtp): void
    {
        $this->smtp = $smtp;
    }

    final public function setPort (int $port): void
    {
        $this->port = $port;
    }

    final public function setMethod (string $method): void
    {
        $this->method = $method;
    }

    final public function setFromEmail (string $fromEmail): void
    {
        $this->fromEmail = $fromEmail;
    }

    final public function setFromName (string $fromName): void
    {
        $this->fromName = $fromName;
    }

    final public function setContentType (string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * Helpers
     */

    private function validateFile (string &$filePath): bool
    {
        if (!File::isValidFile($filePath)) {
            $this->addError("'{$filePath}' is not a valid file");
            return false;
        }

        $filePath = File::getRealPath($filePath);
        return true;
    }
}
