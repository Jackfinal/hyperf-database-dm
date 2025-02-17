<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Hyperf\Database\Dm\Listener;

use Hyperf\Database\Connection;
use Hyperf\Database\Dm\DmConnection;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Psr\Container\ContainerInterface;

class RegisterConnectionListener implements ListenerInterface
{
    /**
     * Create a new connection factory instance.
     */
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    /**
     * register dm and dm-swoole need Connector and Connection.
     */
    public function process(object $event): void
    {
        Connection::resolverFor('dm', static function ($connection, $database, $prefix, $config) {
            return new DmConnection($connection, $database, $prefix, $config);
        });
    }
}