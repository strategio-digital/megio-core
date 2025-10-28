<?php
declare(strict_types=1);

namespace Megio\Mailer;

use Nette\Mail\Message;
use Nette\Mail\SmtpMailer as NetteSmtpMailer;

class SmtpMailer
{
    protected NetteSmtpMailer $mailer;

    public function __construct()
    {
        $encryption = $_ENV['SMTP_ENCRYPTION'] === 'tls' || $_ENV['SMTP_ENCRYPTION'] === 'ssl'
            ? NetteSmtpMailer::EncryptionTLS
            : NetteSmtpMailer::EncryptionSSL;

        $this->mailer = new NetteSmtpMailer(
            host: $_ENV['SMTP_HOST'],
            username: $_ENV['SMTP_USERNAME'],
            password: $_ENV['SMTP_PASSWORD'],
            port: (int)$_ENV['SMTP_PORT'],
            encryption: $encryption,
        );
    }

    public function send(Message $message): void
    {
        $this->mailer->send($message);
    }
}
