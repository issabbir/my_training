<?php

namespace App\Managers;

use App\Contracts\MessageContract;


class MessageManager implements MessageContract
{
    /** @var string  */
    private $url = '';

    /** @var string  */
    private $user = '';

    /** @var string  */
    private $password = '';

    private $from = null;

    /**
     * MessageManager constructor.
     */
    public function __construct()
    {
        $this->url = env('MESSAGE_BASE_URL', '');
        $this->user = env('MESSAGE_USER', '');
        $this->password = env('MESSAGE_PASSWORD', '');
        $this->from = env('MESSAGE_FROM', '');

        if(
            ($this->url == '') || ($this->user == '') || ($this->password == '') || ($this->from == null)
        ) {
            throw new \Exception('One or multiple required configuration(s) had not set yet to send sms.');
        }
    }

    /**
     * @param $to
     * @param $content
     * @return int|null
     */
    public function send($to, $content) : ?int
    {
        $messageId = null;

        if ( ($to == '') || ($content == '') ) {
            return null;
        }

        $url = $this->buildUrl($to, $content);

        if($url) {
            try {
                $result = simplexml_load_file($url);
                $messageId = (int) $result->ServiceClass->MessageId[0]; // PHP 7 will work like this way but lower version "MAY" work like "$result[0]->MessageId".
            } catch(\Exception $exception) {
                $messageId = null;
            }
        }

        return $messageId;
    }

    /**
     * @param $to
     * @param $content
     * @return string
     */
    private function buildUrl($to, $content) : string
    {
        return $this->url.'?Username='.$this->user.'&Password='.$this->password.'&From='.$this->from.'&To=88'.$to."&Message=".urlencode($content);
    }
}