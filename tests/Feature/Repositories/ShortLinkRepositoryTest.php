<?php

namespace Tests\Feature\Repositories;

use App\Interfaces\ShortLinkInterface;
use App\Models\ShortLink;
use App\Repositories\ShortLinkRepository;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShortLinkRepositoryTest extends TestCase
{
    protected $shortLinkRepository;

    protected function setUp(): void
    {
        $this->shortLinkRepository = new ShortLinkRepository(new ShortLink());

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
}
