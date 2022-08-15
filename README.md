# Transactional Email Microservice

## Tools Used

* Docker
* PHP 8.1
* Laravel 9.x
* Traefik
* RabbitMQ

## Description

* This service aims to send emails via API call or CLI with a fallback mechanism.
* Responsibilities of the application are shared amongst the layers like explained below:
  * Controller layer -> Request validation and service initialization
  * Service Layer -> Contains business logic, can only access to its own repository, can interact with other entities via calling other services
  * Repository Layer -> Contains interactions with an entity
  * Model Layer -> Represents database entities
* Service supports sendgrid and mailjet as mail delivery providers
* The service will try to send the email with every provider defined in `app/config/mail.php` until one of them successfully sends the email.
* Every provider must implement `App\Services\Mail\MailDeliveryAdapterInterface`.
* When a new provider is going to be introduced, the adapter must be prepared and it must be listed in the `providers` configuration from `app/config/mail.php`
* The service uses jobs to send an email asynchronously
* In order to provide horizontal scalability, the queue worker is being separated from the app service (see setup for scaling instances)
* If none of the providers are available, it will re-queue the email sending job with 30 seconds of delay and it will attempt to send the email 10 times. 
* At every attempt, job will be delayed for 30 seconds and will be retried after 30 seconds.
* The values for delay duration and attempts are configurable and can be found in `app/config/mail.php`
* The service allows users to send one template to multiple recipients. (see examples for details)
* It will create a separate job and a separate mail record for each recipient and will process these jobs one by one
* The mails sent are being tagged with a delivery group hash so the service can keep track of the batch sending
* Once the email gets sent, the `sent_at` column and `provider` column gets filled from mails table.

## Setup 
Set up your sendgrid api key, mailjet api key and mailjet secret in `.env.local` file

    SENDGRID_API_KEY=<SENDGRID_API_KEY>
    MAILJET_API_KEY=<MAILJET_API_KEY>
    MAILJET_API_SECRET=<MAILJET_API_SECRET>

Run
    
    make build

Run the command below to set up fresh environment

    make fresh-up

Once all the containers are up and ready _(usually takes about 30 sec)_ run below command to set up database structure

    make migrate-fresh

The API will be available at

    http://localhost:8020

To reach to the CLI to create an email by running command below

    make create-mail

To run the tests with coverage report

    make test

To scale up/down app instance

    make scale-app <number of instances desired>

To scale up/down queue worker instance

    make scale-worker <number of instances desired>

## API Example

* The endpoint accepts multiple recipients
* The endpoint accepts content type of `text/html` or `text/plain`
* The endpoint allows you to send an email with replaceable content by adding {{first_name}} and {{last_name}}


    POST http://localhost:8020/api/v1/mail
    Content-Type: application/json
    Accept: application/json

    {
      "recipients": [
          {
              "email": "your@email.com",
              "first_name": "Firstname",
              "last_name": "Lastname"
          }
      ],
      "content": {
          "content": "Hi {{first_name}} {{last_name}}, This is an example content",
          "content_type": "text/plain"
      },
      "mail": {
          "subject": "Greetings."
      }
    }

## Services

### Application

Available at :`http://localhost:8020`

Available Endpoints

``
POST http://localhost:8020/api/v1/mail
``


### Traefik

Dashboard available at: `http://localhost:8080/`

Is being used as reverse proxy for scalability of app service

### MySQL

Accessible at `localhost:8021`

Username: `root`

Password: `root2root`

Database: `transactional_mail`

### RabbitMQ

Accessible at `localhost:15672`

Username: `guest`

Password: `guest`

### Queue Worker

Not accessible logs can be read by running `make tail queue-worker`