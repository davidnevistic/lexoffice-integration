<?php

namespace App\Model\Table;

use App\Helper\Constants;
use Cake\ORM\Table;



/**
 * PartnersSettings Model
 *
 * @property \App\Model\Table\PartnersTable|\Cake\ORM\Association\BelongsTo $Partners
 * @property \App\Model\Table\SettingsTable|\Cake\ORM\Association\BelongsTo $Settings
 *
 * @method \App\Model\Entity\PartnersSetting get($primaryKey, $options = [])
 * @method \App\Model\Entity\PartnersSetting newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PartnersSetting[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PartnersSetting|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PartnersSetting patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PartnersSetting[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PartnersSetting findOrCreate($search, callable $callback = null, $options = [])
 */
class PartnersSettingsTable extends Table
{
    public function verifySubscription($settingId = null, $partnerId = null) {
        $whitelabelSettings = [
            Constants::IS_LEX_OFFICE_ENABLED,
            Constants::LEX_OFFICE_API_KEY
        ];
    }
}
