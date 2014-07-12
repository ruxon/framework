<?php

trait EmailEventable
{
    public function emailEvent($alias, $recipients = array(), $params = array(), $subject = '')
    {
        return Toolkit::i()->emailEvent->raise($alias, $recipients, $params, $subject);
    }
}