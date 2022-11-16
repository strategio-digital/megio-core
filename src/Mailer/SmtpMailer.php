<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Mailer;

use Nette\Mail\Message;

class SmtpMailer
{
    protected \Nette\Mail\SmtpMailer $mailer;
    
    public function __construct()
    {
        $this->mailer = new \Nette\Mail\SmtpMailer([
            'host' => $_ENV['SMTP_HOST'],
            'username' => $_ENV['SMTP_USERNAME'],
            'password' => $_ENV['SMTP_PASSWORD'],
            'secure' => $_ENV['SMTP_SECURE'],
            'port' => $_ENV['SMTP_PORT']
        ]);
    }
    
    public function send(Message $message) : void
    {
        $this->mailer->send($message);
    }
}