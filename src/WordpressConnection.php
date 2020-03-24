<?php

namespace Recoded\WpQuery;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Connection;
use Illuminate\Support\Arr;
use Recoded\WpQuery\Database\Query\Builder;
use Recoded\WpQuery\Database\Query\Grammar;
use Recoded\WpQuery\Exceptions\WordpressHttpException;

class WordpressConnection extends Connection
{
    protected ClientInterface $client;
    protected string $endpoint;

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(array $config, string $name)
    {
        if (!isset($config['endpoint']) || !filter_var($config['endpoint'], FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid endpoint specified for Wordpress connection ' . $name);
        }

        $this->config = $config;
        $this->endpoint = $config['endpoint'];
        $this->client = app(Client::class);

        $this->useDefaultPostProcessor();
        $this->useDefaultQueryGrammar();
        $this->useDefaultSchemaGrammar();
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    protected function getDefaultQueryGrammar()
    {
        return new Grammar();
    }

    public function getDriverName()
    {
        return 'wordpress';
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function query()
    {
        return new Builder(
            $this,
            $this->getQueryGrammar(),
            $this->getPostProcessor(),
        );
    }

    public function select($query, $bindings = [], $useReadPdo = true)
    {
        $request = new Request('GET', $query);

        try {
            $response = $this->getClient()->send($request);
            $body = $response->getBody()->getContents();

            return Arr::wrap(
                json_decode($body),
            );
        } catch (GuzzleException $e) {
            throw new WordpressHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
