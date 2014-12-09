<?php

namespace DC\Tests;

class GatewayTest extends \PHPUnit_Framework_TestCase {

    private function getConfiguration() {
        $configuration = new \DC\SMS\PSWinCom\Configuration();
        $configuration->username = "foo";
        $configuration->password = "bar";
        $configuration->defaultSender = "phpunit";
        return $configuration;
    }

    public function testSendMessage()
    {
        $xmlIn = "<?xml version=\"1.0\"?>\n" .
            "<SESSION><CLIENT>foo</CLIENT><PW>bar</PW><MSGLST><MSG><ID>1</ID><TEXT>Does this work?</TEXT><SND>Vegard</SND><RCV>4712345678</RCV><RCPREQ>Y</RCPREQ></MSG></MSGLST></SESSION>\n";
        $xmlOut = <<<EOXML
<?xml version="1.0"?><SESSION><LOGON>OK</LOGON><REASON></REASON><MSGLST><MSG><ID>1</ID><REF>2DB9594B-8251-4647-B468-EB325872031C</REF><STATUS>OK</STATUS><INFO></INFO></MSG></MSGLST></SESSION>
EOXML;

        $mockCaller = $this->getMock('\DC\SMS\PSWinCom\APICaller');
        $mockCaller->expects($this->once())
            ->method('call')
            ->with($this->equalTo($xmlIn))
            ->willReturn($xmlOut);

        $api = new \DC\SMS\PSWinCom\Gateway($this->getConfiguration(), $mockCaller);
        $msg = new \DC\SMS\TextMessage("Does this work?", "4712345678");
        $msg->setSender("Vegard");
        $result = $api->sendMessage($msg);

        $this->assertTrue($result->wasEnqueued());
        $this->assertEquals("2DB9594B-8251-4647-B468-EB325872031C", $result->getMessageIdentifier());
    }


    public function testSendMessageWithTariff()
    {
        $xmlIn = "<?xml version=\"1.0\"?>\n" .
            "<SESSION><CLIENT>foo</CLIENT><PW>bar</PW><MSGLST><MSG><ID>1</ID><TEXT>Does this work?</TEXT><SND>Vegard</SND><RCV>4712345678</RCV><RCPREQ>Y</RCPREQ><TARIFF>100</TARIFF><SERVICECODE>16007</SERVICECODE></MSG></MSGLST></SESSION>\n";
        $xmlOut = <<<EOXML
<?xml version="1.0"?><SESSION><LOGON>OK</LOGON><REASON></REASON><MSGLST><MSG><ID>1</ID><REF>2DB9594B-8251-4647-B468-EB325872031C</REF><STATUS>OK</STATUS><INFO></INFO></MSG></MSGLST></SESSION>
EOXML;

        $mockCaller = $this->getMock('\DC\SMS\PSWinCom\APICaller');
        $mockCaller->expects($this->once())
            ->method('call')
            ->with($this->equalTo($xmlIn))
            ->willReturn($xmlOut);

        $api = new \DC\SMS\PSWinCom\Gateway($this->getConfiguration(), $mockCaller);
        $msg = new \DC\SMS\TextMessage("Does this work?", "4712345678");
        $msg->setSender("Vegard");
        $msg->setTariff(100); // 1 NOK
        $result = $api->sendMessage($msg);

        $this->assertTrue($result->wasEnqueued());
        $this->assertEquals("<?xml version=\"1.0\"?>\n<SESSION><LOGON>OK</LOGON><REASON/><MSGLST><MSG><ID>1</ID><REF>2DB9594B-8251-4647-B468-EB325872031C</REF><STATUS>OK</STATUS><INFO/></MSG></MSGLST></SESSION>\n", $result->getResponseContent());
        $this->assertEquals("2DB9594B-8251-4647-B468-EB325872031C", $result->getMessageIdentifier());
    }

    public function testParseDeliveryReport() {
        $api = new \DC\SMS\PSWinCom\Gateway($this->getConfiguration());

        $dlr = $api->parseDeliveryReport('<?xml version="1.0" encoding="iso-8859-1"?><!DOCTYPE MSGLST SYSTEM "pswincom_report_request.dtd"><MSGLST><MSG><ID>1</ID><REF>529179796</REF><RCV>4799613958</RCV><STATE>DELIVRD</STATE><DELIVERYTIME>2014.10.22 12:38:57</DELIVERYTIME></MSG></MSGLST>');
        $this->assertEquals("529179796", $dlr->getMessageIdentifier());
        $this->assertEquals(\DC\SMS\DeliveryState::Delivered, $dlr->getState());
        $this->assertTrue($dlr->isDelivered());
        $this->assertTrue($dlr->isFinalDeliveryReport());
        $this->assertFalse($dlr->isError());
    }
}
 