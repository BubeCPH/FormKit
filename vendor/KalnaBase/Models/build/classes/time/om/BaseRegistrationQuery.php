<?php


/**
 * Base class that represents a query for the 'registrations' table.
 *
 *
 *
 * @method RegistrationQuery orderById($order = Criteria::ASC) Order by the id column
 * @method RegistrationQuery orderByUserId($order = Criteria::ASC) Order by the user_id column
 * @method RegistrationQuery orderByActivityId($order = Criteria::ASC) Order by the activity_id column
 * @method RegistrationQuery orderByState($order = Criteria::ASC) Order by the state column
 * @method RegistrationQuery orderByStartDate($order = Criteria::ASC) Order by the start_date column
 * @method RegistrationQuery orderByStartTime($order = Criteria::ASC) Order by the start_time column
 * @method RegistrationQuery orderByEndDate($order = Criteria::ASC) Order by the end_date column
 * @method RegistrationQuery orderByEndTime($order = Criteria::ASC) Order by the end_time column
 * @method RegistrationQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method RegistrationQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method RegistrationQuery orderByCreatedByUserId($order = Criteria::ASC) Order by the created_by_user_id column
 * @method RegistrationQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 * @method RegistrationQuery orderByUpdatedByUserId($order = Criteria::ASC) Order by the updated_by_user_id column
 *
 * @method RegistrationQuery groupById() Group by the id column
 * @method RegistrationQuery groupByUserId() Group by the user_id column
 * @method RegistrationQuery groupByActivityId() Group by the activity_id column
 * @method RegistrationQuery groupByState() Group by the state column
 * @method RegistrationQuery groupByStartDate() Group by the start_date column
 * @method RegistrationQuery groupByStartTime() Group by the start_time column
 * @method RegistrationQuery groupByEndDate() Group by the end_date column
 * @method RegistrationQuery groupByEndTime() Group by the end_time column
 * @method RegistrationQuery groupByDescription() Group by the description column
 * @method RegistrationQuery groupByCreatedAt() Group by the created_at column
 * @method RegistrationQuery groupByCreatedByUserId() Group by the created_by_user_id column
 * @method RegistrationQuery groupByUpdatedAt() Group by the updated_at column
 * @method RegistrationQuery groupByUpdatedByUserId() Group by the updated_by_user_id column
 *
 * @method RegistrationQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method RegistrationQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method RegistrationQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method RegistrationQuery leftJoinActivity($relationAlias = null) Adds a LEFT JOIN clause to the query using the Activity relation
 * @method RegistrationQuery rightJoinActivity($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Activity relation
 * @method RegistrationQuery innerJoinActivity($relationAlias = null) Adds a INNER JOIN clause to the query using the Activity relation
 *
 * @method Registration findOne(PropelPDO $con = null) Return the first Registration matching the query
 * @method Registration findOneOrCreate(PropelPDO $con = null) Return the first Registration matching the query, or a new Registration object populated from the query conditions when no match is found
 *
 * @method Registration findOneByUserId(int $user_id) Return the first Registration filtered by the user_id column
 * @method Registration findOneByActivityId(int $activity_id) Return the first Registration filtered by the activity_id column
 * @method Registration findOneByState(string $state) Return the first Registration filtered by the state column
 * @method Registration findOneByStartDate(string $start_date) Return the first Registration filtered by the start_date column
 * @method Registration findOneByStartTime(string $start_time) Return the first Registration filtered by the start_time column
 * @method Registration findOneByEndDate(string $end_date) Return the first Registration filtered by the end_date column
 * @method Registration findOneByEndTime(string $end_time) Return the first Registration filtered by the end_time column
 * @method Registration findOneByDescription(string $description) Return the first Registration filtered by the description column
 * @method Registration findOneByCreatedAt(string $created_at) Return the first Registration filtered by the created_at column
 * @method Registration findOneByCreatedByUserId(int $created_by_user_id) Return the first Registration filtered by the created_by_user_id column
 * @method Registration findOneByUpdatedAt(string $updated_at) Return the first Registration filtered by the updated_at column
 * @method Registration findOneByUpdatedByUserId(int $updated_by_user_id) Return the first Registration filtered by the updated_by_user_id column
 *
 * @method array findById(int $id) Return Registration objects filtered by the id column
 * @method array findByUserId(int $user_id) Return Registration objects filtered by the user_id column
 * @method array findByActivityId(int $activity_id) Return Registration objects filtered by the activity_id column
 * @method array findByState(string $state) Return Registration objects filtered by the state column
 * @method array findByStartDate(string $start_date) Return Registration objects filtered by the start_date column
 * @method array findByStartTime(string $start_time) Return Registration objects filtered by the start_time column
 * @method array findByEndDate(string $end_date) Return Registration objects filtered by the end_date column
 * @method array findByEndTime(string $end_time) Return Registration objects filtered by the end_time column
 * @method array findByDescription(string $description) Return Registration objects filtered by the description column
 * @method array findByCreatedAt(string $created_at) Return Registration objects filtered by the created_at column
 * @method array findByCreatedByUserId(int $created_by_user_id) Return Registration objects filtered by the created_by_user_id column
 * @method array findByUpdatedAt(string $updated_at) Return Registration objects filtered by the updated_at column
 * @method array findByUpdatedByUserId(int $updated_by_user_id) Return Registration objects filtered by the updated_by_user_id column
 *
 * @package    propel.generator.time.om
 */
