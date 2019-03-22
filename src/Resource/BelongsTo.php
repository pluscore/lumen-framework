<?php

namespace Plus\Resource;

use Zttp\Zttp;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\App;

class BelongsTo
{
    /**
     * The host name to service.
     */
    protected $serviceHostName;

    /**
     * The resource path to service.
     */
    protected $resourcePath;

    /**
     * The child model instance of the relation.
     */
    protected $child;

    /**
     * The foreign key of the parent model.
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * The associated key on the parent model.
     *
     * @var string
     */
    protected $ownerKey;

    /**
     * The name of the relationship.
     *
     * @var string
     */
    protected $relationName;

    /**
     * Params that filtering the service.
     *
     * @var array
     */
    protected $params;

    /**
     * Create a new belongs to relationship instance.
     *
     * @param  string  $serviceHostName
     * @param  string  $resourcePath
     * @param  string  $foreignKey
     * @param  string  $ownerKey
     * @param  string  $relationName
     *
     * @return void
     */
    public function __construct($serviceHostName, $resourcePath, EloquentModel $child, $foreignKey, $ownerKey, $relationName)
    {
        $this->serviceHostName = $serviceHostName;
        $this->resourcePath = $resourcePath;
        $this->ownerKey = $ownerKey;
        $this->relationName = $relationName;
        $this->foreignKey = $foreignKey;

        // In the underlying base relationship class, this variable is referred to as
        // the "parent" since most relationships are not inversed. But, since this
        // one is we will create a "child" variable for much better readability.
        $this->child = $child;
        $this->params = [];
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        $this->params[$this->ownerKey] = array_unique(
            array_pluck($models, $this->foreignKey)
        );
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        $foreign = $this->foreignKey;

        $owner = $this->ownerKey;

        // First we will get to build a dictionary of the child models by their primary
        // key of the relationship, then we can easily match the children back onto
        // the parents using that dictionary and the primary key of the children.
        $dictionary = [];

        foreach ($results as $result) {
            $dictionary[$result[$owner]] = new Model($result);
        }

        // Once we have the dictionary constructed, we can loop through all the parents
        // and match back onto their children using these keys of the dictionary and
        // the primary key of the children to map them onto the correct instances.
        foreach ($models as $model) {
            if (isset($dictionary[$model->{$foreign}])) {
                $model->setRelation($relation, $dictionary[$model->{$foreign}]);
            }
        }

        return $models;
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array   $models
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }

        return $models;
    }

    /**
     * Get the relationship for eager loading.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEager()
    {
        return $this->get();
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($columns = ['*'])
    {
        $url = $this->getUri().'?'.http_build_query($this->params);

        try {
            $response = Zttp::withHeaders(['Accept' => 'application/json'])->get($url);

            if ($response->isSuccess()) {
                return collect($response->json()['data']);
            }

            throw new \RuntimeException($response->json());
        } catch (\Zttp\ConnectionException $exception) {
            if (! App::environment('testing')) {
                throw $exception;
            }

            return collect();
        }
    }

    /**
     * Get uri to the resource.
     *
     * @return string
     */
    public function getUri()
    {
        return "http://{$this->serviceHostName}/{$this->resourcePath}";
    }
}