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

use Hyperf\Database\Connection;
use Hyperf\Database\Schema\Grammars\Grammar;
use Hyperf\Database\Dm\DmReservedWords;
use Hyperf\Database\Dm\Schema\Grammars\DmGrammar;

class Comment extends DmGrammar
{
    use DmReservedWords;
    /**
     * @var \Hyperf\Database\Connection
     */
    protected $connection;

    /**
     * @param  Connection  $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->setSchemaPrefix($this->connection->getConfig('database'));
    }

    /**
     * Set table and column comments.
     *
     * @param  \Yajra\Oci8\Schema\DmBlueprint  $blueprint
     */
    public function setComments(DmBlueprint $blueprint)
    {
        $this->commentTable($blueprint);

        $this->fluentComments($blueprint);

        $this->commentColumns($blueprint);
    }

    /**
     * Run the comment on table statement.
     * Comment set by $table->comment = 'comment';.
     *
     * @param  \Yajra\Oci8\Schema\DmBlueprint  $blueprint
     */
    private function commentTable(DmBlueprint $blueprint)
    {
        $table = $this->getSchemaPrefix() . $this->wrapValue($blueprint->getTable());
        if ($blueprint->comment != null) {
            $this->connection->statement("comment on table {$table} is '{$blueprint->comment}'");
        }
    }

    /**
     * Wrap reserved words.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapValue($value): string
    {
        return $this->isReserved($value) ? parent::wrapValue($value) : $value;
    }

    /**
     * Add comments set via fluent setter.
     * Comments set by $table->string('column')->comment('comment');.
     *
     * @param  \Yajra\Oci8\Schema\DmBlueprint  $blueprint
     */
    private function fluentComments(DmBlueprint $blueprint)
    {
        foreach ($blueprint->getColumns() as $column) {
            if (isset($column['comment'])) {
                $this->commentColumn($blueprint->getTable(), $column['name'], $column['comment']);
            }
        }
    }

    /**
     * Run the comment on column statement.
     *
     * @param  string  $table
     * @param  string  $column
     * @param  string  $comment
     */
    private function commentColumn($table, $column, $comment)
    {
        $table = $this->wrapValue($table);
        $table = $this->getSchemaPrefix() . $this->connection->getTablePrefix().$table;
        $column = $this->wrapValue($column);

        $this->connection->statement("comment on column {$table}.{$column} is '{$comment}'");
    }

    /**
     * Add comments on columns.
     * Comments set by $table->commentColumns = ['column' => 'comment'];.
     *
     * @param  \Yajra\Oci8\Schema\DmBlueprint  $blueprint
     */
    private function commentColumns(DmBlueprint $blueprint)
    {
        foreach ($blueprint->commentColumns as $column => $comment) {
            $this->commentColumn($blueprint->getTable(), $column, $comment);
        }
    }
}