<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Presenters\Models\Shipping;

use Kabooodle\Models\ShippingTransactions;
use Kabooodle\Presenters\PresenterAbstract;

/**
 * Class ShippingTransactionPresenter
 * @package Kabooodle\Presenters\Models\Shipping
 */
class ShippingTransactionPresenter extends PresenterAbstract
{
    /**
     * @return string
     */
    public function getStatus()
    {
        /** @var ShippingTransactions $entity */
        $entity = $this->entity;

        return $this->mapStatusAndReturnLink($entity->getLatestHistory());
    }

    /**
     * @param string $status
     *
     * @return array
     */
    public function mapStatus(string $status)
    {
        switch ($status) {
            case 'LABEL PRINTED':
                $class = 'deep-purple-500';
                $text = 'Label Created';
                break;
            case 'IN TRANSIT':
                $class = 'blue-500';
                $text = 'In Transit';
                break;
            case 'DELIVERED':
                $class = 'green-500';
                $text = 'Delivered!';
                break;
            case 'RETURNED':
                $class = 'pink-500';
                $text = 'Returned';
                break;

            case 'UNKNOWN':
            case null:
            default:
                $class = 'grey-500';
                $text = 'Shipment Created';
                break;
        }

        return [
            $class,
            $text
        ];
    }

    /**
     * @param $status
     *
     * @return string
     */
    public function mapStatusAndReturnLink($status)
    {
        list($class, $text) = $this->mapStatus($status);
        return '<span class="w-8 rounded '.$class.'" style="margin-right: 3px"></span> <span class="text-'.$class.'" >'.$text.'</span>';
    }
}
