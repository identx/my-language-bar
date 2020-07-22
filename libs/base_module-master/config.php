<?php

class Config
{
    // Ваш секретный ключ (из настроек проекта в личном кабинете UnitPay )
    const SECRET_KEY = '64d93f1509fb7d78953747ff1c51cdfc';
    // Стоимость товара в руб.
    const ITEM_PRICE = 10;

    // Таблица начисления товара, например `users`
    const TABLE_ACCOUNT = 'unitpay_payments';
    // Название поля из таблицы начисления товара по которому производится поиск аккаунта/счета, например `email`
    const TABLE_ACCOUNT_NAME = 'account';
    // Название поля из таблицы начисления товара которое будет увеличено на колличево оплаченого товара, например `sum`, `donate`
    const TABLE_ACCOUNT_DONATE= 'sum';

    // Параметры соединения с бд
    // Хост
    const DB_HOST = 'localhost';
    // Имя пользователя
    const DB_USER = 'langbar';
    // Пароль
    const DB_PASS = 'Q1w2e3r4t5y6!1';
    // Назывние базы
    const DB_NAME = 'langbar';
    // номер порта(необязательно)
    const DB_PORT = 3306;

}