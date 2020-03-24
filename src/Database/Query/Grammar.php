<?php

namespace Recoded\WpQuery\Database\Query;

use Illuminate\Database\Query\Grammars\Grammar as IlluminateGrammar;

class Grammar extends IlluminateGrammar
{
    protected $selectComponents = [
        'endpoint',
        'namespace',
        'version',
        'url',
        'query',
    ];

    public function compileEndpoint(Builder $builder, string $endpoint): string
    {
        return $endpoint;
    }

    public function compileNamespace(Builder $builder, string $namespace): string
    {
        return '/' . $namespace;
    }

    public function compileQuery(Builder $builder, array $query): ?string
    {
        $query = http_build_query($query);

        return $query ? '?' . $query : null;
    }

    public function compileUrl(Builder $builder, array $segments): ?string
    {
        $segments = array_filter($segments);

        return $segments ? '/' . implode('/', $segments) : null;
    }

    public function compileVersion(Builder $builder, string $version): string
    {
        return '/' . $version;
    }

    protected function concatenate($segments)
    {
        return implode('', $segments);
    }
}
