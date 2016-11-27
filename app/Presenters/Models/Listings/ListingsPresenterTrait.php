<?php

namespace Kabooodle\Presenters\Models\Listings;

/**
 * Class ListingsPresenterTrait
 */
trait ListingsPresenterTrait
{
    /**
     * @return string
     */
    public function getStatus()
    {
        $entity = $this->entity;
        switch ($entity->status) {
            case 'queued':
                $class = 'blue-500';
                $text = 'Queued';
                break;
            case 'partial':
                $class = 'warning';
                $text = 'Partially Listed';
                break;
            case 'queued_delete':
                $class = 'warning';
                $text = 'Queued Delete';
                break;
            case 'ignored_duplicate':
                $class = 'warn';
                $text = 'Ignored Duplicate';
                break;
            case 'deleted':
                $class = 'brown-800';
                $text = 'Deleted';
                break;
            case 'completed':
            case 'success':
                $class = 'green-500';
                $text = 'Completed';
                break;
            case 'scheduled':
            default:
                $class = 'deep-purple-500';
                $text = 'Scheduled';
                break;
        }

        return '<span class="w-8 rounded '.$class.'" style="margin-right: 2px"></span> <span class="text-'.$class.'" >'.$text.'</span>';
    }
}