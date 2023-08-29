<?php

namespace Tests\Unit\Repositories;

use App\Models\AccessLog;
use App\Models\ShortLink;
use App\Repositories\AccessLogRepository;
use App\Repositories\ShortLinkRepository;
use App\Services\CacheService;
use Illuminate\Support\Str;
use Mockery;
use PHPUnit\Framework\TestCase;

class ShortLinkRepositoryTest extends TestCase
{

    protected $shortLinkRepository;
    protected $cacheService;
    protected $accessLogRepository;


    protected function setUp(): void
    {
        $this->cacheService = new CacheService();

        $accessLogModel = new AccessLog();

        $this->accessLogRepository = new AccessLogRepository($accessLogModel);

        $this->shortLinkRepository = new ShortLinkRepository(new ShortLink(), $this->cacheService, $this->accessLogRepository);

        parent::setUp();
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * @test
     */

     public function handle_short_code_generates_random_code()
    {
       
        $mockStr = Mockery::mock(Str::class);
        $mockStr->shouldReceive('random')
            ->andReturn('mocked_random_code');

        app()->instance(Str::class, $mockStr);

        $randomCode = $this->shortLinkRepository->handleShortCode(null);

        $this->assertIsString($randomCode);

        $this->assertGreaterThanOrEqual(6, strlen($randomCode));

        $this->assertLessThanOrEqual(8, strlen($randomCode));
    }

    /**
     * @test
     */
    public function handle_short_code_returns_provided_code()
    {
        $providedCode = 'abc123';

        $result = $this->shortLinkRepository->handleShortCode($providedCode);

        $this->assertSame($providedCode, $result);
    }
}
