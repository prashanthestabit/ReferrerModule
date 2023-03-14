<?php

namespace Modules\ReferrerModule\Interface;

interface ReferrerModuleInterface
{
    public function responseMessage($responseData, $statusCode);

    public function getUserField($id, $fieldName);

    public function getUserData($condition);

    public function saveReferral($data);

    public function updateReferral($id, $data);

    public function deleteReferral($id);

    public function userFindOrFail($id);

}
