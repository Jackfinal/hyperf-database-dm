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
namespace Hyperf\Database\Dm;
use Hyperf\Database\Dm\Listener\RegisterConnectionListener;
use Hyperf\Database\Dm\Connectors\DmConnector;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'db.connector.dm' => DmConnector::class,
            ],
            'listeners' => [
                RegisterConnectionListener::class,
            ]
        ];
    }
}
