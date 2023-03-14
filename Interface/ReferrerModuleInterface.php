<?php

namespace Modules\ReferrerModule\Interface;

interface ReferrerModuleInterface
{
    public function responseMessage($responseData, $statusCode);

    public function getUserField($id, $fieldName);

    public function getUserData($condition);

    public function saveReferral($data);

}
