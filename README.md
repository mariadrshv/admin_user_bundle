AdminUserBundle
===============

Бандл предоставляет универсальный интерфейс для доступа и менеджмента пользователей. Symfony 5.1

## Installation

Необходимо активировать бандл в AppKernel:

    new Appyfurious\AdminUserBundle\AppyfuriousAdminUserBundle(),
    
В config/packages/security.yaml добавить:

    providers:
            users:
                entity:
                    class: Appyfurious\AdminUserBundle\Entity\AdminUser
    encoders:
        Appyfurious\AdminUserBundle\Entity\AdminUser:
            algorithm: auto
            
    access_control:
            - { path: ^/admin/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/send-email, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/check-email, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin, role: ROLE_USER }          
   
В папке config/packages создать конфигурационный файл с названием 'appyfurious_admin_user' и заполнить его

    appyfurious_admin_user:
      mailer_user: '%env(resolve:MAILER_USER)%'
      
В папке config/routes создать файл 'appyfurious_admin_user', в котором будет:

    appyfurious_admin_user:
      resource: "@AppyfuriousAdminUserBundle/Controller"
      prefix:  /admin/

Для корректного отображение не забыть вызвать `bin/console c:c`, `bin/console assets:install`.
