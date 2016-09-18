<?php

namespace Kabooodle\Services\Shippr;

/**
 * Class RatesObject
 * @package Kabooodle\Services\Shippr
 */
class RatesObject
{
    protected $state;
    /**
     * @var
     */
    protected $rateId;

    /**
     * @var
     */
    protected $shipmentId;

    /**
     * @var
     */
    protected $amount;
    protected $provider;
    protected $serviceLevelName;
    protected $serviceLevelToken;
    protected $days;
    protected $arrivesBy;
    protected $durationTerms;
    protected $isTrackable;

    /**
     * @var array
     */
    protected $carrierLogos = [
        'small' => '',
        'large' => ''
    ];

    /**
     * RatesObject constructor.
     *
     * @param array $shippoRate
     */
    public function __construct(array $shippoRate)
    {
        $this->shippoRateObject = $shippoRate;
        $this->mapValuesToProperties($shippoRate);
    }

    /**
     * @param array $shippo
     */
    protected function mapValuesToProperties(array $shippo)
    {
        $this->state = $shippo['object_state'];
        $this->rateId = $shippo['object_id'];
        $this->shipmentId = $shippo['shipment'];
        $this->amount = $shippo['amount'];
        $this->provider = $shippo['provider'];
        $this->serviceLevelName = $shippo['servicelevel_name'];
        $this->serviceLevelToken = $shippo['servicelevel_token'];
        $this->days = $shippo['days'];
        $this->arrivesBy = $shippo['arrives_by'];
        $this->durationTerms = $shippo['duration_terms'];
        $this->isTrackable = $shippo['trackable'];
        $this->carrierLogos['small'] = $shippo['provider_image_75'];
        $this->carrierLogos['large'] = $shippo['provider_image_200'];
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return mixed
     */
    public function getRateId()
    {
        return $this->rateId;
    }

    /**
     * @return mixed
     */
    public function getShipmentId()
    {
        return $this->shipmentId;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return mixed
     */
    public function getServiceLevelName()
    {
        return $this->serviceLevelName;
    }

    /**
     * @return mixed
     */
    public function getServiceLevelToken()
    {
        return $this->serviceLevelToken;
    }

    /**
     * @return mixed
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @return mixed
     */
    public function getArrivesBy()
    {
        return $this->arrivesBy;
    }

    /**
     * @return mixed
     */
    public function getDurationTerms()
    {
        return $this->durationTerms;
    }

    /**
     * @return mixed
     */
    public function getIsTrackable()
    {
        return $this->isTrackable;
    }

    /**
     * @return array
     */
    public function getCarrierLogos()
    {
        return $this->carrierLogos;
    }
}