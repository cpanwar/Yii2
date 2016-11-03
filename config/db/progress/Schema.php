<?php
namespace exchangecore\yii2\progress\driver\db\progress;

use yii;
use yii\db\TableSchema;
use yii\db\ColumnSchema;

class Schema extends yii\db\Schema
{
    /**
     * @var string the default schema used for the current session.
     */
    public $defaultSchema = 'PUB';
    /**
     * @var array mapping from physical column types (keys) to abstract column types (values)
     */
    public $typeMap = [
        // exact numbers
        'bigint' => self::TYPE_BIGINT,
        'numeric' => self::TYPE_DECIMAL,
        'bit' => self::TYPE_SMALLINT,
        'smallint' => self::TYPE_SMALLINT,
        'decimal' => self::TYPE_DECIMAL,
        'smallmoney' => self::TYPE_MONEY,
        'integer' => self::TYPE_INTEGER,
        'tinyint' => self::TYPE_SMALLINT,
        'money' => self::TYPE_MONEY,
        // approximate numbers
        'float' => self::TYPE_FLOAT,
        'real' => self::TYPE_FLOAT,
        // date and time
        'date' => self::TYPE_DATE,
        'datetimeoffset' => self::TYPE_DATETIME,
        'datetime2' => self::TYPE_DATETIME,
        'smalldatetime' => self::TYPE_DATETIME,
        'datetime' => self::TYPE_DATETIME,
        'time' => self::TYPE_TIME,
        // character strings
        'char' => self::TYPE_STRING,
        'varchar' => self::TYPE_STRING,
        'text' => self::TYPE_TEXT,
        // unicode character strings
        'nchar' => self::TYPE_STRING,
        'nvarchar' => self::TYPE_STRING,
        'ntext' => self::TYPE_TEXT,
        // binary strings
        'binary' => self::TYPE_BINARY,
        'varbinary' => self::TYPE_BINARY,
        'image' => self::TYPE_BINARY,
        // other data types
        // 'cursor' type cannot be used with tables
        'timestamp' => self::TYPE_TIMESTAMP,
        'hierarchyid' => self::TYPE_STRING,
        'uniqueidentifier' => self::TYPE_STRING,
        'sql_variant' => self::TYPE_STRING,
        'xml' => self::TYPE_STRING,
        'table' => self::TYPE_STRING,
    ];

    public $collationCaseMap = [
        'basic_I' => CASE_UPPER,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->db->enableSavepoint = false;
    }

    /**
     * @inheritdoc
     */
    public function quoteSimpleTableName($name)
    {
        return strpos($name, '"') !== false ? $name : '"' . $name . '"';
    }

    /**
     * @inheritdoc
     */
    public function createQueryBuilder()
    {
        return new QueryBuilder($this->db);
    }

    /**
     * @inheritdoc
     */
    public function loadTableSchema($name)
    {
        $table = new yii\db\TableSchema();
        $this->resolveTableNames($table, $name);
        $this->findPrimaryKeys($table);
        if ($this->findColumns($table)) {
            $this->findForeignKeys($table);

            return $table;
        } else {
            return null;
        }
    }

    /**
     * Resolves the table name and schema name (if any).
     * @param TableSchema $table the table metadata object
     * @param string $name the table name
     */
    protected function resolveTableNames($table, $name)
    {
        $parts = explode('.', $name);
        $partCount = count($parts);
        if ($partCount == 3) {
            // catalog name, schema name and table name passed
            $table->catalogName = $parts[0];
            $table->schemaName = $parts[1];
            $table->name = $parts[2];
            $table->fullName = $table->catalogName . '.' . $table->schemaName . '.' . $table->name;
        } elseif ($partCount == 2) {
            // only schema name and table name passed
            $table->schemaName = $parts[0];
            $table->name = $parts[1];
            $table->fullName = $table->schemaName !== $this->defaultSchema ? $table->schemaName . '.' . $table->name : $table->name;
        } else {
            // only table name passed
            $table->schemaName = $this->defaultSchema;
            $table->fullName = $table->name = $parts[0];
        }
    }

    protected function createColumnSchema()
    {
        return Yii::createObject('exchangecore\yii2\progress\driver\db\progress\ColumnSchema');
    }

    /**
     * Loads the column information into a [[ColumnSchema]] object.
     * @param array $info column information
     * @return ColumnSchema the column schema object
     */
    protected function loadColumnSchema($info)
    {
        $column = $this->createColumnSchema();

        $column->name = $info['COL'];
        $column->allowNull = $info['NULLFLAG'] == 'Y';
        $column->dbType = $info['COLTYPE'];
        $column->enumValues = [];
        $column->isPrimaryKey = null;
        $column->autoIncrement = false;
        $column->unsigned = false;
        $column->comment = $info['LABEL'] === null ? '' : $info['LABEL'];
        $column->size = $info['WIDTH'];

        $column->type = self::TYPE_STRING;
        if (preg_match('/^(\w+)(?:\(([^\)]+)\))?/', $column->dbType, $matches)) {
            $type = $matches[1];
            if (isset($this->typeMap[$type])) {
                $column->type = $this->typeMap[$type];
            }
            if (!empty($matches[2])) {
                $values = explode(',', $matches[2]);
                $column->size = $column->precision = (int) $values[0];
                if (isset($values[1])) {
                    $column->scale = (int) $values[1];
                }
                if ($column->size === 1 && ($type === 'tinyint' || $type === 'bit')) {
                    $column->type = 'boolean';
                } elseif ($type === 'bit') {
                    if ($column->size > 32) {
                        $column->type = 'bigint';
                    } elseif ($column->size === 32) {
                        $column->type = 'integer';
                    }
                }
            }
        }
        $column->case = $this->getCollationCase($info['COLLATION']);
        $column->phpType = $this->getColumnPhpType($column);

        if (!$column->isPrimaryKey && ($column->type !== 'timestamp' || $info['DFLT_VALUE'] !== 'CURRENT_TIMESTAMP')) {
            $column->defaultValue = $column->phpTypecast($info['DFLT_VALUE']);
        }

        return $column;
    }

