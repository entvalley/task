<?php

namespace Entvalley\AppBundle\Service;

use Symfony\Component\Templating\EngineInterface;

class TemplatedMailer
{
    private $templateEngine;
    private $mailer;

    public function __construct(EngineInterface $templateEngine, \Swift_Mailer $mailer)
    {
        $this->templateEngine = $templateEngine;
        $this->mailer = $mailer;
    }

    public function send($subject, $to, $template, $args)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('test@example.com')
            ->setTo($to)
            ->setBody(
                $this->templateEngine->render($template, $args)
            )
        ;

        return $this->mailer->send($message);
    }
}
