<?php

declare(strict_types=1);

namespace Tests\Features;

use Mockery;
use App\News;
use App\User;
use App\Wall;
use App\Thread;
use function count;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

final class ImportCommandTest extends TestCase
{
    public function testImport(): void
    {
        Wall::bootSearchable();
        News::bootSearchable();

        factory(User::class, 5)->create();

        // Detects searchable models.
        $userIndexMock = $this->mockIndex(User::class);
        $userIndexMock->expects('clearObjects')->once();
        $userIndexMock->expects('saveObjects')->once()->with(Mockery::on(function ($argument) {
            return count($argument) === 5 && $argument[0]['objectID'] === 'App\User::1';
        }));

        // Detects aggregators.
        $wallIndexMock = $this->mockIndex(Wall::class);
        $wallIndexMock->expects('clearObjects')->once();
        $wallIndexMock->expects('saveObjects')->once()->with(Mockery::on(function ($argument) {
            return count($argument) === 5 && $argument[0]['objectID'] === 'App\User::1';
        }));

        $newsIndexMock = $this->mockIndex(News::class);
        $newsIndexMock->expects('clearObjects')->once();
        $newsIndexMock->expects('saveObjects')->once()->with(Mockery::on(function ($argument) {
            return count($argument) === 5 && $argument[0]['objectID'] === 'App\User::1';
        }));

        // Detects searchable models.
        $threadIndexMock = $this->mockIndex(Thread::class);
        $threadIndexMock->expects('clearObjects')->once();

        Artisan::call('scout:import');
    }
}
