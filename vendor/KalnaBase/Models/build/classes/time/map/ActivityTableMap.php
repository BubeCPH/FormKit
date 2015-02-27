<?php



/**
 * This class defines the structure of the 'activities' table.
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
class ActivityTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'time.map.ActivityTableMap';

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
        $this->setName('activities');
        $this->setPhpName('Activity');
        $this->setClassname('Activity');
        $this->setPackage('time');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, 0);
        $this->addColumn('parent_id', 'ParentId', 'INTEGER', false, null, null);
        $this->addColumn('user_id', 'UserId', 'INTEGER', true, null, null);
        $this->addColumn('code', 'Code', 'VARCHAR', true, 100, null);
        $this->addColumn('description', 'Description', 'VARCHAR', true, 100, null);
        $this->addColumn('favorites_yn', 'FavoritesYn', 'BOOLEAN', true, 1, false);
        $this->addColumn('sort_order', 'SortOrder', 'SMALLINT', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Registration', 'Registration', RelationMap::ONE_TO_MANY, array('id' => 'activity_id', ), null, null, 'Registrations');
    } // buildRelations()

} // ActivityTableMap
