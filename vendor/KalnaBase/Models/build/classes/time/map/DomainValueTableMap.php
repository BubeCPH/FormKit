<?php



/**
 * This class defines the structure of the 'domain_values' table.
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
class DomainValueTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'time.map.DomainValueTableMap';

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
        $this->setName('domain_values');
        $this->setPhpName('DomainValue');
        $this->setClassname('DomainValue');
        $this->setPackage('time');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('domain', 'Domain', 'VARCHAR', true, 45, null);
        $this->addPrimaryKey('low_value', 'LowValue', 'VARCHAR', true, 45, null);
        $this->addColumn('high_value', 'HighValue', 'VARCHAR', false, 45, null);
        $this->addColumn('abbreviation', 'Abbreviation', 'VARCHAR', true, 45, null);
        $this->addColumn('meaning', 'Meaning', 'VARCHAR', true, 45, null);
        $this->addColumn('lang', 'Lang', 'VARCHAR', true, 100, 'da_DK');
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // DomainValueTableMap
