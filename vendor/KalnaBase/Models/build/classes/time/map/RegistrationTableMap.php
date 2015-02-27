<?php



/**
 * This class defines the structure of the 'registrations' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.time.map
 */
class RegistrationTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'time.map.RegistrationTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('registrations');
        $this->setPhpName('Registration');
        $this->setClassname('Registration');
        $this->setPackage('time');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('user_id', 'UserId', 'INTEGER', true, null, null);
        $this->addForeignKey('activity_id', 'ActivityId', 'INTEGER', 'activities', 'id', true, null, null);
        $this->addColumn('state', 'State', 'VARCHAR', true, 10, 'CLOSED');
        $this->addColumn('start_date', 'StartDate', 'DATE', true, null, null);
        $this->addColumn('start_time', 'StartTime', 'TIME', true, null, null);
        $this->addColumn('end_date', 'EndDate', 'DATE', false, null, null);
        $this->addColumn('end_time', 'EndTime', 'TIME', false, null, null);
        $this->addColumn('description', 'Description', 'VARCHAR', false, 100, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        $this->addColumn('created_by_user_id', 'CreatedByUserId', 'INTEGER', true, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_by_user_id', 'UpdatedByUserId', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Activity', 'Activity', RelationMap::MANY_TO_ONE, array('activity_id' => 'id', ), null, null);
    } // buildRelations()

} // RegistrationTableMap
