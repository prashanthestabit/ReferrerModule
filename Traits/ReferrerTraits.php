<?php

namespace Modules\ReferrerModule\Traits;

use Modules\ReferrerModule\Entities\Referral;

trait ReferrerTraits
{

    public function referrers()
    {
        return $this->hasMany(Referral::class);
    }

    public function additionalFillableAttributes()
    {
        return [
            'referrer_id'
        ];
    }

    public function getFillable()
    {
        return array_merge($this->fillable, $this->additionalFillableAttributes());
    }
}
