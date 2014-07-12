<?php

class PaymentPlatron implements PaymentBaseInterface
{
    const PAYMENT_URL = 'https://www.platron.ru/payment.php';
    
    public $merchant_id;
    
    public $secret_key;
    
    public $site_url;
    
    public $test_mode = null;
    
    public $result_url;
    
    public $success_url;
    
    public $failure_url;
    
    public $request_method;
    
    public function init()
    {
        if (!$this->merchant_id)
            $this->merchant_id = Manager::getInstance()->getModule('Shop')->config('PlatronMerchantId');
        
        if (!$this->secret_key)
            $this->secret_key = Manager::getInstance()->getModule('Shop')->config('PlatronSecretKey');
        
        if ($this->test_mode === null)
            $this->test_mode = Manager::getInstance()->getModule('Shop')->config('PlatronTestMode');
        
        if (!$this->site_url)
            $this->site_url = 'http://'.Toolkit::getInstance()->request->getServerName();
        
        if (!$this->result_url)
            $this->result_url = 'http://'.Toolkit::getInstance()->request->getServerName().'/shop/payment/result';
        
        if (!$this->success_url)
            $this->success_url = 'http://'.Toolkit::getInstance()->request->getServerName().'/shop/payment/success';
        
        if (!$this->failure_url)
            $this->failure_url = 'http://'.Toolkit::getInstance()->request->getServerName().'/shop/payment/failed';
    }
    
    public function checkPaymentMethod()
    {
        if (!empty($_REQUEST['pg_sig']))
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
        return isset($_REQUEST["pg_order_id"]) ? intval($_REQUEST["pg_order_id"]) : 0;
    }
    
    public function sendResponse($params)
    {
        $script = 'result';
        $status = $params['status'];
        unset($params['status']);
        unset($params['pg_sig']);
        $params['pg_status'] = $status;
        $params['pg_sig'] = $this->getSig($params, $script);
        
        header("Content-Type: text/xml");
        $result =  '<?xml version="1.0" encoding="utf-8"?><response>';
        foreach ($params as $key => $val) 
        {
            $result .= '<'.$key.'>'.$val.'</'.$key.'>'."\n";
        }
        
        $result .='</response>';
        echo $result;
        Core::app()->hardEnd();
        
        return true;
    }
       
    public function getUrlForPayment($order_id, $amount, $description, $currency='RUR', $language='ru')
    {
        $result = $this->getParams($order_id, $amount, $description, $currency, $language);

        return self::PAYMENT_URL . "?" . $result;
    }
    
    public function checkPayment($oOrder)
    {
        $params = $_REQUEST;
        unset($params['shop/payment/result']);
        
        $response = array(
            'status' => ''
        );
        
        $script = 'result';
        $params_check = $params;
        unset($params_check['pg_sig']);
        ksort($params_check);
        $sig_result = implode(";", $params_check);
        
        $sig_check = md5($script . ";" . $sig_result . ";" . $this->secret_key);
        
        
        if ($sig_check != $params['pg_sig']) { $response['status'] = 'error'; $response['pg_error_description'] = 'Bad sign'; }
        
        if ($oOrder && $oOrder->getId() && $params['pg_result'] == 1) 
        {
            $params['pg_ps_amount'] = round($params['pg_ps_amount'], 0);
            
            if ($params['pg_ps_amount'] >= $oOrder->getAmount())
            {
                if ($response['status'] != 'error') {
                    $response['status'] = 'ok';
                }
            }
        }
        
        $response['pg_salt'] = $params['pg_salt']; //$this->getSalt();
        $response['pg_sig'] = $this->getSig($response, $script);

        return $response;
    }
    
    protected function getParams($order_id, $amount, $description, $currency='RUR', $language='ru')
    {

        $result = array(
            'pg_merchant_id' => $this->merchant_id, // Идентификатор продавца в Platron
            'pg_order_id' => $order_id, // Идентификатор платежа в системе продавца. Рекомендуется поддерживать уникальность этого поля.
            'pg_amount' => $amount, // Сумма платежа в валюте pg_currency
            'pg_currency' => $currency, // Валюта, в которой указана сумма. RUR, USD, EUR.
            'pg_description' => $description,
            'pg_user_ip' => $_SERVER['REMOTE_ADDR'],
            'pg_language' => $language,
            'pg_testing_mode' => intval($this->test_mode),
            'pg_salt' => $this->getSalt() // Случайная строка
        );
        
        if ($this->site_url)
        {
            $result['pg_site_url'] = $this->site_url;
        }
        
        if ($this->result_url)
        {
            $result['pg_result_url'] = $this->result_url;
        }
        
        if ($this->success_url)
        {
            $result['pg_success_url'] = $this->success_url;
        }
        
        if ($this->failure_url)
        {
            $result['pg_failure_url'] = $this->failure_url;
        }
        
        if ($this->request_method)
        {
            $result['pg_request_method'] = $this->request_method;
        }
        
        $sig = $this->getSig($result);
        
        $result['pg_sig'] = $sig;
        
        $res = "";
        
        foreach ($result as $k => $val)
        {
            $res .= "&" . $k . "=" . $val;
        }
        
        return substr($res, 1);
    }
    
    protected function getSalt($length = 6)
    {
        $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ0123456789";
        $validCharNumber = strlen($validCharacters);

        $result = "";

        for ($i = 0; $i < $length; $i++)
        {
            $index = mt_rand(0, $validCharNumber - 1);
            $result .= $validCharacters[$index];
        }

        return $result;

    }
    
    protected function getSig($aParams, $script = false)
    {
        if (!$script)
            $script = substr(self::PAYMENT_URL, strrpos(self::PAYMENT_URL, "/") + 1);
        
        ksort($aParams);
        $result = implode(";", $aParams);
        
        return md5($script . ";" . $result . ";" . $this->secret_key);
    }
}