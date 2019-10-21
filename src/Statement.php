<?php
namespace Swango\Aliyun\Log;
class Statement implements \Iterator, \Countable {
    private $client, $finished, $current, $load_all_data, $position;
    public function __construct(Action\GetLogs $client) {
        $this->client = $client;
        $this->finished = false;
        $this->load_all_data = new \SplQueue();
        $this->position = 0;
    }
    public function __destruct() {
        $this->client = null;
        $this->load_all_data = null;
    }
    /**
     * 返回一个包含结果集中剩余所有行的数组
     *
     * @return array|NULL
     */
    public function toArray(): array {
        $this->rewind();
        $this->finished = true;
        if (isset($this->current)) {
            $ret = [
                $this->current
            ];
            while ( ! $this->load_all_data->isEmpty() )
                $ret[] = $this->load_all_data->dequeue();
            return $ret;
        } else {
            return [];
        }
    }
    public function current() {
        if (! isset($this->current))
            $this->rewind();
        // if (isset($this->current->scalar))
        return $this->current;
    }
    public function next(): void {
        if (! $this->finished) {
            ++ $this->position;
            if ($this->load_all_data->isEmpty()) {
                $this->current = null;
                $this->finished = true;
            } else {
                $this->current = $this->load_all_data->dequeue();
            }
        }
    }
    public function key(): int {
        return $this->position;
    }
    public function valid(): bool {
        if ($this->finished && isset($this->current))
            $this->current = null;
        return ! $this->finished;
    }
    public function rewind() {
        if ($this->finished || $this->client === null)
            return;

        foreach ($this->client->recvResult() as $row) {
            if (property_exists($row, '__time__'))
                unset($row->{'__time__'});
            if (property_exists($row, '__source__'))
                unset($row->{'__source__'});
            $this->load_all_data->enqueue($row);
        }
        $this->client = null;

        if ($this->load_all_data->isEmpty()) {
            $this->current = null;
            $this->finished = true;
        } else {
            $this->current = $this->load_all_data->dequeue();
        }
    }
    public function count() {
        return $this->load_all_data->count();
    }
}