<?php
/**
 * Created by PhpStorm.
 * User: tangui
 * Date: 27/02/17
 * Time: 14:14
 */

namespace OC\PlatformBundle\Email;

use OC\PlatformBundle\Entity\Application;

class ApplicationMailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer =$mailer;
    }

    public function sendNewNotifications(Application $application)
    {
        $message = new \Swift_Message(
            'Nouvelle candidature',
            'Vous avez reçu une nouvelle candidature.'
        );

        $message->addTo($application->getAdvert()->getEmail())
            ->addFrom('dev.wildcodeshool@gmail.com');

        $this->mailer->send($message);
    }
}