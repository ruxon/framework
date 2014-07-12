<?php

trait Eventable
{
    protected $events = array();

    public function on($name, $handler, $data = null)
    {
        $this->events[$name][] = [$handler, $data];
    }

    public function off($name, $handler = null)
    {
        if (empty($this->events[$name])) {
            return false;
        }

        if ($handler === null) {
            unset($this->events[$name]);

            return true;
        } else {
            $success = false;
            foreach ($this->events[$name] as $i => $event) {
                if ($event[0] === $handler) {
                    unset($this->events[$name][$i]);
                    $success = true;
                }
            }

            $this->events[$name] = array_values($this->events[$name]);

            return $success;
        }
    }

    public function trigger($name, $event)
    {
        if (!empty($this->events[$name])) {

            if ($event === null) {
                $event = new Event;
            }

            if ($event->sender === null) {
                $event->sender = $this;
            }
            $event->handled = false;
            $event->name = $name;


            foreach ($this->events[$name] as $handler) {
                $event->data = $handler[1];

                call_user_func($handler[0], $event);

                if ($event->handled) {
                    return true;
                }
            }
        }

        return false;
    }
}