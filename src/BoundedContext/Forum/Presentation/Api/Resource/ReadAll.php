<?php

namespace App\BoundedContext\Forum\Presentation\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\BoundedContext\Forum\Presentation\Api\Controller\MarkAdReadAll;

#[ApiResource(
    shortName: 'ForumForum',
    operations: [
        new Post(
            uriTemplate: '/forum_forums/read-all',
            controller: MarkAdReadAll::class,
            security: 'is_granted("ROLE_USER")',
        ),
    ],
)]

class ReadAll
{
}
