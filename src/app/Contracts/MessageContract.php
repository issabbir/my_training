<?php

namespace App\Contracts;


interface MessageContract
{
    /**
     * @param $to
     * @param $content
     * @return int|null
     */
    public function send($to, $content);
}