<?php

namespace Tests\Feature\Repositories;

use App\Interfaces\Repositories\ShortLinkRepositoryInterface;
use App\Models\AccessLog;
use App\Models\ShortLink;
use App\Models\User;
use App\Repositories\AccessLogRepository;
use App\Repositories\ShortLinkRepository;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

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

        $this->shortLinkRepository = new ShortLinkRepository(new ShortLink(),
             $this->cacheService,
             $this->accessLogRepository);

        parent::setUp();
    }

   

    /**
     * @test
     */
    public function implements_interface_short_link()
    {
        $this->assertInstanceOf(
            ShortLinkRepositoryInterface::class,
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
            'short_code' => 1,
        ];

        $this->shortLinkRepository->createLink($shortLink);
    }

    /**
     * @test
     */
    public function create_short_link()
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'original_url' => fake()->url,
            'short_code' => 'fake123',
            'access_count' => 0,
            'expiration_date' => '2021-10-10',

        ];

        $response = $this->shortLinkRepository->createLink($data);

        $this->assertArrayHasKey('id', $response);

        $this->assertEquals($response['original_url'], $data['original_url']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
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
            ->shouldReceive('has')
            ->with($cacheKey)
            ->andReturn(true);

        $cacheServiceMock
            ->shouldReceive('get')
            ->with($cacheKey)
            ->andReturn($dbResult);

        $modelShortLinkMock = Mockery::mock(ShortLink::class);
        $modelShortLinkMock
                ->shouldReceive('orderBy')
                ->never();
        $modelShortLinkMock
                ->shouldReceive('get')
                ->never();
    
            $accessLogRepositoryMock = Mockery::mock(AccessLogRepository::class);

            $repository = new ShortLinkRepository($modelShortLinkMock, $cacheServiceMock, $accessLogRepositoryMock);
    
            $result = $repository->getAllLinks();
    
            $this->assertInstanceOf(Collection::class, $result); // Verifica se o resultado é uma instância de Collection
            $this->assertEquals($dbResult->toArray(), $result->toArray());
    }

    

    /**
     * @test
     */
    public function it_throws_404_exception_when_short_link_not_found_by_text()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->expectExceptionMessage('Short Code Not Found');

        $this->shortLinkRepository->searchCode('non_existent_code');
    }

    /**
     * @test
     */
    public function it_returns_short_link_when_found_by_text()
    {
        $shortLink = ShortLink::factory()->create([
            'original_url' => 'https://test.com',
            'short_code' => 'test123'
        ]);
        $shortLink2 = ShortLink::factory()->create([
            'original_url' => 'https://test.com',
            'short_code' => 'test124'
        ]);

        $retrievedLinks = $this->shortLinkRepository->searchCode('test123');

        $this->assertCount(1, $retrievedLinks);

        $retrievedLink = $retrievedLinks->first();

        $this->assertEquals($shortLink->original_url, $retrievedLink->original_url);

        $this->assertEquals($shortLink->identifier, $retrievedLink->identifier);
    }

    /**
     * @test
     */
    public function it_throws_404_exception_when_short_link_not_found_by_id()
    {
        $non_ecziste_link_id  = 999;

        $this->expectException(NotFoundHttpException::class);

        $this->shortLinkRepository->getLinkById($non_ecziste_link_id);
    }
    /**
     * @test
     */
    public function it_returns_short_link_when_found_by_id()
    {
        $link = ShortLink::factory()->create();

        $response = $this->shortLinkRepository->getLinkById($link->id);

        $this->assertIsObject($response);
    }
    /**
     * @test
     */
    public function it_throws_404_exception_when_trying_to_update_by_short_link_id_not_found()
    {
        $nonExistentLinkId = 888;

        $dataToUpdate = [
            'original_url' => 'https://updated-link.com',
        ];

        $this->expectException(NotFoundHttpException::class);

        $this->shortLinkRepository->updateLink($nonExistentLinkId, $dataToUpdate);
    }

    /**
     * @test
     */
    public function it_updates_short_link_when_found_by_id()
    {
        $link = ShortLink::factory()->create();

        $dataToUpdate = [
            'original_url' => 'https://updated-link.com',
        ];

        $response = $this->shortLinkRepository->updateLink($link->id, $dataToUpdate);

        $this->assertNotNull($response);

        $this->assertIsObject($response);

        $this->assertDatabaseHas('short_links', [
            'id' => $link->id,
            'original_url' => $dataToUpdate['original_url'],
        ]);
    }

    /**
     * @test
     */
    public function it_throws_404_exception_when_trying_to_deleted_by_short_link_id_not_found()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->shortLinkRepository->deleteLink(888);
    }

    /**
     * @test
     */
    public function it_deleted_short_link_when_found_by_id()
    {
        $link = ShortLink::factory()->create();

        $deleted = $this->shortLinkRepository->deleteLink($link->id);

        $this->assertTrue($deleted);

        $this->assertDatabaseMissing('short_links', [
            $deleted == $link->id
        ]);
    }
}
