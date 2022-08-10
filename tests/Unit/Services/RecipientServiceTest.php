<?php

namespace Tests\Unit\Services;

use App\Models\Recipient;
use App\Repositories\RecipientRepository;
use App\Services\RecipientService;
use Mockery\MockInterface;
use Tests\TestCase;

class RecipientServiceTest extends TestCase
{
    public MockInterface|RecipientService $recipientService;
    public MockInterface|RecipientRepository $recipientRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recipientRepository = $this->mock(RecipientRepository::class);
    }

    public function test_create()
    {
        $model = Recipient::factory()->make();
        $validData = $model->toArray();
        $this->recipientRepository->shouldReceive('hydrate')
            ->once()
            ->withArgs([$validData])
            ->andReturn($model);

        $this->recipientRepository->shouldReceive('save')
            ->once()
            ->withArgs([$model])
            ->andReturn($model);
        $this->recipientService = new RecipientService($this->recipientRepository);

        $result = $this->recipientService->create($validData);
        $this->assertSame($model, $result);
    }
}