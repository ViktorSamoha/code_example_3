<?php

const ASSETS = "/assets/development/"; //путь к папке assets
const DEFAULT_TEMPLATE = '/local/templates/.default/assets/'; //путь к локальной папке assets
const HL_OBJECT_FEATURES = 1; //хл блок "свойства объекта"
const IB_OBJECT = 1; //инфоблок "Объекты"
const HL_OBJECT_BOOKING = 2; //хл инфоблок "Забронированные даты"
const IB_BOOKING_LIST = 3;//инфоблок "Список бронирвоания"
const SITE_ADMIN_GROUP_ID = 7;//идентификатор группы администраторов сайта
const SITE_OPERATOR_GROUP_ID = 8;//идентификатор группы операторов сайта
const SITE_RESERV_GROUP_ID = 9;//идентификатор группы "Бронь" сайта
const IB_LOCATIONS = 2;//инфоблок "Локации"
const VISIT_PERMISSION_COST = 200; //стоимость разрешения на посещение парка
const IB_ORDERS_ARCHIVE = 4; //инфоблок "Архив заказов"
const HL_STATS = 3; //хл блок "Статистика по заказам"
const HL_PARTNERS = 4; //хл блок "Список партнеров"
const OBJECT_PROPERTY_PARTNERS = 71; //номер свойства объекта "Список партнеров"
const CAR_POSSIBILITY = 74;
const GUEST_CARS = 75;
const CAR_POSSIBILITY_YES_VAL_LIST_ID = 14;//dev = 16
const CAR_POSSIBILITY_NO_VAL_LIST_ID = 15;//dev = 17
const CAR_CAPACITY = 76;
const HL_OBJECT_TYPE = 5;//хл блок "Тип объекта"
const IB_VISITORS = 7;//инфоблок "Посетители"
const IB_USERS = 6;//инфоблок "Пользователи"
const HL_VEHICLE_TYPE = 6;//хл блок "Тип транспортного средства"
const IB_TRANSPORT = 8;//инфоблок "Транспорт"
const TOURISTS_ROUTS_SECTION_ID = 98;//идентификатор раздела "Туристические маршруты"
const IB_TRANSPORT_PERMISSION = 10;//инфоблок "Разрешение на транспортное средство"
const IB_PERMISSION = 9;//инфоблок "Разрешение на посещение"
const VEHICLE_PERMISSION_STATUS = 26;//id статуса свойства PERMISSION_STATUS инфоблока разрешение для ТС
const VISITOR_PERMISSION_GROUP = 10;//id группы пользователей "Посетители"
const NATIVE_ID = 49; //id льготной категории "Проживающие в близлежащих населенных пунктах" из свойств посетителя
const PROPERTY_TIME_INTERVAL_DAY = 33; //id значения списка "дневное пребывание" свойства объекта "временной интервал" (TIME_INTERVAL)
const PROPERTY_TIME_INTERVAL_COUPLE = 32; //id значения списка "сутки" свойства объекта "временной интервал" (TIME_INTERVAL)
const HL_ROUTE_BOOKING_ID = 7; //хл инфоблок "Забронированные маршруты"