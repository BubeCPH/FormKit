<?php



/**
 * This class defines the structure of the 'settings' table.
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
class SettingTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'time.map.SettingTableMap';

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
        $this->setName('settings');
        $this->setPhpName('Setting');
        $this->setClassname('Setting');
        $this->setPackage('time');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 100, null);
        $this->addColumn('description', 'Description', 'VARCHAR', true, 100, null);
        $this->addColumn('long_description', 'LongDescription', 'VARCHAR', false, 250, null);
        $this->addColumn('type', 'Type', 'VARCHAR', true, 10, null);
        $this->addColumn('default_value', 'DefaultValue', 'VARCHAR', true, 100, null);
        $this->addColumn('formula', 'Formula', 'VARCHAR', false, 250, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // SettingTableMap