    /**
     * @param string $collation
     * @returns false if collation is case sensitive, CASE_UPPER if value should be converted to uppercase, CASE_LOWER
     * if value should be converted to lower case
     */
    protected function getCollationCase($collation)
    {
        if(isset($this->collationCaseMap[$collation])) {
            return $this->collationCaseMap[$collation];
        }
        return CASE_UPPER;
    }

    /**
     * Collects the metadata of table columns.
     * @param TableSchema $table the table metadata
     * @throws \yii\base\NotSupportedException
     * @return boolean whether the table exists in the database
     */
    protected function findColumns($table)
    {
        $columnsTableName = 'SYSPROGRESS.SYSCOLUMNS_FULL';
        $columnsTableName = $this->quoteTableName($columnsTableName);
        $whereSql = "t1.TBL = '{$table->name}'";
        if ($table->schemaName !== null) {
            $whereSql .= " AND t1.OWNER = '{$table->schemaName}'";
        }
        $sql = "
        SELECT
            t1.COL, t1.NULLFLAG, t1.COLTYPE, t1.DFLT_VALUE, t1.LABEL, t1.WIDTH, t1.COLLATION
        FROM {$columnsTableName} AS t1
        WHERE {$whereSql}
        ";

        try {
            $columns = $this->db->createCommand($sql)->queryAll();
            if (empty($columns)) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        foreach ($columns as $column) {
            $column = $this->loadColumnSchema($column);
            foreach ($table->primaryKey as $primaryKey) {
                if (strcasecmp($column->name, $primaryKey) === 0) {
                    $column->isPrimaryKey = true;
                    break;
                }
            }
            if ($column->isPrimaryKey && $column->autoIncrement) {
                $table->sequenceName = '';
            }
            $table->columns[$column->name] = $column;
        }

        return true;
    }

    /**
     * Collects the primary key column details for the given table.
     * @param TableSchema $table the table metadata
     */
    protected function findPrimaryKeys($table)
    {
        $table->primaryKey = [];
        $primeIndex = $this->db->createCommand(
            'SELECT "_Prime-Index"
            FROM "_File"
            WHERE "_File-Name" = :tableName
            ',
            [':tableName' => $table->name]
        )->queryScalar();
        if (is_null($primeIndex) || $primeIndex === false) {
            return;
        }

        $index = $this->db->createCommand(
            'SELECT ROWID
            FROM "_Index"
            WHERE ROWID = :primeIndex AND "_Unique" = 1
            ',
            [':primeIndex' => $primeIndex]
        )->queryScalar();
        if (is_null($index) || $index === false) {
            return;
        }

        $fieldRecs = $this->db->createCommand(
            'SELECT "_Field-recid", "_Index-Seq"
            FROM "_Index-Field"
            WHERE "_index-recid" = :index',
            [':index' => $index]
        )->queryAll();
        if (empty($fieldRecs)) {
            return;
        }

        $arrHelper = new \yii\helpers\ArrayHelper;
        $arrHelper::multisort($fieldRecs, '_Index-Seq');
        $query = new yii\db\Query();
        $fields = $query->select('"_field-name", ROWID')
            ->from('"_field"')
            ->where(['in', 'ROWID', $arrHelper::getColumn($fieldRecs, "_Field-recid")])
            ->all($this->db);
        foreach ($fieldRecs AS $field) {
            foreach ($fields AS &$f) {
                if ((int)$f['ROWID'] === (int)$field['_Field-recid']) {
                    $table->primaryKey[] = $f['_Field-Name'];
                    unset($f);
                }
            }
        }
    }

    /**
     * Collects the foreign key column details for the given table.
     * @param TableSchema $table the table metadata
     */
    protected function findForeignKeys($table)
    {
        //progress doesn't have foreign keys created with ABL
    }

    /**
     * @inheritdoc
     */
    protected function findTableNames($schema = '')
    {

        if ($schema === '') {
            $schema = $this->defaultSchema;
        }
        $sql = "
        SELECT
            t1.TBL
        FROM SYSPROGRESS.SYSTABLES AS t1
        WHERE t1.OWNER = :schema
        ";

        return $this->db->createCommand($sql, [':schema' => $schema])->queryColumn();
    }

    /**
     * @inheritdoc
     */
    public function setTransactionIsolationLevel($level)
    {
        $this->db->createCommand("SET TRANSACTION ISOLATION LEVEL $level")->execute();
    }
}
