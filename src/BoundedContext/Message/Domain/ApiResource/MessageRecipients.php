<?php

namespace App\BoundedContext\Message\Domain\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\Message\Presentation\Api\Controller\GetRecipients;

#[ApiResource(
    shortName: 'Message',
    uriTemplate: '/messages/get-recipients',
    operations: [
        new Get(
            controller: GetRecipients::class,
            read: false,
            security: 'is_granted("ROLE_USER")',
            openapi: new Model\Operation(
                summary: 'Get message recipients',
                description: 'Get all users who received messages from the authenticated user',
                responses: [
                    200 => new Model\Response(
                        description: 'List of message recipients',
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
class MessageRecipients
{
}
