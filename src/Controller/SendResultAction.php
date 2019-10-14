<?php


namespace App\Controller;


use App\Controller\Data\DataManipulation;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Symfony\Component\Mime\Email;

class SendResultAction extends AbstractController
{
    private $dataManipulation;

    public function __construct(DataManipulation $dataManipulation)
    {
        $this->dataManipulation = $dataManipulation;
    }

    public function send($recipient, $file = null)
    {

        $transport = (new Swift_SmtpTransport('smtp.mailtrap.io', 2525))
            ->setUsername('9fd378cb372bb3')
            ->setPassword('c83e1ec4b4ab5a');

        $mailer = new \Swift_Mailer($transport);

        $email = (new Swift_Message())
            ->setFrom("dito@tuta.io")
            ->setTo($recipient)
            ->setBody("test");

        $email->attach(\Swift_Attachment::fromPath($this->dataManipulation->getResultDir() . "/" . $file)->setFilename($file));

        $mailer->send($email);
    }
}