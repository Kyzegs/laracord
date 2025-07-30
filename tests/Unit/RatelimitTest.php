<?php

use GuzzleHttp\Psr7\Response;
use Kyzegs\Laracord\Ratelimit;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

describe('Ratelimit', function () {
    it('creates a ratelimit with default values', function () {
        $ratelimit = new Ratelimit;

        expect($ratelimit->getLimit())->toBe(1);
        expect($ratelimit->getRemaining())->toBe(1);
        expect($ratelimit->getResetAfter())->toBe(0.0);
        expect($ratelimit->getReset())->toBe(0.0);
        expect($ratelimit->isDirty())->toBeFalse();
    });

    it('resets ratelimit to default values', function () {
        $ratelimit = new Ratelimit;
        $ratelimit->retry(5.0);

        expect($ratelimit->getRemaining())->toBe(0);
        expect($ratelimit->getResetAfter())->toBeGreaterThanOrEqual(4.9);
        expect($ratelimit->getResetAfter())->toBeLessThanOrEqual(5.1);
        expect($ratelimit->isDirty())->toBeTrue();

        $ratelimit->reset();

        expect($ratelimit->getRemaining())->toBe(1);
        // The reset property is not reset, so getResetAfter() still calculates based on it
        expect($ratelimit->getResetAfter())->toBeGreaterThanOrEqual(0.0);
        expect($ratelimit->isDirty())->toBeFalse();
    });

    it('sets retry values correctly', function () {
        $ratelimit = new Ratelimit;
        $retryAfter = 10.5;

        $result = $ratelimit->retry($retryAfter);

        expect($result)->toBe($ratelimit);
        expect($ratelimit->getRemaining())->toBe(0);
        expect($ratelimit->getResetAfter())->toBeGreaterThanOrEqual(10.4);
        expect($ratelimit->getResetAfter())->toBeLessThanOrEqual(10.6);
        expect($ratelimit->getReset())->toBeGreaterThan(microtime(true));
        expect($ratelimit->isDirty())->toBeTrue();
    });

    it('calculates reset after correctly when reset is set', function () {
        $ratelimit = new Ratelimit;
        $resetTime = microtime(true) + 5.0;

        // Use reflection to set private property
        $reflection = new ReflectionClass($ratelimit);
        $resetProperty = $reflection->getProperty('reset');
        $resetProperty->setValue($ratelimit, $resetTime);

        $resetAfter = $ratelimit->getResetAfter();

        expect($resetAfter)->toBeGreaterThan(0.0);
        expect($resetAfter)->toBeLessThanOrEqual(5.0);
    });

    it('calculates reset after correctly when reset is not set', function () {
        $ratelimit = new Ratelimit;
        $resetAfter = 3.5;

        // Use reflection to set private property
        $reflection = new ReflectionClass($ratelimit);
        $resetAfterProperty = $reflection->getProperty('resetAfter');
        $resetAfterProperty->setValue($ratelimit, $resetAfter);

        expect($ratelimit->getResetAfter())->toBe($resetAfter);
    });

    it('returns zero when reset time has passed', function () {
        $ratelimit = new Ratelimit;
        $pastTime = microtime(true) - 5.0;

        // Use reflection to set private property
        $reflection = new ReflectionClass($ratelimit);
        $resetProperty = $reflection->getProperty('reset');
        $resetProperty->setValue($ratelimit, $pastTime);

        expect($ratelimit->getResetAfter())->toBe(0.0);
    });

    it('updates from response headers correctly', function () {
        $ratelimit = new Ratelimit;
        $response = new Response(200, [
            'X-Ratelimit-Limit' => '10',
            'X-Ratelimit-Remaining' => '5',
            'X-Ratelimit-Reset-After' => '2.5',
            'X-Ratelimit-Reset' => '1234567890.123',
        ]);

        $result = $ratelimit->update($response);

        expect($result)->toBe($ratelimit);
        expect($ratelimit->getLimit())->toBe(10);
        expect($ratelimit->getRemaining())->toBe(5);
        // Since reset is set, getResetAfter() calculates based on it, not resetAfter
        expect($ratelimit->getResetAfter())->toBeGreaterThanOrEqual(0.0);
        expect($ratelimit->getReset())->toBe(1234567890.123);
        expect($ratelimit->isDirty())->toBeTrue();
    });

    it('handles missing response headers gracefully', function () {
        $ratelimit = new Ratelimit;
        $response = new Response(200, []);

        $result = $ratelimit->update($response);

        expect($result)->toBe($ratelimit);
        expect($ratelimit->getLimit())->toBe(1);
        expect($ratelimit->getRemaining())->toBe(0);
        expect($ratelimit->getResetAfter())->toBeGreaterThanOrEqual(0.0);
    });

    it('handles missing reset after header', function () {
        $ratelimit = new Ratelimit;
        $response = new Response(200, [
            'X-Ratelimit-Limit' => '5',
            'X-Ratelimit-Remaining' => '3',
            'X-Ratelimit-Reset' => (microtime(true) + 10.0),
        ]);

        $ratelimit->update($response);

        expect($ratelimit->getResetAfter())->toBeGreaterThan(0.0);
        expect($ratelimit->getResetAfter())->toBeLessThanOrEqual(10.1);
    });

    it('handles dirty state when updating', function () {
        $ratelimit = new Ratelimit;
        $ratelimit->retry(5.0); // Make it dirty

        $response = new Response(200, [
            'X-Ratelimit-Limit' => '10',
            'X-Ratelimit-Remaining' => '5',
            'X-Ratelimit-Reset-After' => '2.5',
        ]);

        $ratelimit->update($response);

        expect($ratelimit->getRemaining())->toBe(5); // Should use remaining from response
    });

    it('handles non-dirty state when updating', function () {
        $ratelimit = new Ratelimit;

        $response = new Response(200, [
            'X-Ratelimit-Limit' => '10',
            'X-Ratelimit-Remaining' => '5',
            'X-Ratelimit-Reset-After' => '2.5',
        ]);

        $ratelimit->update($response);

        expect($ratelimit->getRemaining())->toBe(5);
        expect($ratelimit->isDirty())->toBeTrue();
    });

    it('limits remaining to limit when dirty', function () {
        $ratelimit = new Ratelimit;
        $ratelimit->retry(5.0); // Make it dirty

        $response = new Response(200, [
            'X-Ratelimit-Limit' => '5',
            'X-Ratelimit-Remaining' => '10', // More than limit
            'X-Ratelimit-Reset-After' => '2.5',
        ]);

        $ratelimit->update($response);

        expect($ratelimit->getRemaining())->toBe(5); // Should be limited to limit
    });

    it('handles string header values', function () {
        $ratelimit = new Ratelimit;
        $response = new Response(200, [
            'X-Ratelimit-Limit' => ['10'], // Array format
            'X-Ratelimit-Remaining' => ['5'],
            'X-Ratelimit-Reset-After' => ['2.5'],
            'X-Ratelimit-Reset' => ['1234567890.123'],
        ]);

        $ratelimit->update($response);

        expect($ratelimit->getLimit())->toBe(10);
        expect($ratelimit->getRemaining())->toBe(5);
        // Since reset is set, getResetAfter() calculates based on it, not resetAfter
        expect($ratelimit->getResetAfter())->toBeGreaterThanOrEqual(0.0);
        expect($ratelimit->getReset())->toBe(1234567890.123);
    });

    it('handles invalid header values', function () {
        $ratelimit = new Ratelimit;
        $response = new Response(200, [
            'X-Ratelimit-Limit' => 'invalid',
            'X-Ratelimit-Remaining' => 'invalid',
            'X-Ratelimit-Reset-After' => 'invalid',
            'X-Ratelimit-Reset' => 'invalid',
        ]);

        $ratelimit->update($response);

        // When invalid values are passed, they get converted to 0 or default values
        expect($ratelimit->getLimit())->toBe(0);
        expect($ratelimit->getRemaining())->toBe(0);
        expect($ratelimit->getResetAfter())->toBeGreaterThanOrEqual(0.0);
        expect($ratelimit->getReset())->toBe(0.0);
    });
});
