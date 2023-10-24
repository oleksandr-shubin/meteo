<?php

namespace Tests\Feature\Shared;

use App\Domain\Shared\Rules\ValidCityRule;
use App\Domain\Weatherapi\Support\WeatherapiClient;
use Illuminate\Support\Facades\Validator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class ValidCityRuleTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_pass_valid_city_name(): void
    {
        $inputCity = 'London';

        $this->instance(
            WeatherapiClient::class,
            Mockery::mock(WeatherapiClient::class, function (MockInterface $mock) use ($inputCity) {
                $mock
                    ->shouldReceive('getTimeZoneByCity')
                    ->andReturn([
                        'location' => [
                            'name' => $inputCity,
                        ]
                    ]);
            })
        );

        $validator = Validator::make(
            ['city' => $inputCity],
            ['city' => new ValidCityRule()]
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * @test
     */
    public function it_can_reject_invalid_city_name(): void
    {
        $inputCity = 'test';

        $this->instance(
            WeatherapiClient::class,
            Mockery::mock(WeatherapiClient::class, function (MockInterface $mock) use ($inputCity) {
                $mock
                    ->shouldReceive('getTimeZoneByCity')
                    ->andReturn([
                        'location' => [
                            'name' => 'Testerazo',
                        ]
                    ]);
            })
        );

        $validator = Validator::make(
            ['city' => $inputCity],
            ['city' => new ValidCityRule()]
        );

        $this->assertFalse($validator->passes());
    }
}
