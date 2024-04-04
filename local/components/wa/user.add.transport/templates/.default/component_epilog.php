<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$title = 'Добавить ТС';
if($arParams['EDIT_VEHICLE'] && $arParams['EDIT_VEHICLE'] == 'Y'){
    $title = 'Изменить ТС';
}
$APPLICATION->SetTitle($title);
$this->__template->SetViewTarget('page_title');
echo $title;
$this->__template->EndViewTarget();