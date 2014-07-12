<?php

interface PaymentBaseInterface
{
    public function checkPaymentMethod();
    public function sendResponse($params);
    public function getOrderId();
}