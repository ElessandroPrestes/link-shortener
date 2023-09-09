<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\ShortLinkService; 
use App\Interfaces\Repositories\ShortLinkRepositoryInterface;
use App\Interfaces\Services\CacheServiceInterface;
use App\Models\AccessLog;
use App\Models\ShortLink;
use App\Repositories\AccessLogRepository;
use App\Repositories\ShortLinkRepository;
use App\Services\CacheService;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Cache;
use Mockery;

class ShortLinkServiceTest extends TestCase
{
    protected $shortLinkRepository;
    protected $cacheService;
    protected $shortLinkService;

    protected function setUp(): void
    {
        $this->shortLinkRepository = Mockery::mock(ShortLinkRepositoryInterface::class);
        $this->cacheService = Mockery::mock(CacheServiceInterface::class);
        $this->shortLinkService = new ShortLinkService($this->shortLinkRepository, $this->cacheService);
        parent::setUp();

    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    /**
     * @test
     */
    public function index_links_with_cache()
    {
        $cacheKey = 'short-links:all';
        $cachedResult = ['cached data'];

        $this->cacheService
            ->shouldReceive('has')
            ->with($cacheKey)
            ->once()
            ->andReturn(true);

        $this->cacheService
            ->shouldReceive('get')
            ->with($cacheKey)
            ->once()
            ->andReturn($cachedResult);

        $this->shortLinkRepository
            ->shouldReceive('getAllLinks')
            ->never();

        $result = $this->shortLinkService->indexLinks();

        $this->assertEquals($cachedResult, $result);
      
    }
    
}
