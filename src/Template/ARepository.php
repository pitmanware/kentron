<?php

    namespace Kentron\Template;

    use Kentron\Template\Model as AppModel;

    use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Model as EloquentModel;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Database\Query\Builder as QueryBuilder;
    use Illuminate\Validation\Validator;

    use \DateTime;

    /**
     * would contain all "used" model methods
     * Models should NEVER be returned by any functions in the repository
     * Repositories work on persistence, a model can be updated via the repository
     * in a number of requests, there is no need for a single function in the
     * repository to do ALL of the work you expect it to do and its information can
     * be returned at any time using the get(), first() and toArray() functions.
     *
     * @info
     */
    abstract class ARepository
    {
        /**
         * Not best practice
         * @todo seperate into different properties. eg: model, builder, collection.
         * @todo As it stands, model property takes the form of over 7 different
         * @todo objects over the course of getting data from eloquent and the call
         * @todo order of these methods is too "Eloquent Informed" to be worth the
         * @todo abstraction
         *
         * @var AppModel | EloquentModel | EloquentBuilder | QueryBuilder | Collection | Validator | SoftDeletes */
        protected $model;

        protected $ormModel = AppModel::class;
        protected $entity = AbstractEntity::class;

        /** @var EloquentBuilder */
        protected $queryBuilder;

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

        public const STOP = true;

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
         * Query
         *
         * @param null $query
         */
        public function query($query = null)
        {
            if ($query instanceof QueryBuilder) {
                $this->model = $query;
            }
            else {
                $this->model = $this->model->query();
            }
        }

        /**
         * All
         *
         * @param array $columns
         *
         * @return mixed
         */
        public function all($columns = ['*'])
        {
            /** @var AbstractRepository $instance */
            $instance = new static;
            return $instance->model->newQuery()->get($columns);
        }

        public function getFirstArray($model)
        {
            return $this->model->getFirstArray($model);
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
         * Force Delete
         * forceDelete() has multiple Eloquent declarations
         * @todo If it actually matters, wrap inside conditional, maybe instanceof
         * @todo If you know that it doesn't matter, delete these comments :p
         *
         * @author ?
         * @return mixed
         */
        public function forceDelete()
        {
            return $this->model->forceDelete();
        }

        /**
         * Update
         * Translated from French
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
         * Set Query Builder
         * Contained errors in code, rewritten to do what I assume it was intended
         * for (currently at time of writing, no usages for method anyway)
         *
         * @param $id
         * @param array $columns
         *
         * @author MS
         * @return AbstractRepository
         */
        public function setQueryBuilder($id, $columns = ['*'])
        {
            $this->find($id, $columns);
            $this->queryBuilder = $this->model->select($columns)->where('id', $id);
            return $this;
        }

        /**
         * Execute Query Builder
         *
         */
        public function executeQueryBuilder()
        {
            $this->model = $this->queryBuilder->get();
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
         * Key By
         *
         * @param callable|string $key
         *
         * @return Collection
         */
        public function keyBy($key)
        {
            return $this->model->keyBy($key);
        }

        /**
         * Get Array By Key
         *
         * @param callable|string $key
         *
         * @return array
         */
        public function getArrayByKey($key)
        {
            return $this->model->get()->keyBy($key)->toArray();
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
         * To Entity
         *
         *
         * @return mixed
         */
        public function toEntity()
        {
            return new $this->entity($this->toArray());
        }

        /**
         * To Collective Entity
         *
         * @return array
         */
        public function toEntityCollection()
        {
            $return = array();
            foreach ($this->toArray() as $array) {
                $return[] = new $this->entity($array);
            }
            return $return;
        }



        /**
         * Update With Entity
         *
         * @param $entity
         * @param $pk
         *
         */
        public function updateWithEntity($entity, $pk = null)
        {
            $this->resetOrmModel();

            if ($entity instanceof AbstractEntity) {
                $array = $entity->get();

                if (property_exists($entity, 'id')) {
                    $this->where('id', '=', $entity->id);
                }
                else {
                    $this->where($pk, '=', $entity->{$pk});
                }

                $this->update($array);
            }
        }


        /**
         * Create With Entity
         *
         * @param $entity
         *
         * @return bool
         */
        public function createWithEntity($entity)
        {
            $this->resetOrmModel();

            if ($entity instanceof AbstractEntity) {
                foreach ($entity->get() as $key => $value) {
                    $this->set($key, $value);
                }

                return $this->model->save();
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
         *
         * @return Collection
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
         *
         * Mass insert with the models timestamps
         *
         */
        public function insertWithTimestamps($insertData)
        {
            foreach ($insertData as $key => &$rowData) {
                $rowData[$this->model::CREATED_AT] = new \DateTime();

                if ($this->model::UPDATED_AT) {
                    $rowData[$this->model::UPDATED_AT] = new \DateTime();
                }
            }

            return $this->model->insert($insertData);
        }

        /**
         * eloquent eager load (table joins)
         * @param  string $table
         */
        public function with($table)
        {
            $this->model->with($table);
        }

        /**
         * create "where" from array key value of column = value
         * @param  array  $filters
         */
        public function findWhere(array $filters = [])
        {
            // get list of feilds
            foreach ($filters as $index => $value) {

                if ($value instanceof DateTime || $value instanceof DateTimeUk || $value instanceof DateTimeDB)
                {
                    $value = $value->format('Y-m-d');
                    $this->model = $this->model->whereDate($index, '=', $value);
                }
                else {
                    $this->model = $this->model->where($index, $value);
                }
            }
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
        public function join($table, $one, $operator = null, $two = null, $type = 'inner', $where = false)
        {
            $this->model = $this->model
                ->join($table, $one, $operator, $two, $type, $where);
        }

        /**
         * order by $column $direction
         * @param  string $column
         * @param  string $direction
         */
        public function orderBy($column, $direction = 'asc')
        {
            $this->model = $this->model->orderBy($column, $direction);
        }

        /**
         * group by sql
         * @param  mixed $groupBy
         */
        public function groupBy($groupBy)
        {
            $this->model = $this->model->groupBy($groupBy);
        }

        /**
         * @todo rewrite this, don't like this level of exposure on capsule
         *
         * @param  [type] $string [description]
         * @return [type]         [description]
         */
        public function raw($string)
        {
            global $capsule;
            return $capsule::raw($string);

            //Encapsulate the response in the $model property of the repo
            //Raw should never really be used in a service context anyway,
            // so this could be private
            //$this->model = $capsule::raw($string);
        }

        /**
         * paginate with PaginationProxy
         * @param  int $itemsPerPage
         * @param  int $pageNum
         * @return array
         */
        public function paginate($itemsPerPage = 10, $pageNum = 1)
        {
            $pagination = new PaginationProxy($this->model, $itemsPerPage, $pageNum);
            return [
                'results' => $pagination->getItems(),
                'pag_links' => $pagination->getLinks()
            ];
        }
        /**
         * paginate with PaginationProxy Ready for Ajax Requests.
         * @param  int $itemsPerPage
         * @param  int $pageNum
         * @param  string $ajaxClass
         * @return array
         *
         */
        public function ajaxPaginate($itemsPerPage = 10, $pageNum = 1, $ajaxClass)
        {
            $pagination = new PaginationProxy($this->model, $itemsPerPage, $pageNum);
            return [
                'results' => $pagination->getItems(),
                'pag_links' => $pagination->getAjaxLinks($ajaxClass)
            ];
        }

        /**
         * update a single table field
         *
         * @param  null|int $id
         * @param  null|string $field
         * @param  null|mixed $value
         *
         * @return bool
         * @throws Exception | InvalidArgumentException
         */
        public function updateField($id = null, $field = null, $value = null)
        {
            if (is_null($id) || is_null($field)) {
                throw new Exception("updateField args cannot be null.");
            }

            // if value a date, convert UK date object
            $date = \DateTime::createFromFormat('d/m/Y', $value);
            if ($date == true) {
                $value = $date->format('Y-m-d');
            }

            $this->resetOrmModel();
            $row = $this->model->find($id);

            if (is_null($row)) {
                throw new Exception("Record not found.");
            }

            $isValid = $this->model->validate([$field => $value]);

            if (!$isValid) {
                $message = $this->model->errors()->first();
                throw new InvalidArgumentException("Validation error. $message");
            }

            $row->{$field} = $value;

            return $row->update();
        }

        /**
         * Is Empty
         *
         * @return bool
         */
        public function isEmpty()
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
            return $this->model->toSql();
        }
        /**
         * Determine if the given operator and value combination is legal.
         *
         * @param  string  $operator
         * @param  mixed  $value
         * @return bool
         */
        private function invalidOperatorAndValue($operator, $value)
        {
            $isOperator = in_array($operator, $this->operators);

            return $isOperator && $operator != '=' && is_null($value);
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

        /**
         * Get Last Error
         *
         * @return mixed
         */
        public function getLastError()
        {
            return $this->model->errors()->first();
        }

        /**
         * Get Errors
         *
         * @return mixed
         */
        public function getErrors()
        {
            return $this->model->errors()->all();
        }

        /**
         * Manual Key By
         *
         * @param array $array
         * @param string $key
         * @return array
         */
        public function manualKeyBy(array $array, $key = "id")
        {
            $data = [];
            foreach ($array as $item) {
                $data[$item[$key]] = $item;
            }
            return $data;
        }

        public function withTrashed()
        {
            $this->model = $this->model->withTrashed();
        }


        /**
         * never modify this function!
         *
         * @param  string $name class name
         * @param  mixed $key  storage key id
         * @return object
         */
        protected final function dataFactory($name = '', $key = null)
        {
            return $this->factory('Data\\' . $name, $key);
        }

        /**
         * never modify this function!
         *
         * @param  string $name class name
         * @param  mixed $key  storage key id
         * @return object
         */
        protected final function entityFactory($name = '', $key = null)
        {
            return $this->factory('Entity\\' . $name, $key);
        }

        /**
         * never modify this function!
         *
         * @param  string $class class name
         * @param  mixed $key  storage key id
         * @return object
         */
        protected function factory($class, $key)
        {
            $factory = new DataFactory();
            $namespace = $factory->getNamespace();

            // instance manager
            $registryManager = Registry::getInstance();
            $instance = $registryManager->find($namespace . $class, $key);

            if (is_null($instance)) {
                $instance = $factory->make($class);
            }
            return $registryManager->add($namespace . $class, $key, $instance);
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

        public function insertGetId ($insertArray)
        {
            return $this->model->insertGetId($insertArray);
        }

        public function setFilters ($filter)
        {
            foreach ($filter as $key => $value) {
                if($value) {
                    $this->where($key, '=', $value);
                }
            }
        }

        /**
     	 * Iterate over results one query at a time
     	 *
     	 * @return tuple
    	 */
        public function iterator ()
        {
            foreach ($this->model->cursor() as $key => $value) {
                if ((yield $key => $value->toArray()) === self::STOP) {
                    break;
                }
            }
        }
    }
