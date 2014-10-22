<?php

namespace DC\SMS\PSWinCom;

class DeliveryReport extends \DC\SMS\DeliveryReportBase {

    /**
     * @var \SimpleXMLElement
     */
    private $xml;

    private $stateLookup = [
        "DELIVRD" => \DC\SMS\DeliveryState::Delivered,
        "EXPIRED" => \DC\SMS\DeliveryState::Expired,
        "DELETED" => \DC\SMS\DeliveryState::Deleted,
        "UNDELIV" => \DC\SMS\DeliveryState::Undeliverable,
        "ACCEPTD" => \DC\SMS\DeliveryState::Accepted,
        "UNKNOWN" => \DC\SMS\DeliveryState::UnknownError,
        "REJECTD" => \DC\SMS\DeliveryState::Rejected,
        "FAILED"  => \DC\SMS\DeliveryState::Failed,
        "BARRED"  => \DC\SMS\DeliveryState::BarredPermanent,
        "BARREDT" => \DC\SMS\DeliveryState::BarredTemporary,
        "BARREDC" => \DC\SMS\DeliveryState::BarredPremium,
        "BARREDA" => \DC\SMS\DeliveryState::BarredAge,
        "BARREDP" => \DC\SMS\DeliveryState::BarredPrepaid,
        "ZEROBAL" => \DC\SMS\DeliveryState::BarredZeroBalance,
        "INV_NET" => \DC\SMS\DeliveryState::UnknownNetwork,
        "RETRIEV" => \DC\SMS\DeliveryState::MMSRetrieved,
        "REJECTE" => \DC\SMS\DeliveryState::MMSRejected,
        "FORWARD" => \DC\SMS\DeliveryState::MMSForwardedLastResort,
    ];

    function __construct(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    function getMessageIdentifier()
    {
        return (string)$this->xml->MSG->REF;
    }

    function isFinalDeliveryReport()
    {
        return true;
    }

    function getState()
    {
        $state = (string) $this->xml->MSG->STATE;
        if (isset($this->stateLookup[$state])) {
            return $this->stateLookup[$state];
        }
        return \DC\SMS\DeliveryState::Unknown;
    }

    /**
     * @return \DC\SMS\ResponseInterface
     */
    function getSuccessfullyProcessedResponse()
    {
        return new Response(true, (string)$this->xml->MSG->ID);
    }

    /**
     * @return \DC\SMS\ResponseInterface
     */
    function getErrorInProcessingResponse()
    {
        return new Response(false, (string)$this->xml->MSG->ID);
    }
}