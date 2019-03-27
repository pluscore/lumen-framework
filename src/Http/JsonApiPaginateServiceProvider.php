<?php

namespace Plus\Http;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

class JsonApiPaginateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerMacro();
    }

    protected function registerMacro()
    {
        Builder::macro('jsonPaginate', function (int $maxResults = null, int $defaultSize = null) {
            $maxResults = $maxResults ?? 30;
            $defaultSize = $defaultSize ?? 30;
            $numberParameter = 'number';
            $sizeParameter = 'size';

            $size = (int) Request::input('page.'.$sizeParameter, $defaultSize);

            $size = $size > $maxResults ? $maxResults : $size;

            $paginator = $this
                ->paginate($size, ['*'], 'page.'.$numberParameter)
                ->setPageName('page['.$numberParameter.']')
                ->appends(Arr::except(Request::input(), 'page.'.$numberParameter));

            return $paginator;
        });
    }
}
