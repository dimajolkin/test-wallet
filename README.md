Test App
====================

[![Build Status](https://travis-ci.org/dimajolkin/test-wallet.svg?branch=master)](https://travis-ci.org/dimajolkin/test-wallet)

# Требования

Реализовать методы API для работы с кошельком пользователя. Ограничения:
* У пользователя может быть только один кошелек.
* Поддерживаемые валюты: USD и RUB.
* При вызове метода для изменения кошелька на сумму с отличной валютой от валюты кошелька, сумма должна конвертироваться по курсу. 
* Курсы обновляются периодически.
* Все изменения кошелька должны фиксироваться в БД.
Метод для изменения баланса

#### Обязательные параметры метода:
* ID кошелька (например: 241, 242)
* Тип транзакции (debit или credit)
* Сумма, на которую нужно изменить баланс
* Валюта суммы (допустимы значения: USD, RUB)
* Причина изменения счета (например: stock, refund). Список причин фиксирован.

#### Метод для получения текущего баланса 
Обязательные параметры метода:
* ID кошелька (например: 241, 242)

#### SQL запрос
Написать SQL запрос, который вернет сумму, полученную по причине refund за последние 7 дней.

#### Технические требования
* Серверная логика должна быть написана на PHP версии >=7.0
* Для хранения данных должна использоваться реляционная СУБД
* Должны быть инструкции для развертывания проекта
