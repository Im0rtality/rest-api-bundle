# REST API Bundle

This bundle is a RESTful API building tool making process as fast as possible. For example, to achieve simple CRUD you **only** need to:

1. Have your entities (for now only Doctrine is supported)
2. Configure bundle

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Im0rtality/rest-api-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Im0rtality/rest-api-bundle/?branch=master)
[![Build Status](https://travis-ci.org/Im0rtality/rest-api-bundle.svg?branch=master)](https://travis-ci.org/Im0rtality/rest-api-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/79784322-e4c4-4b26-84ca-1e657940b809/mini.png)](https://insight.sensiolabs.com/projects/79784322-e4c4-4b26-84ca-1e657940b809)

# Installation

### 1. Install via Composer

    $ composer require "im0rtality/rest-api-bundle:dev-master"

### 2. Activate it

Enable bundle in kernel:

```php
// app/AppKernel.php
<?php

public function registerBundles()
{
    $bundles = array(
        // ...

        // this bundle depends on following two
        new JMS\SerializerBundle\JMSSerializerBundle(),
        new FOS\UserBundle\FOSUserBundle(),

        new Im0rtality\ApiBundle\Im0rtalityApiBundle(),
    );
}

```

### 3. Register routes

Add following to your routing configuration:

```yml
im0rtality_api:
    resource: "@Im0rtalityApiBundle/Resources/config/routing.yml"
    prefix:   /api

```

### 4. Configure FOSUserBundle's security

Described in details https://github.com/FriendsOfSymfony/FOSUserBundle/blob/1.3.x/Resources/doc/index.md#step-4-configure-your-applications-securityyml

### 5. Disable security for API route

Add following to your security configuration:

```yml
security:
    role_hierarchy:
        # Simple admin/user configuration
        ROLE_OWNER:       ROLE_USER
        ROLE_ADMIN:       ROLE_OWNER

    access_control:
        # API bundle takes care of security
        - { path: ^/api, role: IS_AUTHENTICATED_ANONYMOUSLY }
```

> Heads-up! `ROLE_OWNER` should not be set on user explicitly. It is added to user roles (in bundle scope only) internally.

# Configuration

> You can find minimal sample app with configuration and everything in [tests directory](https://github.com/Im0rtality/rest-api-bundle/tree/master/test) of this project (maintained for behat tests)

Example configuration:

```yml
# app/config/config.yml

im0rtality_api:
    acl: api_acl.yml
    mapping:
        user: Acme\DemoBundle\Entity\User
    data:
        type: orm
    ownership:
        "Acme\DemoBundle\Entity\User": id
```

-----------
key | description
----|------
acl | Relative file path which contains ACL configuration stored in YAML format
mapping | Aliases to your entity used in URL
data.type | Data source (right now only `orm` is supported)
ownership | Entity class name and field linking given entity to it's "owner" user
