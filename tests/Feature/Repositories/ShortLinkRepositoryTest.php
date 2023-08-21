<?php

namespace Tests\Feature\Repositories;

use App\Interfaces\Services\CacheServiceInterface;
use App\Interfaces\Repositories\ShortLinkInterface;
use App\Models\ShortLink;
use App\Repositories\ShortLinkRepository;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class ShortLinkRepositoryTest extends TestCase
{
    
    protected $shortLinkRepository;

    protected $cacheService;


    protected function setUp(): void
    {
        $this->cacheService = new CacheService();
        $this->shortLinkRepository = new ShortLinkRepository(new ShortLink(), $this->cacheService);

        parent::setUp();
    }

    /**
     * @test
     */
    public function implements_interface_short_link()
    {
        $this->assertInstanceOf(
                ShortLinkInterface::class,
                $this->shortLinkRepository
        );
    }
    
     /**
     * @test
     */
    public function create_short_link_exception()
    {
        $this->expectException(QueryException::class);

        $shortLink = [
            'identifier' => 1,
        ];

        $this->shortLinkRepository->createLink($shortLink); 
    }

    /**
     * @test
     */
    public function create_short_link()
    {
        $shortLink = ShortLink::factory()->raw();

        $response = $this->shortLinkRepository->createLink($shortLink);

        $this->assertArrayHasKey('id', $response);

        $this->assertEquals($response['original_url'], $shortLink['original_url']);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function get_all_links_returns_cached_data()
    {
        $cacheKey = 'short-links:all';
        $dbResult = collect(['db_link_1', 'db_link_2']);

        $cacheServiceMock = Mockery::mock(CacheService::class);
        $cacheServiceMock
            ->shouldReceive('get')
            ->once()
            ->with($cacheKey)
            ->andReturn(null);

            $modelShortLinkMock = Mockery::mock(ShortLink::class);
            $modelShortLinkMock
                ->shouldReceive('orderBy')
                ->once()
                ->with('created_at', 'desc')
                ->andReturnSelf();
            $modelShortLinkMock
                ->shouldReceive('get')
                ->once()
                ->andReturn($dbResult);

                $cacheServiceMock
                ->shouldReceive('put')
                ->once()
                ->with($cacheKey, $dbResult, Mockery::type(Carbon::class))
                ->andReturnNull();
    
            $repository = new ShortLinkRepository($modelShortLinkMock, $cacheServiceMock,);
    
            $result = $repository->getAllLinks();
    
            $this->assertEquals($dbResult, $result);
    }
    

}
