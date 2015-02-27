<?php


/**
 * Base class that represents a query for the 'domain_values' table.
 *
 *
 *
 * @method DomainValueQuery orderByDomain($order = Criteria::ASC) Order by the domain column
 * @method DomainValueQuery orderByLowValue($order = Criteria::ASC) Order by the low_value column
 * @method DomainValueQuery orderByHighValue($order = Criteria::ASC) Order by the high_value column
 * @method DomainValueQuery orderByAbbreviation($order = Criteria::ASC) Order by the abbreviation column
 * @method DomainValueQuery orderByMeaning($order = Criteria::ASC) Order by the meaning column
 * @method DomainValueQuery orderByLang($order = Criteria::ASC) Order by the lang column
 *
 * @method DomainValueQuery groupByDomain() Group by the domain column
 * @method DomainValueQuery groupByLowValue() Group by the low_value column
 * @method DomainValueQuery groupByHighValue() Group by the high_value column
 * @method DomainValueQuery groupByAbbreviation() Group by the abbreviation column
 * @method DomainValueQuery groupByMeaning() Group by the meaning column
 * @method DomainValueQuery groupByLang() Group by the lang column
 *
 * @method DomainValueQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method DomainValueQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method DomainValueQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method DomainValue findOne(PropelPDO $con = null) Return the first DomainValue matching the query
 * @method DomainValue findOneOrCreate(PropelPDO $con = null) Return the first DomainValue matching the query, or a new DomainValue object populated from the query conditions when no match is found
 *
 * @method DomainValue findOneByDomain(string $domain) Return the first DomainValue filtered by the domain column
 * @method DomainValue findOneByLowValue(string $low_value) Return the first DomainValue filtered by the low_value column
 * @method DomainValue findOneByHighValue(string $high_value) Return the first DomainValue filtered by the high_value column
 * @method DomainValue findOneByAbbreviation(string $abbreviation) Return the first DomainValue filtered by the abbreviation column
 * @method DomainValue findOneByMeaning(string $meaning) Return the first DomainValue filtered by the meaning column
 * @method DomainValue findOneByLang(string $lang) Return the first DomainValue filtered by the lang column
 *
 * @method array findByDomain(string $domain) Return DomainValue objects filtered by the domain column
 * @method array findByLowValue(string $low_value) Return DomainValue objects filtered by the low_value column
 * @method array findByHighValue(string $high_value) Return DomainValue objects filtered by the high_value column
 * @method array findByAbbreviation(string $abbreviation) Return DomainValue objects filtered by the abbreviation column
 * @method array findByMeaning(string $meaning) Return DomainValue objects filtered by the meaning column
 * @method array findByLang(string $lang) Return DomainValue objects filtered by the lang column
 *
 * @package    propel.generator.time.om
 */
abstract class BaseDomainValueQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseDomainValueQuery object.
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
            $modelName = 'DomainValue';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new DomainValueQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   DomainValueQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return DomainValueQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof DomainValueQuery) {
            return $criteria;
        }
        $query = new DomainValueQuery(null, null, $modelAlias);

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
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array $key Primary key to use for the query
                         A Primary key composition: [$domain, $low_value]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   DomainValue|DomainValue[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DomainValuePeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(DomainValuePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 DomainValue A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `domain`, `low_value`, `high_value`, `abbreviation`, `meaning`, `lang` FROM `domain_values` WHERE `domain` = :p0 AND `low_value` = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_STR);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new DomainValue();
            $obj->hydrate($row);
            DomainValuePeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return DomainValue|DomainValue[]|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|DomainValue[]|mixed the list of results, formatted by the current formatter
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
     * @return DomainValueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(DomainValuePeer::DOMAIN, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(DomainValuePeer::LOW_VALUE, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return DomainValueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(DomainValuePeer::DOMAIN, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(DomainValuePeer::LOW_VALUE, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the domain column
     *
     * Example usage:
     * <code>
     * $query->filterByDomain('fooValue');   // WHERE domain = 'fooValue'
     * $query->filterByDomain('%fooValue%'); // WHERE domain LIKE '%fooValue%'
     * </code>
     *
     * @param     string $domain The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DomainValueQuery The current query, for fluid interface
     */
    public function filterByDomain($domain = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($domain)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $domain)) {
                $domain = str_replace('*', '%', $domain);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DomainValuePeer::DOMAIN, $domain, $comparison);
    }

    /**
     * Filter the query on the low_value column
     *
     * Example usage:
     * <code>
     * $query->filterByLowValue('fooValue');   // WHERE low_value = 'fooValue'
     * $query->filterByLowValue('%fooValue%'); // WHERE low_value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $lowValue The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DomainValueQuery The current query, for fluid interface
     */
    public function filterByLowValue($lowValue = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($lowValue)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $lowValue)) {
                $lowValue = str_replace('*', '%', $lowValue);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DomainValuePeer::LOW_VALUE, $lowValue, $comparison);
    }

    /**
     * Filter the query on the high_value column
     *
     * Example usage:
     * <code>
     * $query->filterByHighValue('fooValue');   // WHERE high_value = 'fooValue'
     * $query->filterByHighValue('%fooValue%'); // WHERE high_value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $highValue The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DomainValueQuery The current query, for fluid interface
     */
    public function filterByHighValue($highValue = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($highValue)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $highValue)) {
                $highValue = str_replace('*', '%', $highValue);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DomainValuePeer::HIGH_VALUE, $highValue, $comparison);
    }

    /**
     * Filter the query on the abbreviation column
     *
     * Example usage:
     * <code>
     * $query->filterByAbbreviation('fooValue');   // WHERE abbreviation = 'fooValue'
     * $query->filterByAbbreviation('%fooValue%'); // WHERE abbreviation LIKE '%fooValue%'
     * </code>
     *
     * @param     string $abbreviation The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DomainValueQuery The current query, for fluid interface
     */
    public function filterByAbbreviation($abbreviation = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($abbreviation)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $abbreviation)) {
                $abbreviation = str_replace('*', '%', $abbreviation);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DomainValuePeer::ABBREVIATION, $abbreviation, $comparison);
    }

    /**
     * Filter the query on the meaning column
     *
     * Example usage:
     * <code>
     * $query->filterByMeaning('fooValue');   // WHERE meaning = 'fooValue'
     * $query->filterByMeaning('%fooValue%'); // WHERE meaning LIKE '%fooValue%'
     * </code>
     *
     * @param     string $meaning The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DomainValueQuery The current query, for fluid interface
     */
    public function filterByMeaning($meaning = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($meaning)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $meaning)) {
                $meaning = str_replace('*', '%', $meaning);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DomainValuePeer::MEANING, $meaning, $comparison);
    }

    /**
     * Filter the query on the lang column
     *
     * Example usage:
     * <code>
     * $query->filterByLang('fooValue');   // WHERE lang = 'fooValue'
     * $query->filterByLang('%fooValue%'); // WHERE lang LIKE '%fooValue%'
     * </code>
     *
     * @param     string $lang The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DomainValueQuery The current query, for fluid interface
     */
    public function filterByLang($lang = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($lang)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $lang)) {
                $lang = str_replace('*', '%', $lang);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DomainValuePeer::LANG, $lang, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   DomainValue $domainValue Object to remove from the list of results
     *
     * @return DomainValueQuery The current query, for fluid interface
     */
    public function prune($domainValue = null)
    {
        if ($domainValue) {
            $this->addCond('pruneCond0', $this->getAliasedColName(DomainValuePeer::DOMAIN), $domainValue->getDomain(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(DomainValuePeer::LOW_VALUE), $domainValue->getLowValue(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}
