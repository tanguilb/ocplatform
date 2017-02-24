<?php
/**
 * Created by PhpStorm.
 * User: tangui
 * Date: 24/02/17
 * Time: 14:02
 */

namespace OC\PlatformBundle\Antispam;


class OCAntispam
{
    private $mailer;
    private $locale;
    private $minLength;

    public function __construct(\Swift_Mailer $mailer, $locale, $minLength)
    {
        $this->mailer = $mailer;
        $this->locale = $locale;
        $this->minLength = (int) $minLength;
    }

    public function isSpam($text)
    {
        return strlen($text) < $this->minLength;
    }

}