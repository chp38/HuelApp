# Average Order App

This application will download the Products/Customers/Orders from a given
Shopify API, and store them in a database. Endpoints will then be provided
to get the average order value over all customers, for a given customer and for
a given variant.

1. Laravel 8
2. MySQL

Found the challenge very fun, using what I already know of laravel and 
incorporating laravel packages on top of that for a more complete application.

## Usage

Use Docker to set up this project's local development environment, contains 3
containers within the network:

 - container_redis: used as the Redis serer
 - container_php: container for the actual application
 - container_nginx: nginx web server, will forward request to container_php
 - container_mysql: mysql database server
 
Copy the .env.example to .env and add values for:

required:

```sh
SHOPIFY_AUTH_TOKEN=
SHOPIFY_AUTH_PASSWORD=
SHOPIFY_STORE=
SHOPIFY_SLUG_PREFIX={/admin/api/2020-07/}
```

optional - used for webhook authentication:

```sh
SHOPIFY_APP_SECRET=
```

You can then run:

```sh
./run
```

Which will do all the setting up for development and getting the app up and 
running. This script runs the following, build and up the docker containers and
migrates the database.

```sh
docker-compose build
docker-compose up -d

docker exec -it container_app php artisan migrate:fresh --seed
```

It also runs these three commands:

```sh
docker exec -it container_app php artisan shopify:get:customers
docker exec -it container_app php artisan shopify:get:products
docker exec -it container_app php artisan shopify:get:orders
```

These three artisan commands, will grab the customers, products and orders from
Shopify, and add them to the local databse, as a quick and re-usable way to get 
the database populated with the data that we need.

You may also need to run the following command to generate the key for the .env:

```sh
docker exec -it container_app php artisan key:generate
```

## Services

Two main services are provided by the application:

- AverageOrderValueService:
  The application needs to provide average order values for varius different
  scenarios, therefore this service encapsulates the logic to generate the 
  values, from the data.
  
  Creating the Average Order Values was quite easy, calling the avg() function
  on the Eloquent models, with the callback to the field to be used.

- ShopifyService: 
  One of the functions of the application is to request data from Shopify, hence
  this class to handle the logic to extract the required information from the 
  Shopify data, and prepare it to be stored.
  
## Repositories

Repositories have been used to abstract dealing with models out of the services
where required. Another main reason for using repositories is that Eloquent 
models can't be used with the Shopify API, therefore placing the logic to deal
with Shopify as a Repository for data here seems sensible.

There are two types of Repositories, ones that deal with Eloquent Models as the 
data source, and ones that deal with Shopify, Laravel HttpClient.

## Average Order Endpoints

The application includes 3 endpoints for the average orders, these are:

```php
get /api/customers/average-order-value
get /api/customer/{id}/average-order-value
get /api/variant/{id}/average-order-value
```

Returning the average order price of all orders, all orders for a given customer
and all orders for a given variant.


## Dealing With Shopify

The app contains two ways to get data into the MySQL database from Shopify, the 
first way is mentioned in the usage section (see above), and involves a 
laravel command for each of products, customers, and orders, this is so that we 
have a quick and easy way to bulk retrieve all of the data from Shopify at 
any time.

The second way of which data from Shopify may be obtained is through the use of
webhooks. Shopify supplies a webhook feature, so setting up the webhook in the 
Shopify admin panel, to send events to any of the following links is possible:

```php
post /api/shopify/create/customer
post /api/shopify/create/product
post /api/shopify/create/order
```

This was tested by using ngrok, and adding the ngrok url into the Webhook for
create_customer event in Shopify, and sending a test event to the app, which 
updated okay.

Error handling could be done better here and ensuring values are present and
good, but time constraints of the test were reached.

## Sanctum / JetStream / Interia JS

This task required me to create endpoints to be used in an SPA, so I decided
to integrate a few new laravel technologies I wanted to use to help with this.

Laravel Sanctum provides SPA auth, with this I used JetStream with the Interia
suit to create a SPA application so I could test out the endpoints created for
the average order values. 

Suggest that when using the app, go to

- /register

And create a user account, then login at

- /login

Once logged in, head to

- /averages

to see the three averages and some meta data. Currently the specific customer 
and variant average order values are hardcoded to a set value.

## Other Design Choices

### Products and Variants

Looking at the products and variants, I thought it would be ideal to store both
in one table, but have the Shopify products as a parent, and each variant as a 
child product, using the `product_id` column to link to the parent.

## Further work

There is two features I would have liked to have added:

1. Add the ability to change the customer/product variant to get different 
average orders.

2. Attempt to cache using redis the average order for a particular customer/
variant and all orders. I planned to do this by storing the AOV with a date
of the last current order in the DB, and check this, if it's different then 
recompute the AOV, return if the date is the same.