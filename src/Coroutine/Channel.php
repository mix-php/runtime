<?php

namespace Mix\Coroutine;

/**
 * Class Channel
 * @package Mix\Coroutine
 */
class Channel extends \Swoole\Coroutine\Channel
{

    /**
     * @var \Swoole\Coroutine\Channel[]
     */
    protected $notifies = [];

    /**
     * Push
     * @param $data
     * @param null $timeout
     * @return mixed
     */
    public function push($data, $timeout = null)
    {
        if ($this->isFull()) {
            foreach ($this->notifies as $channel) {
                $channel->push(true);
            }
        }
        $result = parent::push($data, $timeout);
        foreach ($this->notifies as $channel) {
            $channel->push(true);
        }
        return $result;
    }

    /**
     * Pop
     * @param null $timeout
     * @return mixed
     */
    public function pop($timeout = null)
    {
        foreach ($this->notifies as $channel) {
            $channel->push(true);
        }
        return parent::pop($timeout);
    }

    /**
     * Add Notifier
     * @param \Swoole\Coroutine\Channel $channel
     */
    public function addNotifier(\Swoole\Coroutine\Channel $channel)
    {
        $id                  = spl_object_id($channel);
        $this->notifies[$id] = $channel;
    }

    /**
     * Del Notifier
     * @param \Swoole\Coroutine\Channel $channel
     */
    public function delNotifier(\Swoole\Coroutine\Channel $channel)
    {
        $id = spl_object_id($channel);
        unset($this->notifies[$id]);
    }

}
