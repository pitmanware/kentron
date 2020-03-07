<?php

namespace Kentron\Facade;

use Swift_SmtpTransport;
use Swift_Message;
use Swift_Mailer;

final class Mail
{
    /**
     * If Swiftmailer produces an error, it goes here
     * @var string
     */
    public $error = "";

    /**
     * Count of all emails successfully delivered
     * @var int
     */
    public $mailSentCount;

    /**
     * Username for the mailer transport
     * @var string
     */
    private $username = "";

    /**
     * Password for the mailer transport
     * @var string
     */
    private $password = "";

    /**
     * SMTP domain
     * @var string
     */
    private $smtp = "smtp.office365.com";

    /**
     * Port number
     * @var int
     */
    private $port = 587;

    /**
     * Mail protocol
     * @var string
     */
    private $method = "tls";

    /**
     * Origin email address
     * @var string
     */
    private $fromEmail = "";

    /**
     * Origin display name
     * @var string
     */
    private $fromName = "";

    /**
     * Content type of the email body
     * @var string
     */
    private $contentType;

    /**
     * The mailer transport
     * @var Swift_SmtpTransport
     */
    private $transport;

    public function send (string $to, string $subject, string $body = "")
    {
        if (is_null($this->transport)) {
            $this->buildTransport();
        }

        $message = new Swift_Message();

        $message->setFrom([$this->fromEmail => $this->fromName]);
        $message->setTo($to);
        $message->setSubject($subject);

        $message->setBody($body);
        $message->setContentType($this->contentType);

        try {
            $mailer = new Swift_Mailer($this->transport);
            $this->mailSentCount = $mailer->send($message);
        }
        catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }

        return true;
    }

    private function buildTransport (): void
    {
        $this->transport = new Swift_SmtpTransport($this->smtp, $this->port, $this->method);

        $this->transport->setUsername($this->username);
        $this->transport->setPassword($this->password);
    }

    /**
     *
     * Methods
     *
     */
    public function setUsername (string $username): void
    {
        $this->username = $username;
    }

    public function setPassword (string $password): void
    {
        $this->password = $password;
    }

    public function setSmtp (string $smtp): void
    {
        $this->smtp = $smtp;
    }

    public function setPort (int $port): void
    {
        $this->port = $port;
    }

    public function setMethod (string $method): void
    {
        $this->method = $method;
    }

    public function setFromEmail (string $fromEmail): void
    {
        $this->fromEmail = $fromEmail;
    }

    public function setFromName (string $fromName): void
    {
        $this->fromName = $fromName;
    }

    public function setContentType (string $contentType): void
    {
        $this->contentType = $contentType;
    }

}
