## About

This project is just for demontration about REST API built on laravel.  

## Minimum Requirements

- PHP 8.1
- MySql 5.x
- Apache 2.x


## Installation 

In any terminal/CMD follow the following steps

- git clone https://github.com/rnsharma93/webshoop.git
- cd webshoop
- composer install
- cp .env.example .env 
- update database credentials in .env file => DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- php artisan migrate
- php artisan key:generate
- php artisan serve



## Import CSV Data from server

- php artisan import:data product (it will import product csv into database products table)  

- php artisan import:data customer (it will import customer csv into database customers table )


## API endpoints

All API request should send following headers parameters  
1. Accept : application/json
2. Content-Type: application/json

- Users login  
    GET => <base_url>/api/login?id={customer_id} //customer_id as 1,2,3 from customer table
    ## It will return token , this token should be send as Authorization: Bearer token in all following API requests  

- Create an order   
    POST => <base_url>/api/order  
    It will create an order and return the order information.

- Add Product to an order  
    POST => <base_url>/api/order/{order_id}/product  
        @Parameters  
        1. product_id (required)  

- Remove Product from an order  
    DELETE => <base_url>/api/order/{order_id}/product/{product_id}  

- Choose payment method to pay an order  
    POST => <base_url>/api/order/{order_id}/payment-method  
        @parameters  
        1. payment_code = superpay (only accepted value for now)  

- Pay an order  
    POST => <base_url>/api/order/{order_id}/pay  

- Delete an order  
    DELETE => <base_url>/api/order/{order_id}  

- All orders related to authorize customer  
    GET => <base_url>/api/orders  

- Get order details  
    GET => <base_url>/api/order/{order_id}  


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
