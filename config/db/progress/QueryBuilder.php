<?php
namespace exchangecore\yii2\progress\driver\db\progress;

use yii\base\NotSupportedException;

class QueryBuilder extends \yii\db\QueryBuilder
{
    /**
     * @var array mapping from abstract column types (keys) to physical column types (values).
     */
    public $typeMap = [
        Schema::TYPE_PK => 'integer CONSTRAINT table_pk NOT NULL PRIMARY KEY',
        Schema::TYPE_BIGPK => 'bigint CONSTRAINT table_pk NOT NULL PRIMARY KEY',
        Schema::TYPE_STRING => 'varchar(255)',
        Schema::TYPE_TEXT => 'text',
        Schema::TYPE_SMALLINT => 'smallint',
        Schema::TYPE_INTEGER => 'integer',
        Schema::TYPE_BIGINT => 'bigint',
        Schema::TYPE_FLOAT => 'float',
        Schema::TYPE_DECIMAL => 'decimal',
        Schema::TYPE_DATETIME => 'datetime',
        Schema::TYPE_TIMESTAMP => 'timestamp',
        Schema::TYPE_TIME => 'time',
        Schema::TYPE_DATE => 'date',
        Schema::TYPE_BINARY => 'binary(1)',
        Schema::TYPE_BOOLEAN => 'bit',
        Schema::TYPE_MONEY => 'decimal(19,4)',
    ];


    /**
     * @inheritdoc
     */
    public function buildOrderByAndLimit($sql, $orderBy, $limit, $offset)
    {

        $orderBy = $this->buildOrderBy($orderBy);
        if ($orderBy !== '') {
            $sql .= $this->separator . $orderBy;
        }

        if ($this->hasLimit($limit)) {
            $find = '/^SELECT /';
            $replace = 'SELECT TOP ' . $limit . ' ';
            $sql = preg_replace($find, $replace, $sql, 1);
        }
        if($this->hasOffset($offset)) {
            throw new NotSupportedException('OpenEdge Drivers before version 11.2 do not support offset capabilities');
        }

        return $sql;
    }

    /**
     * @param integer $limit
     * @param integer $offset
     * @throws \yii\base\NotSupportedException
     * @return string the LIMIT and OFFSET clauses
     */
    public function buildLimit($limit, $offset)
    {
        throw new NotSupportedException('The buildLimit function is not implemented, please use buildOrderByAndLimit');
    }
    
    /**
     * @inheritdoc
     */
    protected function buildCompositeInCondition($operator, $columns, $values, &$params)
    {
        $quotedColumns = [];
        foreach ($columns as $i => $column) {
            $quotedColumns[$i] = strpos($column, '(') === false ? $this->db->quoteColumnName($column) : $column;
        }
        $vss = [];
        foreach ($values as $value) {
            $vs = [];
            foreach ($columns as $i => $column) {
                if (isset($value[$column])) {
                    $phName = self::PARAM_PREFIX . count($params);
                    $params[$phName] = $value[$column];
                    $vs[] = $quotedColumns[$i] . ($operator === 'IN' ? ' = ' : ' != ') . $phName;
                } else {
                    $vs[] = $quotedColumns[$i] . ($operator === 'IN' ? ' IS' : ' IS NOT') . ' NULL';
                }
            }
            $vss[] = '(' . implode($operator === 'IN' ? ' AND ' : ' OR ', $vs) . ')';
        }
        return '(' . implode($operator === 'IN' ? ' OR ' : ' AND ', $vss) . ')';
    }
}
