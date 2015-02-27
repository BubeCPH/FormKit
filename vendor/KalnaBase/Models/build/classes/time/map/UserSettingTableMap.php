<?php



/**
 * This class defines the structure of the 'user_settings' table.
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
class UserSettingTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'time.map.UserSettingTableMap';

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
        $this->setName('user_settings');
        $this->setPhpName('UserSetting');
        $this->setClassname('UserSetting');
        $this->setPackage('time');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, 0);
        $this->addColumn('uid', 'Uid', 'INTEGER', true, null, null);
        $this->addColumn('prm_id', 'PrmId', 'INTEGER', true, null, null);
        $this->addColumn('value', 'Value', 'VARCHAR', true, 100, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // UserSettingTableMap
