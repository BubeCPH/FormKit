<?php


/**
 * Base class that represents a query for the 'settings' table.
 *
 *
 *
 * @method SettingQuery orderById($order = Criteria::ASC) Order by the id column
 * @method SettingQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method SettingQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method SettingQuery orderByLongDescription($order = Criteria::ASC) Order by the long_description column
 * @method SettingQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method SettingQuery orderByDefaultValue($order = Criteria::ASC) Order by the default_value column
 * @method SettingQuery orderByFormula($order = Criteria::ASC) Order by the formula column
 *
 * @method SettingQuery groupById() Group by the id column
 * @method SettingQuery groupByName() Group by the name column
 * @method SettingQuery groupByDescription() Group by the description column
 * @method SettingQuery groupByLongDescription() Group by the long_description column
 * @method SettingQuery groupByType() Group by the type column
 * @method SettingQuery groupByDefaultValue() Group by the default_value column
 * @method SettingQuery groupByFormula() Group by the formula column
 *
 * @method SettingQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method SettingQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method SettingQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Setting findOne(PropelPDO $con = null) Return the first Setting matching the query
 * @method Setting findOneOrCreate(PropelPDO $con = null) Return the first Setting matching the query, or a new Setting object populated from the query conditions when no match is found
 *
 * @method Setting findOneByName(string $name) Return the first Setting filtered by the name column
 * @method Setting findOneByDescription(string $description) Return the first Setting filtered by the description column
 * @method Setting findOneByLongDescription(string $long_description) Return the first Setting filtered by the long_description column
 * @method Setting findOneByType(string $type) Return the first Setting filtered by the type column
 * @method Setting findOneByDefaultValue(string $default_value) Return the first Setting filtered by the default_value column
 * @method Setting findOneByFormula(string $formula) Return the first Setting filtered by the formula column
 *
 * @method array findById(int $id) Return Setting objects filtered by the id column
 * @method array findByName(string $name) Return Setting objects filtered by the name column
 * @method array findByDescription(string $description) Return Setting objects filtered by the description column
 * @method array findByLongDescription(string $long_description) Return Setting objects filtered by the long_description column
 * @method array findByType(string $type) Return Setting objects filtered by the type column
 * @method array findByDefaultValue(string $default_value) Return Setting objects filtered by the default_value column
 * @method array findByFormula(string $formula) Return Setting objects filtered by the formula column
 *
 * @package    propel.generator.time.om
 */
abstract class BaseSettingQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseSettingQuery object.
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
            $modelName = 'Setting';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SettingQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   SettingQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SettingQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SettingQuery) {
            return $criteria;
        }
        $query = new SettingQuery(null, null, $modelAlias);

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
     * @return   Setting|Setting[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SettingPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SettingPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Setting A model object, or null if the key is not found
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
     * @return                 Setting A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `name`, `description`, `long_description`, `type`, `default_value`, `formula` FROM `settings` WHERE `id` = :p0';
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
            $obj = new Setting();
            $obj->hydrate($row);
            SettingPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Setting|Setting[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Setting[]|mixed the list of results, formatted by the current formatter
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
     * @return SettingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SettingPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SettingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SettingPeer::ID, $keys, Criteria::IN);
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
     * @return SettingQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SettingPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SettingPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SettingQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingPeer::NAME, $name, $comparison);
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
     * @return SettingQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SettingPeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the long_description column
     *
     * Example usage:
     * <code>
     * $query->filterByLongDescription('fooValue');   // WHERE long_description = 'fooValue'
     * $query->filterByLongDescription('%fooValue%'); // WHERE long_description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $longDescription The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SettingQuery The current query, for fluid interface
     */
    public function filterByLongDescription($longDescription = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($longDescription)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $longDescription)) {
                $longDescription = str_replace('*', '%', $longDescription);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingPeer::LONG_DESCRIPTION, $longDescription, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SettingQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the default_value column
     *
     * Example usage:
     * <code>
     * $query->filterByDefaultValue('fooValue');   // WHERE default_value = 'fooValue'
     * $query->filterByDefaultValue('%fooValue%'); // WHERE default_value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $defaultValue The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SettingQuery The current query, for fluid interface
     */
    public function filterByDefaultValue($defaultValue = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($defaultValue)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $defaultValue)) {
                $defaultValue = str_replace('*', '%', $defaultValue);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingPeer::DEFAULT_VALUE, $defaultValue, $comparison);
    }

    /**
     * Filter the query on the formula column
     *
     * Example usage:
     * <code>
     * $query->filterByFormula('fooValue');   // WHERE formula = 'fooValue'
     * $query->filterByFormula('%fooValue%'); // WHERE formula LIKE '%fooValue%'
     * </code>
     *
     * @param     string $formula The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SettingQuery The current query, for fluid interface
     */
    public function filterByFormula($formula = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($formula)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $formula)) {
                $formula = str_replace('*', '%', $formula);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingPeer::FORMULA, $formula, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Setting $setting Object to remove from the list of results
     *
     * @return SettingQuery The current query, for fluid interface
     */
    public function prune($setting = null)
    {
        if ($setting) {
            $this->addUsingAlias(SettingPeer::ID, $setting->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
