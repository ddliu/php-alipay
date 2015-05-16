# php-alipay

[![Travis](https://img.shields.io/travis/ddliu/php-alipay.svg?style=flat-square)](https://travis-ci.org/ddliu/php-alipay) [![Packagist](https://img.shields.io/packagist/v/ddliu/alipay.svg?style=flat-square)](https://packagist.org/packages/ddliu/alipay) [![Packagist](https://img.shields.io/packagist/l/doctrine/orm.svg?style=flat-square)]()

支付宝PHP SDK

## 内容

- direct(快捷支付)
    - web(web版)
    - mobile(移动版)
- ...(其它服务)

## 使用

### 初始化

```php
<?php
use ddliu\alipay\DirectPay\Web\WebPay;
use ddliu\alipay\DirectPay\Mobile\MobilePay;

$webPay = new WebPay($options); // 参数参考配置选项说明
$mobilePay = new MobilePay($options);
```

### 生成web付款url

```php
<?php
// ...
$webPay->generatePaymentUrl($goodsData); // 参数参考商品选项说明
```

### 生成移动客户端支付串

```php
<?php
// ...
$mobilePay->generatePaymentString($goodsData); // 参数参考商品选项说明
```

### 通知处理

```php
$data = $webPay->verifyRequest();
if (!$data) {
    die('验证失败');
}

switch($data['trade_status']) {
    case 'TRADE_SUCCESS':
    case 'TRADE_FINISHED':
        // TODO: 支付成功，取得订单号进行其它相关操作。
        $info['out_trade_no'] = $data['out_trade_no'];
        $info['trade_no'] = $data['trade_no'];
        break;
}

$webPay->getVerifier()->confirm();
```

### 配置选项说明

- partner:
- seller_id:
- key: 
- notify_url: 
- charset: 传入编码，默认为UTF-8
- sign_type: 签名类型，默认为RSA
- cacert: cacert.pem路径，默认使用本库自带的
- public_key_path: RSA公钥
- private_key_path: RSA密钥
- transport: 通知验证使用, 默认为https
- show_url:

### 商品选项说明

- notify_url: 可选，覆盖默认notify_url
- out_trade_no: 单号
- subject
- total_fee
- body
- show_url: 可选
- anti_phishing_key: 可选
- exter_invoke_ip: 可选

## 参考资料

- [Alipay SDK for Laravel5](https://github.com/Latrell/Alipay)
- [即时到账SDK](http://download.alipay.com/public/api/base/alipaydirect.zip)