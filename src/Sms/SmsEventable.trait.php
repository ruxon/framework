<?php

trait SmsEventable
{
    public function smsEvent($alias, $to, $params = array())
    {
        return Toolkit::i()->smsEvent->raise($alias, $to, $params);
    }
}