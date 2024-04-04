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

if ($post['USER_NUMBER']) {
    if (Loader::includeModule("iblock")) {
        $arUsers = [];
        $arParams["FIELDS"] = array("ID", "NAME", "LAST_NAME", "SECOND_NAME");
        $filter = array("ACTIVE" => "Y", "WORK_PHONE" => $post['USER_NUMBER']);
        $rsUsers = CUser::GetList(($by = "id"), ($order = "desc"), $filter, $arParams);
        while ($res = $rsUsers->GetNext()) {
            $arUsers[$res['ID']] = [
                'ID' => $res['ID'],
                'LAST_NAME' => $res['LAST_NAME'],
                'NAME' => $res['NAME'],
                'SECOND_NAME' => $res['SECOND_NAME'],
            ];
        }
        if (!empty($arUsers)) {
            ?>
            <div class="input-label input-label--mb input-label--gray">Посетители, привязанные к номеру
            </div>
            <div class="custom-select" id="ajax-select">
                <div class="custom-select_head">
                    <span class="custom-select_title">Пользователь</span>
                    <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L7 7L13 1" stroke="#000"/>
                    </svg>
                </div>
                <div class="custom-select_body">
                    <?
                    foreach ($arUsers as $userId => &$userFields) {
                        $arSelect = array("ID");
                        $arFilter = array("IBLOCK_ID" => IB_USERS, "ACTIVE" => "Y", 'PROPERTY_USER_ID' => $userId);
                        $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
                        while ($ob = $res->GetNextElement()) {
                            $arFields = $ob->GetFields();
                            if ($arFields) {
                                $userFields['USER_RECORD_ID'] = $arFields['ID'];
                            }
                        }
                        if ($userFields['USER_RECORD_ID']) {
                            ?>
                            <div class="custom-select_item" data-id="<?= $userFields['USER_RECORD_ID'] ?>">
                                <?= $userFields['LAST_NAME'] . ' ' . $userFields['NAME'] . ' ' . $userFields['SECOND_NAME'] ?>
                            </div>
                            <?
                        }
                    }

                    ?>
                </div>
            </div>
            <?
        }
    }
}