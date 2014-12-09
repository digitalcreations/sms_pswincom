<?php
namespace DC\SMS\PSWinCom;

class Gateway implements \DC\SMS\GatewayInterface {

    /**
     * @var Configuration
     */
    private $configuration;

    private $apiCaller;

    function __construct(Configuration $configuration, APICaller $apiCaller = null) {
        $this->configuration = $configuration;
        $this->apiCaller = isset($apiCaller) ? $apiCaller : new APICaller($configuration);
    }

    private function call(array $dataArray) {
        $xml = new \SimpleXMLElement('<?xml version="1.0"?><SESSION />');
        $this->arrayToXml($dataArray, $xml);

        $xmlAsString = $xml->asXML();
        $result = $this->apiCaller->call($xmlAsString);
        return simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
    }

    /**
     * @param \DC\SMS\TextMessageInterface $message
     * @return \DC\SMS\MessageReceiptInterface|void
     */
    function sendMessage(\DC\SMS\TextMessageInterface $message) {
        if ($message->getSender() == null) {
            $message->setSender($this->configuration->defaultSender);
        }

        $session = [
            "CLIENT" => $this->configuration->username,
            "PW" => $this->configuration->password,
            "MSGLST" => [
                "MSG" => [
                    "ID" => 1,
                    "TEXT" => $message->getText(),
                    "SND" => $message->getSender(),
                    "RCV" => $message->getReceiver(),
                    "RCPREQ" => "Y",
                ]
            ]
        ];

        if ($message->getShortCode() != null) {
            $session["MSGLST"]["MSG"]["SHORTCODE"] = $message->getShortCode();
        }

        if ($message->getTariff() != null) {
            $session["MSGLST"]["MSG"]["TARIFF"] = $message->getTariff();
            if ($this->configuration->isGoodsAndServices) {
                $session["MSGLST"]["MSG"]["SERVICECODE"] = $this->configuration->serviceCode;
            }
        }

        $result = $this->call($session);
        return new \DC\SMS\MessageReceipt($result->MSGLST[0]->MSG->REF, $result->MSGLST[0]->MSG->STATUS == "OK");
    }

    private function arrayToXml($array, \SimpleXMLElement $xml) {

        foreach($array as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $child = $xml->addChild($key);
                    $this->arrayToXml($value, $child);
                }
                else{
                    $child = $xml->addChild("MSG");
                    $this->arrayToXml($value, $child);
                }
            }
            else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
    }

    /**
     * @param string $data
     * @return \DC\SMS\DeliveryReportInterface
     */
    function parseDeliveryReport($data)
    {
        $xml = new \SimpleXMLElement($data);
        return new DeliveryReport($xml);
    }
}