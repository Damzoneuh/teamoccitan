<?php


namespace App\Helper;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;

trait MailHelper
{
    /**
     * @param string $subject
     * @param string $from
     * @param string $target
     * @param string $vue
     * @param array $context
     * @return TemplatedEmail
     */
    public function createTemplatedMail(string $subject, string $from, string $target, string $vue, array $context){
        $message = new TemplatedEmail();
        $message->to($target);
        $message->from($from);
        $message->subject($subject);
        $message->htmlTemplate($vue);
        $message->context($context);

        return $message;
    }
}