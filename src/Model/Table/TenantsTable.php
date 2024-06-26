<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Table\Traits\BasicTableTrait;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use QueryFilter\QueryFilterPlugin;

/**
 * Tenants Model
 *
 * @property \App\Model\Table\LapsesTable&\Cake\ORM\Association\HasMany $Lapses
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\HasMany $Students
 * @property \App\Model\Table\TenantFiltersTable&\Cake\ORM\Association\HasMany $TenantFilters
 * @method \App\Model\Entity\Tenant newEmptyEntity()
 * @method \App\Model\Entity\Tenant newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Tenant[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tenant get($primaryKey, $options = [])
 * @method \App\Model\Entity\Tenant findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Tenant patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Tenant[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tenant|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tenant saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tenant[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Tenant[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Tenant[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Tenant[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class TenantsTable extends Table
{
    use BasicTableTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('tenants');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('QueryFilter.QueryFilter');
        $this->addBehavior('FilterTenant', [
            'field' => 'id',
        ]);

        $this->hasMany('Lapses', [
            'foreignKey' => 'tenant_id',
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'tenant_id',
        ]);
        $this->hasMany('TenantFilters', [
            'foreignKey' => 'tenant_id',
        ]);
        $this->hasOne('CurrentLapse', [
            'className' => 'Lapses',
            'foreignKey' => 'tenant_id',
            'strategy' => 'select',
            'finder' => 'lastElement',
        ]);
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
        ]);
        $this->belongsTo('Locations', [
            'foreignKey' => 'location_id',
        ]);

        $this->loadQueryFilters();
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(
            ['location_id', 'program_id'],
            __('This location is already assigned to this program'),
        ));

        return $rules;
    }

    /**
     * @param \Cake\ORM\Query $query
     * @param array $options
     * @return \Cake\ORM\Query
     */
    public function findComplete(Query $query, array $options): Query
    {
        return $query->contain([
            'Locations',
            'Programs' => ['Areas'],
        ]);
    }

    /**
     * @param \Cake\ORM\Query $query
     * @param array $options
     * @return \Cake\ORM\Query
     */
    public function findListLabel(Query $query, array $options): Query
    {
        return $query
            ->find('complete')
            ->find('list', array_merge([
                'keyField' => 'id',
                'valueField' => 'label',
                'groupField' => 'program.area_label',
            ], $options));
    }

    /**
     * @return void
     */
    public function loadQueryFilters()
    {
        $this->addFilterField('name', [
            'tableField' => $this->aliasField('name'),
            'finder' => QueryFilterPlugin::FINDER_SELECT,
        ]);

        $this->addFilterField('program_id', [
            'tableField' => $this->aliasField('program_id'),
            'finder' => QueryFilterPlugin::FINDER_SELECT,
        ]);

        $this->addFilterField('active', [
            'tableField' => $this->aliasField('active'),
            'finder' => QueryFilterPlugin::FINDER_SELECT,
        ]);
    }
}
