<?php

namespace Modules\ReferrerModule\Traits;

use Modules\ReferrerModule\Entities\Referral;

trait ReferrerTraits
{

    public function referrers()
    {
        return $this->hasMany(Referral::class);
    }
}
