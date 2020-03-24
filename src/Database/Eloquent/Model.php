<?php

namespace Recoded\WpQuery\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as IlluminateModel;
use Recoded\WpQuery\Database\Concerns\HasRelationships;

abstract class Model extends IlluminateModel
{
    use HasRelationships;

    const CREATED_AT = 'date_gmt';
    const UPDATED_AT = 'modified_gmt';

    protected $connection = 'wordpress';

    /**
     * Get the database connection for the model.
     *
     * @return \Illuminate\Database\Connection|\Recoded\WpQuery\WordpressConnection
     */
    public function getConnection()
    {
        return static::resolveConnection($this->getConnectionName());
    }
}
