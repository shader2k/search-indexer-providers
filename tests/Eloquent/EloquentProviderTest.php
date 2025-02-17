<?php

namespace Shader2k\SearchIndexerProviders\Tests\Eloquent;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery as m;
use Shader2k\SearchIndexer\Contracts\Indexable\IndexableContract;
use Shader2k\SearchIndexer\Contracts\Indexable\IndexableEntityContract;
use Shader2k\SearchIndexerProviders\Eloquent\EloquentProvider;
use Shader2k\SearchIndexer\Tests\Data\MockObjects;
use Shader2k\SearchIndexer\Tests\TestCase;

class EloquentProviderTest extends TestCase
{

    protected function tearDown(): void
    {
        parent::tearDown();
        m::close();
    }

    /**
     * Тест получения чанка
     * @throws Exception
     */
    public function testGetChunk(): void
    {
        $rawUserData = MockObjects::getRawUserData(1);
        $usersArray = [
            MockObjects::getUserObject($rawUserData[0]),
        ];

        $provider = new EloquentProvider();
        $mockUser = m::mock('alias:' . 'User', IndexableContract::class);
        $mockPaginator = m::mock(LengthAwarePaginator::class);
        $mockPaginator->shouldReceive('currentPage')
            ->andReturn(1, 2);
        $mockPaginator->shouldReceive('lastPage')
            ->andReturn(1);
        $mockPaginator->shouldReceive('all')
            ->andReturn($usersArray, []);
        $mockBuilder = m::mock(Builder::class);
        $mockBuilder->shouldReceive('select')
            ->andReturn($mockBuilder);
        $mockBuilder->shouldReceive('paginate')
            ->andReturn($mockPaginator);

        $mockUser->shouldReceive('query')
            ->andReturn($mockBuilder);
        $mockUser->shouldReceive('getProviderName')
            ->andReturn('ProviderName');
        $mockUser->shouldReceive('getSearchDriverName')
            ->andReturn('DriverName');
        $mockUser->shouldReceive('getIdentifierField')
            ->andReturn('id');
        $mockUser->shouldReceive('getIndexableFields')
            ->andReturn(['name', 'email']);
        $mockUser->shouldReceive('getIdentifierValue')
            ->andReturn(1);
        $mockUser->shouldReceive('getIdentifierValue')
            ->andReturn(1);
        $collectionItems = $provider->getChunk('User', 1);
        $this->assertNotEmpty($collectionItems);
        $this->assertCount(1, $collectionItems);
        $this->assertContainsOnlyInstancesOf(IndexableEntityContract::class, $collectionItems);
        $collectionItems = $provider->getChunk('User', 1);
        $this->assertContainsOnlyInstancesOf(IndexableEntityContract::class, $collectionItems);
        $this->assertTrue($collectionItems->isEmpty());


    }

}
