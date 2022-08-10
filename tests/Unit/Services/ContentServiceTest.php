<?php

namespace Tests\Unit\Services;

use App\Models\Content;
use App\Models\Recipient;
use App\Repositories\ContentRepository;
use App\Services\ContentService;
use Illuminate\Support\Facades\Validator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class ContentServiceTest extends TestCase
{
    public MockInterface|ContentRepository $contentRepository;
    protected function setUp(): void
    {
        parent::setUp();
        $this->contentRepository = $this->mock(ContentRepository::class);
    }

    public function test_create_with_valid_data()
    {
        // Must create a recipient in DB since the service class expects to validate it
        $recipient = Recipient::factory()->createOne();
        $model = Content::factory()->make();
        $model->recipient_id = $recipient->id;

        $this->contentRepository->shouldReceive('hydrate')
            ->once()
            ->withAnyArgs()
            ->andReturn($model);
        $this->contentRepository->shouldReceive('save')
            ->once()
            ->withAnyArgs()
            ->andReturn($model);

        $service = new ContentService($this->contentRepository);
        $service->create($model->toArray());
        // Delete the recipient we created
        $recipient->delete();
    }

}