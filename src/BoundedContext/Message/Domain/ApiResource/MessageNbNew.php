<?php

namespace App\BoundedContext\Message\Domain\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use App\BoundedContext\Message\Presentation\Api\Controller\GetNbNewMessage;

#[ApiResource(
    shortName: 'Message',
    uriTemplate: '/messages/get-nb-new-message',
    operations: [
        new Get(
            controller: GetNbNewMessage::class,
            read: false,
            security: 'is_granted("ROLE_USER")',
            openapi: new Model\Operation(
                summary: 'Get number of new messages',
                description: 'Get the number of new unread messages for the authenticated user',
                responses: [
                    200 => new Model\Response(
                        description: 'Number of new messages',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'count' => ['type' => 'integer']
                                    ]
                                ],
                                'example' => [
                                    'count' => 5
                                ]
                            ]
                        ])
                    )
                ]
            )
        )
    ]
)]
class MessageNbNew
{
}
