<?php

declare(strict_types=1);

namespace App\Tests\BoundedContext\Forum\Functional\Api;

use App\BoundedContext\Forum\Domain\Entity\TopicType;
use App\BoundedContext\Forum\Tests\Factory\TopicTypeFactory;

class TopicTypeTest extends AbstractFunctionalTestCase
{
    public function testGetCollection(): void
    {
        TopicTypeFactory::new()
            ->announcement()
            ->create();

        TopicTypeFactory::new()
            ->discussion()
            ->create();

        TopicTypeFactory::new()
            ->question()
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topic_types');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(TopicType::class);

        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:totalItems', $data);
        $this->assertEquals(3, $data['hydra:totalItems']);
    }

    public function testGetSingleTopicType(): void
    {
        $topicType = TopicTypeFactory::new()
            ->announcement()
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topic_types/' . $topicType->getId());

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(TopicType::class);

        $data = $response->toArray();
        $this->assertEquals($topicType->getId(), $data['id']);
        $this->assertEquals('Annonce', $data['name']);
        $this->assertEquals(0, $data['position']);
    }

    public function testTopicTypesOrderedByPosition(): void
    {
        // Create topic types with different positions
        TopicTypeFactory::new()
            ->withPosition(2)
            ->create();

        TopicTypeFactory::new()
            ->withPosition(0)
            ->create();

        TopicTypeFactory::new()
            ->withPosition(1)
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topic_types');

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $topicTypes = $data['hydra:member'];

        // Check that topic types are ordered by position
        $this->assertCount(3, $topicTypes);
        $positions = array_column($topicTypes, 'position');
        $this->assertEquals([0, 1, 2], $positions);
    }

    public function testGetAnnouncementType(): void
    {
        $announcement = TopicTypeFactory::new()
            ->announcement()
            ->create();

        $discussion = TopicTypeFactory::new()
            ->discussion()
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topic_types/' . $announcement->getId());

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertEquals('Annonce', $data['name']);
        $this->assertEquals(0, $data['position']);
    }

    public function testGetDiscussionType(): void
    {
        $discussion = TopicTypeFactory::new()
            ->discussion()
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topic_types/' . $discussion->getId());

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertEquals('Discussion', $data['name']);
        $this->assertEquals(1, $data['position']);
    }

    public function testGetQuestionType(): void
    {
        $question = TopicTypeFactory::new()
            ->question()
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topic_types/' . $question->getId());

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertEquals('Question', $data['name']);
        $this->assertEquals(2, $data['position']);
    }

    public function testTopicTypeWithCustomName(): void
    {
        $customType = TopicTypeFactory::new()
            ->with(['name' => 'Bug Report', 'position' => 5])
            ->create();

        $response = $this->apiClient->request('GET', '/api/forum_topic_types/' . $customType->getId());

        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertEquals('Bug Report', $data['name']);
        $this->assertEquals(5, $data['position']);
    }
}
