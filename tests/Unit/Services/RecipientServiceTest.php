<?php

namespace Tests\Unit\Services;

use App\Models\Recipient;
use App\Repositories\RecipientRepository;
use App\Services\RecipientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class RecipientServiceTest extends TestCase
{
    use RefreshDatabase;
    public MockInterface|RecipientService $recipientService;
    public MockInterface|RecipientRepository $recipientRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recipientRepository = $this->mock(RecipientRepository::class);
    }

    public function test_getOrCreate()
    {
        $model = Recipient::factory()->make();
        $validData = $model->toArray();
        $this->recipientRepository->shouldReceive('hydrate')
            ->once()
            ->withArgs([$validData])
            ->andReturn($model);

        $this->recipientRepository->shouldReceive('firstOrCreate')
            ->once()
            ->withArgs([$model])
            ->andReturn($model);
        $this->recipientService = new RecipientService($this->recipientRepository);

        $result = $this->recipientService->getOrCreate($validData);
        $this->assertSame($model, $result);
    }
}