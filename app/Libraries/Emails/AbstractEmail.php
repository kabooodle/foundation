<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Libraries\Emails;

use Bugsnag;
use Closure;
use SuperClosure\Serializer;
use InvalidArgumentException;
use Illuminate\Queue\QueueManager;
use Kabooodle\Libraries\QueueHelper;
use Illuminate\Contracts\Mail\MailQueue;
use Illuminate\Mail\Jobs\HandleQueuedMessage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class AbstractEmail
 * @package Kabooodle\Libraries\Emails
 */
abstract class AbstractEmail
{
    /**
     * @var MailQueue|mixed|null
     */
    public static $mailer = null;

    /**
     * @var \Illuminate\View\Factory|mixed
     */
    public static $view = null;

    /**
     * @var \Illuminate\Queue\QueueManager
     */
    public static $queue = null;

    /**
     * @var Closure
     */
    public $callable;

    /**
     * @var string
     */
    public $resourceView = null;

    /**
     * @var array
     */
    public $parameters = null;

    /**
     * @var string
     */
    public $queueConnection = 'email-queue';

    /**
     * AbstractEmail constructor.
     *
     * @param string  $resourceView
     * @param array   $parameters
     * @param Closure $callable
     */
    public function __construct(string $resourceView = null, array $parameters = [], Closure $callable = null)
    {
        $this->callable = $callable;
        $this->resourceView = $resourceView;
        $this->parameters = $parameters;

        return $this;
    }

    abstract public function getEmailTemplate();

    /**
     * @param $name
     *
     * @return $this
     */
    public function setQueueConnection($name)
    {
        $this->queueConnection = $name;

        return $this;
    }

    /**
     * @param $resourceView
     *
     * @return $this
     */
    public function setView($resourceView)
    {
        $this->resourceView = $resourceView;

        return $this;
    }

    /**
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param Closure $callable
     *
     * @return $this
     */
    public function setCallable(Closure $callable)
    {
        $this->callable = $callable;

        return $this;
    }

    /**
     * @return Closure
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * @return string
     */
    public function getResourceView()
    {
        return $this->resourceView;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string|null
     */
    public function getQueueConnectionName()
    {
        return $this->queueConnection ? : env('QUEUE_DRIVER');
    }

    /**
     * TODO: Consider passing queue parameter such that queuing can be made optional.
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function send()
    {
        if (! $this->getCallable()) {
            throw new InvalidArgumentException('Missing [callable] when attempting to email.');
        }

        try {
            // The template we will be embedding the email's content into.
            // This is just a dot.object path representation.
            $view = $this->getEmailTemplate();

            // Render the content that should be inserted into the template.
            // Inject the content parameters into this view
            $content = $this->getView()->make($this->getResourceView(), $this->getParameters())->render();

            $data = ['emailContent' => $content] + $this->getParameters();
            $callback =  $this->buildQueueCallable($this->getCallable());

            return $this->getQueueManager()->connection(QueueHelper::pickEmails())
                ->push(new HandleQueuedMessage($view, $data, $callback));
        } catch (ModelNotFoundException $e) {
            Bugsnag::notifyException($e);
        }
    }

    /**
     * @return MailQueue|mixed|null
     */
    public function getMailer()
    {
        if (! self::$mailer) {
            self::$mailer = app()->make(MailQueue::class);
        }

        return self::$mailer;
    }

    /**
     * @return \Illuminate\View\Factory|mixed
     */
    public function getView()
    {
        if (! self::$view) {
            self::$view = app()->make('view');
        }

        return self::$view;
    }

    /**
     * @return QueueManager|mixed
     */
    public function getQueueManager()
    {
        if(! self::$queue){
            self::$queue = app()->make(QueueManager::class);
        }

        return self::$queue;
    }

    /**
     * Build the callable for a queued e-mail job.
     *
     * @param  \Closure|string  $callback
     * @return string
     */
    public function buildQueueCallable($callback)
    {
        if (! $callback instanceof Closure) {
            return $callback;
        }

        return (new Serializer)->serialize($callback);
    }
}