abstract class BaseRegistrationQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseRegistrationQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'time';
        }
        if (null === $modelName) {
            $modelName = 'Registration';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new RegistrationQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   RegistrationQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return RegistrationQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof RegistrationQuery) {
            return $criteria;
        }
        $query = new RegistrationQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Registration|Registration[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = RegistrationPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(RegistrationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Registration A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Registration A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `user_id`, `activity_id`, `state`, `start_date`, `start_time`, `end_date`, `end_time`, `description`, `created_at`, `created_by_user_id`, `updated_at`, `updated_by_user_id` FROM `registrations` WHERE `id` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Registration();
            $obj->hydrate($row);
            RegistrationPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return Registration|Registration[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|Registration[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(RegistrationPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(RegistrationPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(RegistrationPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(RegistrationPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RegistrationPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUserId(1234); // WHERE user_id = 1234
     * $query->filterByUserId(array(12, 34)); // WHERE user_id IN (12, 34)
     * $query->filterByUserId(array('min' => 12)); // WHERE user_id >= 12
     * $query->filterByUserId(array('max' => 12)); // WHERE user_id <= 12
     * </code>
     *
     * @param     mixed $userId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByUserId($userId = null, $comparison = null)
    {
        if (is_array($userId)) {
            $useMinMax = false;
            if (isset($userId['min'])) {
                $this->addUsingAlias(RegistrationPeer::USER_ID, $userId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userId['max'])) {
                $this->addUsingAlias(RegistrationPeer::USER_ID, $userId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RegistrationPeer::USER_ID, $userId, $comparison);
    }

    /**
     * Filter the query on the activity_id column
     *
     * Example usage:
     * <code>
     * $query->filterByActivityId(1234); // WHERE activity_id = 1234
     * $query->filterByActivityId(array(12, 34)); // WHERE activity_id IN (12, 34)
     * $query->filterByActivityId(array('min' => 12)); // WHERE activity_id >= 12
     * $query->filterByActivityId(array('max' => 12)); // WHERE activity_id <= 12
     * </code>
     *
     * @see       filterByActivity()
     *
     * @param     mixed $activityId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByActivityId($activityId = null, $comparison = null)
    {
        if (is_array($activityId)) {
            $useMinMax = false;
            if (isset($activityId['min'])) {
                $this->addUsingAlias(RegistrationPeer::ACTIVITY_ID, $activityId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($activityId['max'])) {
                $this->addUsingAlias(RegistrationPeer::ACTIVITY_ID, $activityId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RegistrationPeer::ACTIVITY_ID, $activityId, $comparison);
    }

    /**
     * Filter the query on the state column
     *
     * Example usage:
     * <code>
     * $query->filterByState('fooValue');   // WHERE state = 'fooValue'
     * $query->filterByState('%fooValue%'); // WHERE state LIKE '%fooValue%'
     * </code>
     *
     * @param     string $state The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByState($state = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($state)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $state)) {
                $state = str_replace('*', '%', $state);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RegistrationPeer::STATE, $state, $comparison);
    }

    /**
     * Filter the query on the start_date column
     *
     * Example usage:
     * <code>
     * $query->filterByStartDate('2011-03-14'); // WHERE start_date = '2011-03-14'
     * $query->filterByStartDate('now'); // WHERE start_date = '2011-03-14'
     * $query->filterByStartDate(array('max' => 'yesterday')); // WHERE start_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $startDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByStartDate($startDate = null, $comparison = null)
    {
        if (is_array($startDate)) {
            $useMinMax = false;
            if (isset($startDate['min'])) {
                $this->addUsingAlias(RegistrationPeer::START_DATE, $startDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($startDate['max'])) {
                $this->addUsingAlias(RegistrationPeer::START_DATE, $startDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RegistrationPeer::START_DATE, $startDate, $comparison);
    }

    /**
     * Filter the query on the start_time column
     *
     * Example usage:
     * <code>
     * $query->filterByStartTime('2011-03-14'); // WHERE start_time = '2011-03-14'
     * $query->filterByStartTime('now'); // WHERE start_time = '2011-03-14'
     * $query->filterByStartTime(array('max' => 'yesterday')); // WHERE start_time < '2011-03-13'
     * </code>
     *
     * @param     mixed $startTime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByStartTime($startTime = null, $comparison = null)
    {
        if (is_array($startTime)) {
            $useMinMax = false;
            if (isset($startTime['min'])) {
                $this->addUsingAlias(RegistrationPeer::START_TIME, $startTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($startTime['max'])) {
                $this->addUsingAlias(RegistrationPeer::START_TIME, $startTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RegistrationPeer::START_TIME, $startTime, $comparison);
    }

    /**
     * Filter the query on the end_date column
     *
     * Example usage:
     * <code>
     * $query->filterByEndDate('2011-03-14'); // WHERE end_date = '2011-03-14'
     * $query->filterByEndDate('now'); // WHERE end_date = '2011-03-14'
     * $query->filterByEndDate(array('max' => 'yesterday')); // WHERE end_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $endDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByEndDate($endDate = null, $comparison = null)
    {
        if (is_array($endDate)) {
            $useMinMax = false;
            if (isset($endDate['min'])) {
                $this->addUsingAlias(RegistrationPeer::END_DATE, $endDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($endDate['max'])) {
                $this->addUsingAlias(RegistrationPeer::END_DATE, $endDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RegistrationPeer::END_DATE, $endDate, $comparison);
    }

    /**
     * Filter the query on the end_time column
     *
     * Example usage:
     * <code>
     * $query->filterByEndTime('2011-03-14'); // WHERE end_time = '2011-03-14'
     * $query->filterByEndTime('now'); // WHERE end_time = '2011-03-14'
     * $query->filterByEndTime(array('max' => 'yesterday')); // WHERE end_time < '2011-03-13'
     * </code>
     *
     * @param     mixed $endTime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByEndTime($endTime = null, $comparison = null)
    {
        if (is_array($endTime)) {
            $useMinMax = false;
            if (isset($endTime['min'])) {
                $this->addUsingAlias(RegistrationPeer::END_TIME, $endTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($endTime['max'])) {
                $this->addUsingAlias(RegistrationPeer::END_TIME, $endTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RegistrationPeer::END_TIME, $endTime, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $description)) {
                $description = str_replace('*', '%', $description);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RegistrationPeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(RegistrationPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(RegistrationPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RegistrationPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the created_by_user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedByUserId(1234); // WHERE created_by_user_id = 1234
     * $query->filterByCreatedByUserId(array(12, 34)); // WHERE created_by_user_id IN (12, 34)
     * $query->filterByCreatedByUserId(array('min' => 12)); // WHERE created_by_user_id >= 12
     * $query->filterByCreatedByUserId(array('max' => 12)); // WHERE created_by_user_id <= 12
     * </code>
     *
     * @param     mixed $createdByUserId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByCreatedByUserId($createdByUserId = null, $comparison = null)
    {
        if (is_array($createdByUserId)) {
            $useMinMax = false;
            if (isset($createdByUserId['min'])) {
                $this->addUsingAlias(RegistrationPeer::CREATED_BY_USER_ID, $createdByUserId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdByUserId['max'])) {
                $this->addUsingAlias(RegistrationPeer::CREATED_BY_USER_ID, $createdByUserId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RegistrationPeer::CREATED_BY_USER_ID, $createdByUserId, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(RegistrationPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(RegistrationPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RegistrationPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query on the updated_by_user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedByUserId(1234); // WHERE updated_by_user_id = 1234
     * $query->filterByUpdatedByUserId(array(12, 34)); // WHERE updated_by_user_id IN (12, 34)
     * $query->filterByUpdatedByUserId(array('min' => 12)); // WHERE updated_by_user_id >= 12
     * $query->filterByUpdatedByUserId(array('max' => 12)); // WHERE updated_by_user_id <= 12
     * </code>
     *
     * @param     mixed $updatedByUserId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function filterByUpdatedByUserId($updatedByUserId = null, $comparison = null)
    {
        if (is_array($updatedByUserId)) {
            $useMinMax = false;
            if (isset($updatedByUserId['min'])) {
                $this->addUsingAlias(RegistrationPeer::UPDATED_BY_USER_ID, $updatedByUserId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedByUserId['max'])) {
                $this->addUsingAlias(RegistrationPeer::UPDATED_BY_USER_ID, $updatedByUserId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RegistrationPeer::UPDATED_BY_USER_ID, $updatedByUserId, $comparison);
    }

    /**
     * Filter the query by a related Activity object
     *
     * @param   Activity|PropelObjectCollection $activity The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 RegistrationQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByActivity($activity, $comparison = null)
    {
        if ($activity instanceof Activity) {
            return $this
                ->addUsingAlias(RegistrationPeer::ACTIVITY_ID, $activity->getId(), $comparison);
        } elseif ($activity instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(RegistrationPeer::ACTIVITY_ID, $activity->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByActivity() only accepts arguments of type Activity or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Activity relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function joinActivity($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Activity');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Activity');
        }

        return $this;
    }

    /**
     * Use the Activity relation Activity object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   ActivityQuery A secondary query class using the current class as primary query
     */
    public function useActivityQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinActivity($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Activity', 'ActivityQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Registration $registration Object to remove from the list of results
     *
     * @return RegistrationQuery The current query, for fluid interface
     */
    public function prune($registration = null)
    {
        if ($registration) {
            $this->addUsingAlias(RegistrationPeer::ID, $registration->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
