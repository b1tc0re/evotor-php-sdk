<?php

namespace Kily\API\Evotor;

use Psr\Http\Message\ResponseInterface;
use DusanKasan\Knapsack\Collection;

class Response
{
    protected $response;
    protected $clnt;
    protected $filter;
    protected $cursor = null;

    private $arr;

    public function __construct(Client $clnt, ResponseInterface $resp, $filter = null)
    {
        $this->client = $clnt;
        $this->response = $resp;
        $this->filter = $filter;
    }

    public function __toString()
    {
        return $this->response->getBody()->__toString();
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getCursor()
    {
        return $this->cursor;
    }

    public function toArray()
    {
        $this->cursor = null;

        if (!$this->arr) {
            $data = json_decode($this->response->getBody(), true);
            $this->arr = $data['items'] ?? $data;

            if( array_key_exists('paging', $data) && array_key_exists('next_cursor', $data['paging']) ) {
                $this->cursor = $data['paging']['next_cursor'];
            }
        }

        return $this->arr;
    }

    public function first()
    {
        return $this->toArray()[0] ?? null;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([Collection::from($this->toArray() ?: []),$name], $arguments);
    }
}
