<?php

namespace Kentron\Facade\Mail\Template;

use Kentron\Facade\Mail\Entity\Attachment\MailAttachmentEntity;
use Kentron\Facade\Mail\Entity\Embed\MailEmbedEntity;
use Kentron\Facade\Mail\Entity\MailTargetEntity;
use Kentron\Facade\Mail\Entity\MailTransportEntity;

abstract class AMail
{
    abstract public static function send(MailTransportEntity $mailTransportEntity): bool;

    abstract protected static function addRecipient(MailTargetEntity $mailTargetEntity): bool;
    abstract protected static function attach(MailAttachmentEntity $mailAttachmentEntity): bool;
    abstract protected static function embed(MailEmbedEntity $mailEmbedEntity): bool;

    public static function sendMail(MailTransportEntity $mailTransportEntity): bool
    {
        if (!$mailTransportEntity->isValid()) {
            return false;
        }

        return self::send($mailTransportEntity);
    }
}
