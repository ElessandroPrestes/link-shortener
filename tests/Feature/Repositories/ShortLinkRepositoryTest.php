<?php

namespace Tests\Feature\Repositories;

use App\Interfaces\Repositories\ShortLinkInterface;
use App\Models\ShortLink;
use App\Repositories\ShortLinkRepository;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ShortLinkRepositoryTest extends TestCase
{
    use RefreshDatabase;

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
       $shortLink = [
           'original_url' => 'https://www.google.com',
           'identifier' => 'fake123',
       ];

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

    /**
     * @test
     */
    public function it_throws_404_exception_when_short_link_not_found_by_text()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->shortLinkRepository->searchText('non_ecziste_link');

        $this->expectExceptionMessage('Short Link Not Found'); 

        $this->get('v1/links/non_ecziste_link');
        
    }

    /**
     * @test
     */
    public function it_returns_short_link_when_found_by_text()
    {
        $shortLink = ShortLink::factory()->create([
            'original_url' => 'https://test.com',
            'identifier' => 'test123'
        ]);

        $retrievedLink = $this->shortLinkRepository->searchText('test123');

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
    
        $this->shortLinkRepository->getLinkById($non_ecziste_link_id );
    
        
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
