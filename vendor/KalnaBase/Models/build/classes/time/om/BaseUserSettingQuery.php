<?php


/**
 * Base class that represents a query for the 'user_settings' table.
 *
 *
 *
 * @method UserSettingQuery orderById($order = Criteria::ASC) Order by the id column
 * @method UserSettingQuery orderByUid($order = Criteria::ASC) Order by the uid column
 * @method UserSettingQuery orderByPrmId($order = Criteria::ASC) Order by the prm_id column
 * @method UserSettingQuery orderByValue($order = Criteria::ASC) Order by the value column
 *
 * @method UserSettingQuery groupById() Group by the id column
 * @method UserSettingQuery groupByUid() Group by the uid column
 * @method UserSettingQuery groupByPrmId() Group by the prm_id column
 * @method UserSettingQuery groupByValue() Group by the value column
 *
 * @method UserSettingQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method UserSettingQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method UserSettingQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method UserSetting findOne(PropelPDO $con = null) Return the first UserSetting matching the query
 * @method UserSetting findOneOrCreate(PropelPDO $con = null) Return the first UserSetting matching the query, or a new UserSetting object populated from the query conditions when no match is found
 *
 * @method UserSetting findOneByUid(int $uid) Return the first UserSetting filtered by the uid column
 * @method UserSetting findOneByPrmId(int $prm_id) Return the first UserSetting filtered by the prm_id column
 * @method UserSetting findOneByValue(string $value) Return the first UserSetting filtered by the value column
 *
 * @method array findById(int $id) Return UserSetting objects filtered by the id column
 * @method array findByUid(int $uid) Return UserSetting objects filtered by the uid column
 * @method array findByPrmId(int $prm_id) Return UserSetting objects filtered by the prm_id column
 * @method array findByValue(string $value) Return UserSetting objects filtered by the value column
 *
 * @package    propel.generator.time.om
 */
abstract class BaseUserSettingQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseUserSettingQuery object.
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
            $modelName = 'UserSetting';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new UserSettingQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   UserSettingQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return UserSettingQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof UserSettingQuery) {
            return $criteria;
        }
        $query = new UserSettingQuery(null, null, $modelAlias);

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
     * @return   UserSetting|UserSetting[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = UserSettingPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(UserSettingPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 UserSetting A model object, or null if the key is not found
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
     * @return                 UserSetting A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `uid`, `prm_id`, `value` FROM `user_settings` WHERE `id` = :p0';
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
            $obj = new UserSetting();
            $obj->hydrate($row);
            UserSettingPeer::addInstanceToPool($obj, (string) $key);
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
     * @return UserSetting|UserSetting[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|UserSetting[]|mixed the list of results, formatted by the current formatter
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
     * @return UserSettingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(UserSettingPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return UserSettingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(UserSettingPeer::ID, $keys, Criteria::IN);
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
     * @return UserSettingQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(UserSettingPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(UserSettingPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserSettingPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the uid column
     *
     * Example usage:
     * <code>
     * $query->filterByUid(1234); // WHERE uid = 1234
     * $query->filterByUid(array(12, 34)); // WHERE uid IN (12, 34)
     * $query->filterByUid(array('min' => 12)); // WHERE uid >= 12
     * $query->filterByUid(array('max' => 12)); // WHERE uid <= 12
     * </code>
     *
     * @param     mixed $uid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserSettingQuery The current query, for fluid interface
     */
    public function filterByUid($uid = null, $comparison = null)
    {
        if (is_array($uid)) {
            $useMinMax = false;
            if (isset($uid['min'])) {
                $this->addUsingAlias(UserSettingPeer::UID, $uid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($uid['max'])) {
                $this->addUsingAlias(UserSettingPeer::UID, $uid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserSettingPeer::UID, $uid, $comparison);
    }

    /**
     * Filter the query on the prm_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPrmId(1234); // WHERE prm_id = 1234
     * $query->filterByPrmId(array(12, 34)); // WHERE prm_id IN (12, 34)
     * $query->filterByPrmId(array('min' => 12)); // WHERE prm_id >= 12
     * $query->filterByPrmId(array('max' => 12)); // WHERE prm_id <= 12
     * </code>
     *
     * @param     mixed $prmId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserSettingQuery The current query, for fluid interface
     */
    public function filterByPrmId($prmId = null, $comparison = null)
    {
        if (is_array($prmId)) {
            $useMinMax = false;
            if (isset($prmId['min'])) {
                $this->addUsingAlias(UserSettingPeer::PRM_ID, $prmId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($prmId['max'])) {
                $this->addUsingAlias(UserSettingPeer::PRM_ID, $prmId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserSettingPeer::PRM_ID, $prmId, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue('fooValue');   // WHERE value = 'fooValue'
     * $query->filterByValue('%fooValue%'); // WHERE value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $value The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserSettingQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($value)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $value)) {
                $value = str_replace('*', '%', $value);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserSettingPeer::VALUE, $value, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   UserSetting $userSetting Object to remove from the list of results
     *
     * @return UserSettingQuery The current query, for fluid interface
     */
    public function prune($userSetting = null)
    {
        if ($userSetting) {
            $this->addUsingAlias(UserSettingPeer::ID, $userSetting->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
