# Symfony Messenger SNS pack

This small package helps you install and configure Symfony Messenger with SNS. If installed with Flex you will have most
things configured for you. 

## Package contents

#### bin/message-consumer

This file will be "composer copied" to your projects bin folder (ie `vendor/bin/message-consumer`). This file receives 
SNS messages and gives them to `App\Consumer\SnsConsumer`.

#### src/Consumer/SnsConsumer

This file will be copied by Flex to `App\Consumer\SnsConsumer`. This class is responsible to decode the SNS message
and put it back on the message bus. Feel free to modify this file after your needs. Maybe configure [retry mechanism](http://developer.happyr.com/sns-retry).

#### config/sns-consumer.yaml

This file will be copied by Flex to `config/packages/sns-consumer.yaml`. It contains example configuration for 
[Messenger](https://symfony.com/doc/current/components/messenger.html) and [Enqueue Bundle](https://php-enqueue.github.io/bundle/quick_tour/). 

## Bref template

Here is a small snippet to make sure we configure a consumer for SNS with Bref. 

```yaml
Resources:
    Consumer:
        Type: AWS::Serverless::Function
        Properties:
            FunctionName: 'my-app-consumer'
            Handler: vendor/bin/message-consumer
            Timeout: 20 # in seconds
            MemorySize: 2048
            # ...
            Events:
                Sns:
                    Type: SNS
                    Properties:
                        Topic: arn:aws:sns:eu-central-1:xxxxxxx:my_sns_topic

```

## Local Development

When working in production we are using SNS but in local development we may want to use something simpler, say RabbitMQ. 

```
# .env

# Production
AWS_SNS_DSN=enqueue://acme?topic[name]=my_sns_topic

# Development
AWS_SNS_DSN=amqp://guest:guest@127.0.0.1:5672/%2f/fake_sns 
```

Then you can consume messages are you normally would with `bin/console messenger:consume`.
