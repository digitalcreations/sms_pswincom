<?php
namespace DC\SMS\PSWinCom;


class APICaller {
    /**
     * @var Configuration
     */
    private $configuration;

    function __construct(Configuration $configuration = null)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string $xml
     * @param $endpoint
     * @throws GatewayException
     * @return string
     */
    public function call($xml, $endpoint) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml', 'Length: ' . count($xml)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new GatewayException(curl_error($ch), curl_errno($ch));
        }
        curl_close($ch);

        return $result;
    }
} 