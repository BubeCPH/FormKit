<?php


/**
 * Base class that represents a row from the 'activities' table.
 *
 *
 *
 * @package    propel.generator.time.om
 */
abstract class BaseActivity extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'ActivityPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ActivityPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $id;

    /**
     * The value for the parent_id field.
     * @var        int
     */
    protected $parent_id;

    /**
     * The value for the user_id field.
     * @var        int
     */
    protected $user_id;

    /**
     * The value for the code field.
     * @var        string
     */
    protected $code;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * The value for the favorites_yn field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $favorites_yn;

    /**
     * The value for the sort_order field.
     * @var        int
     */
    protected $sort_order;

    /**
     * @var        PropelObjectCollection|Registration[] Collection to store aggregation of Registration objects.
     */
    protected $collRegistrations;
    protected $collRegistrationsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $registrationsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->id = 0;
        $this->favorites_yn = false;
    }

    /**
     * Initializes internal state of BaseActivity object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [parent_id] column value.
     *
     * @return int
     */
    public function getParentId()
    {

        return $this->parent_id;
    }

    /**
     * Get the [user_id] column value.
     *
     * @return int
     */
    public function getUserId()
    {

        return $this->user_id;
    }

    /**
     * Get the [code] column value.
     *
     * @return string
     */
    public function getCode()
    {

        return $this->code;
    }

    /**
     * Get the [description] column value.
     *
     * @return string
     */
    public function getDescription()
    {

        return $this->description;
    }

    /**
     * Get the [favorites_yn] column value.
     *
     * @return boolean
     */
    public function getFavoritesYn()
    {

        return $this->favorites_yn;
    }

    /**
     * Get the [sort_order] column value.
     *
     * @return int
     */
    public function getSortOrder()
    {

        return $this->sort_order;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Activity The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = ActivityPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [parent_id] column.
     *
     * @param  int $v new value
     * @return Activity The current object (for fluent API support)
     */
    public function setParentId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->parent_id !== $v) {
            $this->parent_id = $v;
            $this->modifiedColumns[] = ActivityPeer::PARENT_ID;
        }


        return $this;
    } // setParentId()

    /**
     * Set the value of [user_id] column.
     *
     * @param  int $v new value
     * @return Activity The current object (for fluent API support)
     */
    public function setUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->user_id !== $v) {
            $this->user_id = $v;
            $this->modifiedColumns[] = ActivityPeer::USER_ID;
        }


        return $this;
    } // setUserId()

    /**
     * Set the value of [code] column.
     *
     * @param  string $v new value
     * @return Activity The current object (for fluent API support)
     */
    public function setCode($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->code !== $v) {
            $this->code = $v;
            $this->modifiedColumns[] = ActivityPeer::CODE;
        }


        return $this;
    } // setCode()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return Activity The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = ActivityPeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

    /**
     * Sets the value of the [favorites_yn] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Activity The current object (for fluent API support)
     */
    public function setFavoritesYn($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->favorites_yn !== $v) {
            $this->favorites_yn = $v;
            $this->modifiedColumns[] = ActivityPeer::FAVORITES_YN;
        }


        return $this;
    } // setFavoritesYn()

    /**
     * Set the value of [sort_order] column.
     *
     * @param  int $v new value
     * @return Activity The current object (for fluent API support)
     */
    public function setSortOrder($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sort_order !== $v) {
            $this->sort_order = $v;
            $this->modifiedColumns[] = ActivityPeer::SORT_ORDER;
        }


        return $this;
    } // setSortOrder()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->id !== 0) {
                return false;
            }

            if ($this->favorites_yn !== false) {
                return false;
            }

        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->parent_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->user_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->code = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->description = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->favorites_yn = ($row[$startcol + 5] !== null) ? (boolean) $row[$startcol + 5] : null;
            $this->sort_order = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 7; // 7 = ActivityPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Activity object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(ActivityPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = ActivityPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collRegistrations = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(ActivityPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ActivityQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(ActivityPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                ActivityPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->registrationsScheduledForDeletion !== null) {
                if (!$this->registrationsScheduledForDeletion->isEmpty()) {
                    RegistrationQuery::create()
                        ->filterByPrimaryKeys($this->registrationsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->registrationsScheduledForDeletion = null;
                }
            }

            if ($this->collRegistrations !== null) {
                foreach ($this->collRegistrations as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ActivityPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(ActivityPeer::PARENT_ID)) {
            $modifiedColumns[':p' . $index++]  = '`parent_id`';
        }
        if ($this->isColumnModified(ActivityPeer::USER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`user_id`';
        }
        if ($this->isColumnModified(ActivityPeer::CODE)) {
            $modifiedColumns[':p' . $index++]  = '`code`';
        }
        if ($this->isColumnModified(ActivityPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '`description`';
        }
        if ($this->isColumnModified(ActivityPeer::FAVORITES_YN)) {
            $modifiedColumns[':p' . $index++]  = '`favorites_yn`';
        }
        if ($this->isColumnModified(ActivityPeer::SORT_ORDER)) {
            $modifiedColumns[':p' . $index++]  = '`sort_order`';
        }

        $sql = sprintf(
            'INSERT INTO `activities` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`id`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`parent_id`':
                        $stmt->bindValue($identifier, $this->parent_id, PDO::PARAM_INT);
                        break;
                    case '`user_id`':
                        $stmt->bindValue($identifier, $this->user_id, PDO::PARAM_INT);
                        break;
                    case '`code`':
                        $stmt->bindValue($identifier, $this->code, PDO::PARAM_STR);
                        break;
                    case '`description`':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '`favorites_yn`':
                        $stmt->bindValue($identifier, (int) $this->favorites_yn, PDO::PARAM_INT);
                        break;
                    case '`sort_order`':
                        $stmt->bindValue($identifier, $this->sort_order, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            if (($retval = ActivityPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collRegistrations !== null) {
                    foreach ($this->collRegistrations as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = ActivityPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getParentId();
                break;
            case 2:
                return $this->getUserId();
                break;
            case 3:
                return $this->getCode();
                break;
            case 4:
                return $this->getDescription();
                break;
            case 5:
                return $this->getFavoritesYn();
                break;
            case 6:
                return $this->getSortOrder();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Activity'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Activity'][$this->getPrimaryKey()] = true;
        $keys = ActivityPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getParentId(),
            $keys[2] => $this->getUserId(),
            $keys[3] => $this->getCode(),
            $keys[4] => $this->getDescription(),
            $keys[5] => $this->getFavoritesYn(),
            $keys[6] => $this->getSortOrder(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collRegistrations) {
                $result['Registrations'] = $this->collRegistrations->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = ActivityPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setParentId($value);
                break;
            case 2:
                $this->setUserId($value);
                break;
            case 3:
                $this->setCode($value);
                break;
            case 4:
                $this->setDescription($value);
                break;
            case 5:
                $this->setFavoritesYn($value);
                break;
            case 6:
                $this->setSortOrder($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = ActivityPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setParentId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setUserId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setCode($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDescription($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setFavoritesYn($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setSortOrder($arr[$keys[6]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ActivityPeer::DATABASE_NAME);

        if ($this->isColumnModified(ActivityPeer::ID)) $criteria->add(ActivityPeer::ID, $this->id);
        if ($this->isColumnModified(ActivityPeer::PARENT_ID)) $criteria->add(ActivityPeer::PARENT_ID, $this->parent_id);
        if ($this->isColumnModified(ActivityPeer::USER_ID)) $criteria->add(ActivityPeer::USER_ID, $this->user_id);
        if ($this->isColumnModified(ActivityPeer::CODE)) $criteria->add(ActivityPeer::CODE, $this->code);
        if ($this->isColumnModified(ActivityPeer::DESCRIPTION)) $criteria->add(ActivityPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(ActivityPeer::FAVORITES_YN)) $criteria->add(ActivityPeer::FAVORITES_YN, $this->favorites_yn);
        if ($this->isColumnModified(ActivityPeer::SORT_ORDER)) $criteria->add(ActivityPeer::SORT_ORDER, $this->sort_order);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(ActivityPeer::DATABASE_NAME);
        $criteria->add(ActivityPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Activity (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setParentId($this->getParentId());
        $copyObj->setUserId($this->getUserId());
        $copyObj->setCode($this->getCode());
        $copyObj->setDescription($this->getDescription());
        $copyObj->setFavoritesYn($this->getFavoritesYn());
        $copyObj->setSortOrder($this->getSortOrder());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getRegistrations() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addRegistration($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId('0'); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return Activity Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return ActivityPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ActivityPeer();
        }

        return self::$peer;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('Registration' == $relationName) {
            $this->initRegistrations();
        }
    }

    /**
     * Clears out the collRegistrations collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Activity The current object (for fluent API support)
     * @see        addRegistrations()
     */
    public function clearRegistrations()
    {
        $this->collRegistrations = null; // important to set this to null since that means it is uninitialized
        $this->collRegistrationsPartial = null;

        return $this;
    }

    /**
     * reset is the collRegistrations collection loaded partially
     *
     * @return void
     */
    public function resetPartialRegistrations($v = true)
    {
        $this->collRegistrationsPartial = $v;
    }

    /**
     * Initializes the collRegistrations collection.
     *
     * By default this just sets the collRegistrations collection to an empty array (like clearcollRegistrations());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initRegistrations($overrideExisting = true)
    {
        if (null !== $this->collRegistrations && !$overrideExisting) {
            return;
        }
        $this->collRegistrations = new PropelObjectCollection();
        $this->collRegistrations->setModel('Registration');
    }

    /**
     * Gets an array of Registration objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Activity is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Registration[] List of Registration objects
     * @throws PropelException
     */
    public function getRegistrations($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collRegistrationsPartial && !$this->isNew();
        if (null === $this->collRegistrations || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collRegistrations) {
                // return empty collection
                $this->initRegistrations();
            } else {
                $collRegistrations = RegistrationQuery::create(null, $criteria)
                    ->filterByActivity($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collRegistrationsPartial && count($collRegistrations)) {
                      $this->initRegistrations(false);

                      foreach ($collRegistrations as $obj) {
                        if (false == $this->collRegistrations->contains($obj)) {
                          $this->collRegistrations->append($obj);
                        }
                      }

                      $this->collRegistrationsPartial = true;
                    }

                    $collRegistrations->getInternalIterator()->rewind();

                    return $collRegistrations;
                }

                if ($partial && $this->collRegistrations) {
                    foreach ($this->collRegistrations as $obj) {
                        if ($obj->isNew()) {
                            $collRegistrations[] = $obj;
                        }
                    }
                }

                $this->collRegistrations = $collRegistrations;
                $this->collRegistrationsPartial = false;
            }
        }

        return $this->collRegistrations;
    }

    /**
     * Sets a collection of Registration objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $registrations A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Activity The current object (for fluent API support)
     */
    public function setRegistrations(PropelCollection $registrations, PropelPDO $con = null)
    {
        $registrationsToDelete = $this->getRegistrations(new Criteria(), $con)->diff($registrations);


        $this->registrationsScheduledForDeletion = $registrationsToDelete;

        foreach ($registrationsToDelete as $registrationRemoved) {
            $registrationRemoved->setActivity(null);
        }

        $this->collRegistrations = null;
        foreach ($registrations as $registration) {
            $this->addRegistration($registration);
        }

        $this->collRegistrations = $registrations;
        $this->collRegistrationsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Registration objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Registration objects.
     * @throws PropelException
     */
    public function countRegistrations(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collRegistrationsPartial && !$this->isNew();
        if (null === $this->collRegistrations || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collRegistrations) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getRegistrations());
            }
            $query = RegistrationQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByActivity($this)
                ->count($con);
        }

        return count($this->collRegistrations);
    }

    /**
     * Method called to associate a Registration object to this object
     * through the Registration foreign key attribute.
     *
     * @param    Registration $l Registration
     * @return Activity The current object (for fluent API support)
     */
    public function addRegistration(Registration $l)
    {
        if ($this->collRegistrations === null) {
            $this->initRegistrations();
            $this->collRegistrationsPartial = true;
        }

        if (!in_array($l, $this->collRegistrations->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddRegistration($l);

            if ($this->registrationsScheduledForDeletion and $this->registrationsScheduledForDeletion->contains($l)) {
                $this->registrationsScheduledForDeletion->remove($this->registrationsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	Registration $registration The registration object to add.
     */
    protected function doAddRegistration($registration)
    {
        $this->collRegistrations[]= $registration;
        $registration->setActivity($this);
    }

    /**
     * @param	Registration $registration The registration object to remove.
     * @return Activity The current object (for fluent API support)
     */
    public function removeRegistration($registration)
    {
        if ($this->getRegistrations()->contains($registration)) {
            $this->collRegistrations->remove($this->collRegistrations->search($registration));
            if (null === $this->registrationsScheduledForDeletion) {
                $this->registrationsScheduledForDeletion = clone $this->collRegistrations;
                $this->registrationsScheduledForDeletion->clear();
            }
            $this->registrationsScheduledForDeletion[]= clone $registration;
            $registration->setActivity(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->parent_id = null;
        $this->user_id = null;
        $this->code = null;
        $this->description = null;
        $this->favorites_yn = null;
        $this->sort_order = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collRegistrations) {
                foreach ($this->collRegistrations as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collRegistrations instanceof PropelCollection) {
            $this->collRegistrations->clearIterator();
        }
        $this->collRegistrations = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ActivityPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

}
