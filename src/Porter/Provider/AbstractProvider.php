<?php
namespace ScriptFUSION\Porter\Provider;

use ScriptFUSION\Porter\Cache\CacheToggle;
use ScriptFUSION\Porter\Cache\CacheUnavailableException;
use ScriptFUSION\Porter\Connector\Connector;
use ScriptFUSION\Porter\Provider\DataSource\ProviderDataSource;

abstract class AbstractProvider implements Provider, CacheToggle
{
    private $connector;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @param ProviderDataSource $dataSource
     *
     * @return \Iterator
     *
     * @throws ForeignDataSourceException A foreign data source was received.
     */
    public function fetch(ProviderDataSource $dataSource)
    {
        if ($dataSource->getProviderClassName() !== static::class) {
            throw new ForeignDataSourceException(sprintf(
                'Cannot fetch data from foreign source: "%s".',
                get_class($dataSource)
            ));
        }

        return $dataSource->fetch($this->connector);
    }

    /**
     * @return Connector
     */
    public function getConnector()
    {
        return $this->connector;
    }

    public function enableCache()
    {
        $connector = $this->getConnector();

        if (!$connector instanceof CacheToggle) {
            throw CacheUnavailableException::modify();
        }

        $connector->enableCache();
    }

    public function disableCache()
    {
        $connector = $this->getConnector();

        if (!$connector instanceof CacheToggle) {
            throw CacheUnavailableException::modify();
        }

        $connector->disableCache();
    }

    public function isCacheEnabled()
    {
        $connector = $this->getConnector();

        if (!$connector instanceof CacheToggle) {
            return false;
        }

        return $connector->isCacheEnabled();
    }
}