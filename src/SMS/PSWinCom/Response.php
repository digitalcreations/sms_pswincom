<?php

namespace DC\SMS\PSWinCom;

class Response implements \DC\SMS\ResponseInterface {
    /**
     * @var bool
     */
    private $success;
    /**
     * @var string
     */
    private $id;

    /**
     * @param bool $success
     * @param string $id
     */
    function __construct($success, $id)
    {
        $this->success = $success;
        $this->id = $id;
    }

    /**
     * @return int
     */
    function getHTTPResponseCode()
    {
        return $this->success ? 200 : 500;
    }

    /**
     * @return string[]
     */
    function getHeaders()
    {
        return [
            "Content-Type" => "text/xml"
        ];
    }

    /**
     * @return string
     */
    function getContent()
    {
        $xml = <<<EOXML
<?xml version="1.0"?>
<SESSION>
  <ID>$this->id</ID>
  <STATUS>%s</STATUS>
</SESSION>
EOXML;
        return sprintf($xml, $this->success ? "OK" : "FAIL");
    }
}