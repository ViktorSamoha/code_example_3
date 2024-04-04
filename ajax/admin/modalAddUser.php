<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$post = $request->getPostList();

if ($post['USER_RECORD_ID']) {
    $APPLICATION->IncludeComponent(
        "wa:admin.permission.add",
        "modal",
        [
            'USER_RECORD_ID' => $post['USER_RECORD_ID']
        ],
        false
    );
}
