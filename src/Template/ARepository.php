<?php

    namespace Kentron\Template;

    use Kentron\Template\AModel as AppModel;

    use \DateTime;

    abstract class ARepository
    {
        /**
         * The model
         * @var AppModel
         */
        protected $model;

        protected $ormModel;

        protected $pagniator;

        /**
         * All of the available clause operators.
         *
         * @var array
         */
        protected $operators = [
            '=', '<', '>', '<=', '>=', '<>', '!=',
            'like', 'like binary', 'not like', 'between', 'ilike',
            '&', '|', '^', '<<', '>>',
            'rlike', 'regexp', 'not regexp',
            '~', '~*', '!~', '!~*', 'similar to',
            'not similar to'
        ];

        /**
         * AbstractRepository constructor.
         * @param null $model
         */
        public function __construct($model = null)
        {
            if (($model instanceof $this->ormModel)) {
                $this->model = $model;
            } else {
                $this->model = new $this->ormModel();
            }
        }

        /**
         * Delete
         *
         * @return bool|int|mixed|null
         */
        public function delete()
        {
            return $this->model->delete();
        }

        /**
         * Update
         *
         * @param array $array
         */
        public function update(array $array)
        {
            $this->model->update($array);
        }

        /**
         * Find
         *
         * @param $id
         * @param array $columns
         */
        public function find($id, $columns = ['*'])
        {
            $this->model = $this->model->find($id, $columns);
        }

        /**
         * Find Array
         *
         * @param $id
         * @param array $columns
         *
         * @return mixed
         */
        public function findArray($id, $columns = ['*'])
        {
            $repo = new $this($this->model->find($id, $columns));
            return $repo->model->toArray();
        }

        /**
         * Get
         *
         * @param array $columns
         */
        public function get($columns = ['*'])
        {
            $this->model = $this->model->get($columns);
        }

        /**
         * To Array
         *
         * @return array
         */
        public function toArray()
        {
            if (empty($this->model)) {
                return [];
            }
            else {
                return $this->model->toArray();
            }
        }

        /**
         * Select
         *
         * @param array $columns
         */
        public function select($columns)
        {
            $this->model = $this->model->select($columns);
        }

        /**
         * Select Raw
         *
         * @param string $string
         */
        public function selectRaw($string)
        {
            return $this->model->selectRaw($string);
        }

        /**
         * Set
         *
         * @param $key
         * @param $value
         */
        public function set($key, $value)
        {
            $this->model->{$key} = $value;
        }

        /**
         * Save
         *
         * @return bool
         */
        public function save()
        {
            return $this->model->save();
        }

        /**
         * Describe
         *
         * @return mixed
         */
        public function describe()
        {
            return $this->model->describe();
        }

        /**
         * Where
         *
         * @param string $column
         * @param null|string $operator
         * @param null|string $value
         *
         * @author MS
         * @throws InvalidArgumentException
         */
        public function where($column, $operator = null, $value = null)
        {
            $this->model = $this->model->where($column, $operator, $value);
        }

        /**
         * Where Date
         *
         * @param $column
         * @param $operator
         * @param $value
         */
        public function whereDate($column, $operator, $value)
        {
            if ($value instanceOf \DateTime) {
                $value = $value->format('Y-m-d');
            }
            $this->model = $this->model->whereDate($column, $operator, $value);
        }

        /**
         * Where Between
         *
         * @param $column
         * @param array $values
         * @param string $modifier
         * @param bool $not
         */
        public function whereBetween($column, array $values, $modifier = 'and', $not = false)
        {
            $this->model = $this->model->whereBetween($column, $values, $modifier, $not);
        }

        /**
         * Get Value
         *
         * @param $key
         * @return mixed
         */
        public function getValue($key)
        {
            return $this->model->{$key};
        }

        /**
         * Count
         *
         * @return int
         */
        public function count() {
            return $this->model->count();
        }

        /**
         * First
         *
         * @return array|null
         */
        public function first() {
            $this->model = $this->model->first();

            if ($this->model) {
                return $this->model->toArray();
            }

            return null;
        }

        /**
         * Skip
         *
         * @param $skip
         */
        public function skip($skip) {
            if ($skip) {
                $this->model->skip($skip);
            }
        }

        /**
         * Pluck
         *
         * @param $key
         * @return mixed
         */
        public function pluck($key)
        {
            return $this->model->pluck($key);
        }

        /**
         * Reset Orm Model
         *
         */
        public function resetOrmModel()
        {
            $this->model = new $this->ormModel();
        }

        /**
         * Insert
         *
         * @param $insert
         * @return bool
         */
        public function insert($insert)
        {
            return $this->model->insert($insert);
        }

        /**
         * Create model by array
         * @param array $model
         */
        public function create($model) {
            $this->model = $this->model->create($model);
        }

        /**
         * OrWhere
         * @param $column
         * @param $operator
         * @param $value
         */
        public function orWhere($column, $operator, $value) {
            $this->model->orWhere($column, $operator, $value);
        }

        /**
         * abstract table join method
         * @param  string       $table    table to join
         * @param  string       $one      column on table
         * @param  null|string  $operator operator
         * @param  null|string  $two      foreign key column
         * @param  string       $type     join type
         * @param  boolean      $where    join where clause
         */
        public function join(string $table, string $one, ?string $operator = null, ?string $two = null, string $type = 'inner', bool $where = false)
        {
            $this->model = $this->model
                ->join($table, $one, $operator, $two, $type, $where);
        }

        /**
         * order by $column $direction
         * @param  string $column
         * @param  string $direction
         */
        public function orderBy(string $column, string $direction = 'asc'): void
        {
            $this->model = $this->model->orderBy($column, $direction);
        }

        /**
         * Is Empty
         * @return bool
         */
        public function isEmpty(): bool
        {
            return $this->model->isEmpty();
        }

        /**
         * To Sql
         *
         * @return string
         */
        public function toSql()
        {
            return $this->model->getStatement();
        }

        /**
         * Get Id
         *
         * @return mixed
         */
        public function getId()
        {
            return $this->model->id;
        }

        /**
         * Where In
         *
         * @param $field
         * @param array $values
         */
        public function whereIn($field, $values)
        {
            $this->model = $this->model->whereIn($field, $values);
        }

        /**
         * Where Raw
         *
         * @param $queryString
         */
        public function whereRaw($queryString)
        {
            $this->model = $this->model->whereRaw($queryString);
        }

        /**
         * Orderby Raw
         *
         * @param $queryString
         *
         * @author MS
         */
        public function orderByRaw($queryString)
        {
            $this->model = $this->model->orderByRaw($queryString);
        }

        /**
         * Limit
         *
         * @param int $limitBy
         *
         * @author MS
         */
        public function limit($limitBy)
        {
            $this->model = $this->model->limit($limitBy);
        }

        /**
        * Offset
        *
        * @param $offset
        *
        * @author AC
        */
        public function offset($offset)
        {
            $this->model = $this->model->offset($offset);
        }

        /**
         * @info set protected property $guarded on Model to empty array before using
         *
         * @param $values
         */
        public function firstOrCreate($values)
        {
            $this->model = $this->model->firstOrCreate($values);
        }

        /**
         * Validate
         *
         * @param array $array
         * @return mixed
         */
        public function validate(array $array = [])
        {
            return $this->model->validate($array);
        }

        public function withTrashed()
        {
            $this->model = $this->model->withTrashed();
        }

        public function replicate()
        {
            $this->model = $this->model->replicate();
        }

        public function whereNull($column)
        {
            $this->model = $this->model->whereNull($column);
        }

        public function leftJoin($table, $one, $operator = null, $two = null)
        {
            $this->model = $this->model->leftJoin($table, $one, $operator, $two);
        }

        public function whereNotNull($column)
        {
            $this->model = $this->model->whereNotNull($column);
        }

        public function getModel()
        {
            return $this->model;
        }

        public function union(AbstractRepository $repo)
        {
            $model = $repo->getModel();
            $this->model = $this->model->union($model->getQuery());
        }

        public function getTableName()
        {
            return $this->model->getTableName();
        }

        /**
         * Add a "where not in" clause to the query.
         *
         * @param  string  $column
         * @param  mixed   $values
         * @param  string  $boolean
         */
        public function whereNotIn($column, $values, $boolean = 'and')
        {
            $this->model = $this->model->whereIn($column, $values, $boolean, true);
        }

        public function simplePaginate ($rows)
        {
            return $this->model->simplePaginate($rows);
        }

        public function pagination ($recAmount, $columns = ['*'], $page)
        {
            return $this->model->paginate($recAmount, $columns, '', $page)->toArray();
        }
    }

