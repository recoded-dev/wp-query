<?php

namespace Recoded\WpQuery\Database\Query;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Query\Builder as IlluminateBuilder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Support\Arr;
use Recoded\WpQuery\Exceptions\WordpressHttpException;
use Recoded\WpQuery\WordpressConnection;

class Builder extends IlluminateBuilder
{
    public $bindings = [
        'where' => [],
        'order' => [],
    ];

    public string $endpoint;
    public ?string $namespace;

    public $operators = [
        '=',
    ];

    public array $query = [];
    public array $url = [];
    public ?string $version;

    public const LIMIT_QUERY = 'limit';
    public const OFFSET_QUERY = 'offset';

    public function __construct(WordpressConnection $connection, Grammar $grammar = null, Processor $processor = null)
    {
        parent::__construct($connection, $grammar, $processor);

        $this->endpoint = $connection->getEndpoint();
    }

    public function count($columns = '*')
    {
        $request = new Request(
            'HEAD',
            $this->toSql(),
        );

        try {
            $response = $this->getConnection()->getClient()->send($request);

            return (int) last($response->getHeader('X-WP-Total'));
        } catch (GuzzleException $e) {
            throw new WordpressHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getCountForPagination($columns = ['*'])
    {
        return $this->count();
    }

    public function limit($value)
    {
        if (!is_int($value) || $value <= 0) {
            unset($this->query[static::LIMIT_QUERY]);

            return $this;
        }

        $this->query[static::LIMIT_QUERY] = $value;

        return $this;
    }

    public function namespace(?string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function offset($value)
    {
        if (!is_int($value) || $value <= 0) {
            unset($this->query[static::OFFSET_QUERY]);

            return $this;
        }

        $this->query[static::OFFSET_QUERY] = $value;

        return $this;
    }

    public function postType(string $postType): self
    {
        $this->url = [$postType];

        return $this;
    }

    /**
     * @param string $namespace
     * @param string $version
     *
     * @return \Recoded\WpQuery\Database\Query\Builder
     */
    public function repository(string $namespace, string $version)
    {
        return $this
            ->namespace($namespace)
            ->version($version);
    }

    public function version(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and'): self
    {
        if (!$column) {
            throw new \InvalidArgumentException('Column name required');
        }

        if (func_num_args() <= 2) {
            $value = $operator;
            $operator = '=';
        }

        if (!in_array($operator, $this->operators)) {
            throw new \InvalidArgumentException('Invalid operator ' . $operator);
        }

        $this->query[$column] = implode(',', Arr::wrap($value));

        return $this;
    }
}
