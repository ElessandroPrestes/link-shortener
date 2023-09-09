<?php

namespace Tests\Feature\Services;

use App\Models\AccessLog;
use App\Models\ShortLink;
use App\Models\User;
use App\Repositories\AccessLogRepository;
use App\Repositories\ShortLinkRepository;
use App\Services\CacheService;
use App\Services\RedirectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Mockery;
use Mockery\LegacyMockInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class RedirectionServiceTest extends TestCase
{
    protected $shortLinkRepository;
    protected $cacheService;
    protected $accessLogRepository;
    protected $redirectionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheService = new CacheService();
        $accessLogModel = new AccessLog();
        $this->accessLogRepository = new AccessLogRepository($accessLogModel);
        $this->shortLinkRepository = new ShortLinkRepository(new ShortLink(), $this->accessLogRepository);
        $this->redirectionService = new RedirectionService($this->shortLinkRepository);
    }

    /**
     * @test
     */
    public function it_should_redirect_to_the_original_url()
    {

        $user = User::factory()->create();

        $shortLink = ShortLink::factory()->create([
            'user_id' => $user->id,
            'original_url' => 'https://example.com',
            'short_code' => 'testslug'
        ]);

        $mockRequest = Mockery::mock(Request::class);

        $shortLinkRepository = Mockery::mock(ShortLinkRepository::class);

        $shortLinkRepository->shouldReceive('searchCode')
            ->with('testslug')
            ->andReturn(collect([$shortLink]));

        $shortLinkRepository->shouldReceive('incrementAccessCount')
            ->with($shortLink->id);

        $redirectionService = new RedirectionService($shortLinkRepository);

        $result = $redirectionService->redirectToOriginalUrl('testslug', $mockRequest);

        $this->assertEquals('https://example.com', $result);
        
    }

        
    /**
     * @test
     */
    public function redirec_to_original_url_with_ith_invalide_slug()
    {
       $mockShortLinkRepository = Mockery::mock(ShortLinkRepository::class);

       $mockShortLinkRepository->shouldReceive('searchCode')->andReturn(new Collection());

       $mockRequest = Mockery::mock(Request::class);

       $redirectionService = new RedirectionService($mockShortLinkRepository);

       $this->expectException(NotFoundHttpException::class);

       $redirectionService->redirectToOriginalUrl('invalidslug', $mockRequest);
    }
}
