<?php
namespace DC\SMS\PSWinCom;

class Configuration {
    /**
     * @var array|string Single or multiple URLs to try to post to. If one fails, go to the next one on the list.
     */
    public $endpoint = [
        "https://secure.pswin.com/XMLHttpWrapper/process.aspx",
        "https://secure-backup.pswin.com/XMLHttpWrapper/process.aspx",
        "http://sms3.pswin.com/sms",
        "http://sms3-backup.pswin.com/sms"
    ];
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