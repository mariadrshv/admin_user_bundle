Changelog
All backwards compatible breaking changes (other changes are possible) to this project will be documented in this file.

The format is based on Keep a Changelog, and this project adheres to Semantic Versioning.

[1.0.0] - 2019-05-07
Backwards compatible breaking changes
Новый корневой namespace у всех классов, AppyFurious заменён на Appyfurious. Необходимы изменения во всех блоках use, описании Doctrine сущностей, файлах конфигурации и других местах. Для поиска можно использовать AppyFurious\AdminUserBundle и AppyFuriousAdminUser. Рекомендуется таким же образом обновить корневой namespace в проекте.
[1.1.0] - 2019-07-19
Added
Возможность использовать для авторизации Appy SSO - единый сервер авторизации, который использует Google аккаунты @appyfurious.com для авторизации.
[1.2.0] - 2020-01-03
Updated
Обновлено использование AbstractController вместо Controller из пакета Symfony\Bundle\FrameworkBundle. Это необходимо для совместимости с версиями Symfony выше 4.3

[2.0.0-alpha.1] - 2020-08-18
Added
Добавлена новая авторизация на базе Symfony 5.1