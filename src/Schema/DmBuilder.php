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

namespace Hyperf\Database\Dm\Schema;

use Hyperf\Database\Exception\InvalidArgumentException;
use Hyperf\Database\Schema\Builder;
use Hyperf\Database\Connection;
use Closure;
use Hyperf\Support\Fluent;

class DmBuilder extends Builder
{
    /**
     * @var \Yajra\Oci8\Schema\Comment
     */
    public $comment;

    /**
     * @param  Connection  $connection
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
        $this->comment = new Comment($connection);
    }

    /**
     * Create a new table on the schema.
     *
     * @param  string  $table
     * @param  Closure  $callback
     * @return \Illuminate\Database\Schema\Blueprint
     */
    public function create($table, Closure $callback): void
    {
        $blueprint = $this->createBlueprint($table);

        $blueprint->create();

        $callback($blueprint);

        $this->build($blueprint);

        $this->comment->setComments($blueprint);

    }

    /**
     * Create a new command set with a Closure.
     *
     * @param  string  $table
     * @param  Closure  $callback
     * @return \Illuminate\Database\Schema\Blueprint
     */
    protected function createBlueprint($table, Closure $callback = null)
    {
        $blueprint = new DmBlueprint($table, $callback);
        $blueprint->setTablePrefix($this->connection->getTablePrefix());

        return $blueprint;
    }

    /**
     * Changes an existing table on the schema.
     *
     * @param  string  $table
     * @param  Closure  $callback
     * @return \Illuminate\Database\Schema\Blueprint
     */
    public function table($table, Closure $callback): void
    {
        $blueprint = $this->createBlueprint($table);

        $callback($blueprint);

        $this->build($blueprint);

        $this->comment->setComments($blueprint);
    }

    /**
     * Drop a table from the schema.
     *
     * @param  string  $table
     * @return \Illuminate\Database\Schema\Blueprint
     */
    public function drop($table): void
    {

        $blueprint = $this->createBlueprint($table);
        $this->grammar->compileDrop($blueprint, $this->addCommand('drop'));
        // parent::drop($table);
    }

    /**
     * Drop all tables from the database.
     *
     * @return void
     */
    public function dropAllTables(): void
    {
        $this->connection->statement($this->grammar->compileDropAllTables());
    }

    /**
     * Indicate that the table should be dropped if it exists.
     *
     * @param  string  $table
     * @return \Illuminate\Support\Fluent
     */
    public function dropIfExists($table): void
    {

        $blueprint = $this->createBlueprint($table);
        $this->connection->statement($this->grammar->compileDropIfExists($blueprint, $this->addCommand('dropIfExists')));
        // parent::dropIfExists($table);
    }

    /**
     * Determine if the given table exists.
     *
     * @param  string  $table
     * @return bool
     */
    public function hasTable($table): bool
    {
        /** @var \Yajra\Oci8\Schema\Grammars\OracleGrammar $grammar */
        $grammar = $this->grammar;
        $sql = $grammar->compileTableExists();

        $database = $this->connection->getConfig('username');
        if ($this->connection->getConfig('prefix_schema')) {
            $database = $this->connection->getConfig('prefix_schema');
        }
        $table = $this->connection->getTablePrefix().$table;

        return count($this->connection->select($sql, [$database, $table])) > 0;
    }

    /**
     * Get the column listing for a given table.
     *
     * @param  string  $table
     * @return array
     */
    public function getColumnListing($table): array
    {
        $database = $this->connection->getConfig('username');
        $table = $this->connection->getTablePrefix().$table;
        /** @var \Yajra\Oci8\Schema\Grammars\OracleGrammar $grammar */
        $grammar = $this->grammar;
        $results = $this->connection->select($grammar->compileColumnExists($database, $table));

        return $this->connection->getPostProcessor()->processColumnListing($results);
    }


    /**
     * Get all the table names for the database.
     *
     * @return array
     */
    public function getAllTables()
    {
        return $this->connection->select(
            $this->grammar->compileGetAllTables()
        );
    }


    /**
     * Add a new command to the blueprint.
     *
     * @param string $name
     * @return Fluent
     */
    protected function addCommand($name, array $parameters = [])
    {
        $this->commands[] = $command = $this->createCommand($name, $parameters);

        return $command;
    }

    /**
     * Create a new Fluent command.
     *
     * @param string $name
     * @return Fluent
     */
    protected function createCommand($name, array $parameters = [])
    {
        return new Fluent(array_merge(compact('name'), $parameters));
    }
}