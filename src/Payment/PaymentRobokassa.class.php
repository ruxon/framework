<?php

class PaymentRobokassa implements PaymentBaseInterface
{
    const PAYMENT_URL = 'https://auth.robokassa.ru/Merchant/Index.aspx';

    const PAYMENT_TEST_URL = 'http://test.robokassa.ru/Index.aspx';

    public $merchant_login;

    public $password1;

    public $password2;

    public $test_mode = null;

    public function init()
    {
        if (!$this->merchant_login)
            $this->merchant_login = Manager::getInstance()->getModule('Shop')->config('RobokassaMerchantLogin');

        if (!$this->password1)
            $this->password1 = Manager::getInstance()->getModule('Shop')->config('RobokassaPassword1');

        if (!$this->password2)
            $this->password2 = Manager::getInstance()->getModule('Shop')->config('RobokassaPassword2');

        if ($this->test_mode === null)
            $this->test_mode = Manager::getInstance()->getModule('Shop')->config('RobokassaTestMode');
    }

    public function checkPaymentMethod()
    {
        if (!empty($_REQUEST['SignatureValue']))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getOrderId()
    {
        return isset($_REQUEST["InvId"]) ? intval($_REQUEST["InvId"]) : 0;
    }

    public function sendResponse($params)
    {
        if ($params['status'] == 'ok')
        {
            echo "OK".$params['order_id'];
            Core::app()->hardEnd();

            return true;
        }
    }

    public function getUrlForPayment($order_id, $amount, $description, $currency='', $language='ru')
    {
        $result = $this->getParams($order_id, $amount, $description, $currency, $language);

        return $this->test_mode ? self::PAYMENT_TEST_URL . "?" . $result : self::PAYMENT_URL . "?" . $result;
    }

    public function checkPayment($oOrder)
    {
        $params = $_REQUEST;
        unset($params['shop/payment/result']);

        $response = array(
            'status' => ''
        );

        $params_check = $params;
        unset($params_check['SignatureValue']);
        ksort($params_check);

        $sig_check = strtoupper(md5($params_check['OutSum'].":".$params_check['InvId'].":".$this->password2));


        if ($sig_check != strtoupper($params['SignatureValue']))
        {
            $response['status'] = 'error'; $response['error_description'] = 'Bad sign';
        }

        if ($oOrder && $oOrder->getId())
        {
            $params['OutSum'] = round($params['OutSum'], 0);

            if ($params['OutSum'] >= $oOrder->getAmount())
            {
                if ($response['status'] != 'error') {
                    $response['status'] = 'ok';
                    $response['order_id'] = $oOrder->getId();
                }
            }
        }

        return $response;
    }

    protected function getParams($order_id, $amount, $description, $currency='RUR', $language='ru')
    {
        $result = array(
            'MrchLogin' => $this->merchant_login, // Идентификатор продавца
            'InvId' => $order_id, // Идентификатор платежа в системе продавца. Рекомендуется поддерживать уникальность этого поля.
            'OutSum' => $amount, // Сумма платежа в валюте IncCurrLabel
            'IncCurrLabel' => $currency, // Валюта, в которой указана сумма. RUR, USD, EUR.
            'Desc' => $description,
            'Culture' => $language,
        );

        $sig = $this->getSig($result);
        $result['SignatureValue'] = $sig;
        $res = "";

        foreach ($result as $k => $val)
        {
            $res .= "&" . $k . "=" . $val;
        }

        return substr($res, 1);
    }

    protected function getSig($aParams)
    {
        return strtoupper(md5($this->merchant_login.":".$aParams['OutSum'].":".$aParams['InvId'].":".$this->password1));
    }
}