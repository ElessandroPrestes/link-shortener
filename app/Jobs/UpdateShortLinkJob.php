<?php

namespace App\Jobs;

use App\Repositories\ShortLinkRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateShortLinkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;

    protected $data;

    public $tries = 3;

    public $timeout = 60;

    public $retryAfter = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(int $id, array $data)
    {
        $this->id = $id;

        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(ShortLinkRepository $shortLinkRepository):void
    {
        $shortLinkRepository->updateLink($this->id,$this->data);
    }
}
