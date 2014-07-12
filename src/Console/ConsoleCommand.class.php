<?php

abstract class ConsoleCommand extends SimpleController
{
    abstract function actionExecute();
}