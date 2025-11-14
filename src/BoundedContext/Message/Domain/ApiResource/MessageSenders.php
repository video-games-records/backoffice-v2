<?php

namespace App\BoundedContext\Message\Domain\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\Message\Presentation\Api\Controller\GetSenders;

#[ApiResource(
    shortName: 'Message',
    uriTemplate: '/messages/get-senders',
    operations: [
        new Get(
            controller: GetSenders::class,
            read: false,
            security: 'is_granted("ROLE_USER")',
            openapi: new Model\Operation(
                summary: 'Get message senders',
                description: 'Get all users who sent messages to the authenticated user',
                responses: [
                    200 => new Model\Response(
                        description: 'List of message senders',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer'],
                                            'username' => ['type' => 'string']
                                        ]
                                    ]
                                ]
                            ]
                        ])
                    )
                ]
            )
        )
    ]
)]
class MessageSenders
{
}
