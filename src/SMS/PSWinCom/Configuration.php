<?php
namespace DC\SMS\PSWinCom;

class Configuration {
    public $endpoint = "https://secure.pswin.com/XMLHttpWrapper/process.aspx";
    public $defaultSender = "2270";
    public $username;
    public $password;
    /**
     * @var bool Set to false to bill as CPA instead of GAS.
     */
    public $isGoodsAndServices = true;
    /**
     * @var int Service code from \DC\SMS\PSWinCom\ServiceCode
     * @see \DC\SMS\PSWinCom\ServiceCode
     */
    public $serviceCode = ServiceCode::Other;
}