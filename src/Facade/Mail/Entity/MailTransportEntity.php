<?php
declare(strict_types=1);

namespace Kentron\Facade\Mail\Entity;

use Kentron\Entity\Template\AEntity;
use Kentron\Service\File;

use Kentron\Facade\Mail\Entity\Attachment\{
    MailAttachmentCollectionEntity,
    MailAttachmentEntity
};
use Kentron\Facade\Mail\Entity\Embed\{
    MailEmbedCollectionEntity,
    MailEmbedEntity
};
use Kentron\Facade\Mail\Entity\{
    MailTargetEntity,
    MailTargetCollectionEntity
};

final class MailTransportEntity extends AEntity
{
    /**
     * Count of all emails successfully delivered
     *
     * @var int
     */
    private $sentCount = 0;

    /**
     * Username for the mailer transport
     *
     * @var string
     */
    private $username;

    /**
     * Password for the mailer transport
     *
     * @var string
     */
    private $password;

    /**
     * SMTP domain
     *
     * @var string
     */
    private $host = "smtp.gmail.com";

    /**
     * Port number
     *
     * @var int
     */
    private $port = 465;

    /**
     * Mail protocol
     *
     * @var string
     */
    private $method = "ssl";

    /**
     * Origin email address
     *
     * @var string
     */
    private $fromEmail;

    /**
     * Origin display name
     *
     * @var null|string
     */
    private $fromName;

    /**
     * The subject of the email
     *
     * @var string
     */
    private $subject;

    /**
     * The main body of the email
     *
     * @var null|string
     */
    private $body;

    /**
     * The alternative body of the email
     *
     * @var null|string
     */
    private $altBody;

    /**
     * If the body of the email is HTML
     *
     * @var boolean
     */
    private $isHtml = true;

    /**
     * List of recipients to receive the email
     *
     * @var MailTargetCollectionEntity
     */
    private $targetCollectionEntity;

    /**
     * List of paths and CIDs of the files to be attached
     *
     * @var MailAttachmentCollectionEntity
     */
    private $attachmentCollectionEntity;

    /**
     * List of paths and CIDs of the files to be embedded
     *
     * @var MailEmbedCollectionEntity
     */
    private $embedCollectionEntity;

    public function __construct(?string $username = null, ?string $password = null)
    {
        $this->username = $username ?? $this->username;
        $this->password = $password ?? $this->password;

        $this->targetCollectionEntity = new MailTargetCollectionEntity();
    }

    /**
     * Add file as attachment
     *
     * @param string $filePath
     * @param string|null $fileName
     *
     * @return boolean
     */
    public function attachFile(string $filePath, ?string $fileName = null): bool
    {
        if (!$this->validateFile($filePath)) {
            return false;
        }

        $fileName = $fileName ?? basename($filePath);

        /** @var MailAttachmentEntity $attachmentEntity */
        $attachmentEntity = $this->getAttachmentCollectionEntity()->getNewEntity();

        $attachmentEntity->setPath($filePath);
        $attachmentEntity->setName($fileName);

        $this->attachmentCollectionEntity->addEntity($attachmentEntity);
    }

    /**
     * Add file into the body (usually an image)
     *
     * @param string $filePath
     * @param string|null $fileName
     * @param string|null $cid
     *
     * @return boolean
     */
    public function embedFile(string $filePath, ?string $fileName = null, ?string $cid = null): bool
    {
        if (!$this->validateFile($filePath)) {
            return false;
        }

        $fileName = $fileName ?? basename($filePath);

        /** @var MailEmbedEntity $embedEntity */
        $embedEntity = $this->getEmbedCollectionEntity()->getNewEntity();

        $embedEntity->setPath($filePath);
        $embedEntity->setName($fileName);

        if (is_string($cid)) {
            $embedEntity->setCid($cid);
        }

        $this->embedCollectionEntity->addEntity($embedEntity);
    }

    /**
     * Add a recipient for the email (multiple allowed)
     *
     * @param string $email
     * @param string|null $name
     *
     * @return void
     */
    public function addRecipient(string $email, ?string $name = null): void
    {
        /** @var MailTargetEntity $targetEntity */
        $targetEntity = $this->targetCollectionEntity->getNewEntity();

        $targetEntity->setEmail($email);
        $targetEntity->setName($name);

        $this->targetCollectionEntity->addEntity($targetEntity);
    }

    /**
     * Getters
     */

    public function getSentCount(): int
    {
        return $this->sentCount;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function getFromName(): string
    {
        return $this->fromName ?? "";
    }

    public function getSubject(): string
    {
        return $this->subject ?? "";
    }

    public function getBody(): string
    {
        return $this->body ?? "";
    }

    public function getAltBody(): string
    {
        return $this->altBody ?? "";
    }

    public function getIsHtml(): bool
    {
        return $this->isHtml;
    }

    public function getTargetCollectionEntity(): MailTargetCollectionEntity
    {
        return $this->targetCollectionEntity;
    }

    public function getAttachmentCollectionEntity(): MailAttachmentCollectionEntity
    {
        if (is_null($this->attachmentCollectionEntity)) {
            $this->attachmentCollectionEntity = new MailAttachmentCollectionEntity();
        }

        return $this->attachmentCollectionEntity;
    }

    public function getEmbedCollectionEntity(): MailEmbedCollectionEntity
    {
        if (is_null($this->embedCollectionEntity)) {
            $this->embedCollectionEntity = new MailEmbedCollectionEntity();
        }

        return $this->embedCollectionEntity;
    }

    public function getLastEmbeddedCid(): ?string
    {
        /** @var MailEmbedEntity $embedEntity */
        $embedEntity = $this->embedCollectionEntity->getLastEntity();
        return $embedEntity->getCid();
    }

    /**
     * Setters
     */

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function setFromEmail(string $fromEmail): void
    {
        $this->fromEmail = $fromEmail;
    }

    public function setFromName(string $fromName): void
    {
        $this->fromName = $fromName;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function setAltBody(string $altBody): void
    {
        $this->altBody = $altBody;
    }

    public function setIsHtml(bool $isHtml = true): void
    {
        $this->isHtml = $isHtml;
    }

    /**
     * Helpers
     */

    public function isValid(): bool
    {
        if (is_null($this->username)) {
            $this->addError("Username is not set");
            return false;
        }
        if (is_null($this->password)) {
            $this->addError("Password is not set");
            return false;
        }
        if (is_null($this->fromEmail)) {
            $this->addError("From email is not set");
            return false;
        }
        if (is_null($this->targetEmail)) {
            $this->addError("Target email is not set");
            return false;
        }
        if (is_null($this->subject)) {
            $this->addError("Email subject is not set");
            return false;
        }
    }

    public function incrementSentCount(): void
    {
        $this->sentCount++;
    }

    private function validateFile(string &$filePath): bool
    {
        if (!File::isValidFile($filePath)) {
            $this->addError("'{$filePath}' is not a valid file");
            return false;
        }

        $filePath = File::getRealPath($filePath);
        return true;
    }
}
