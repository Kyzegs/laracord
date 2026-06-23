<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Kyzegs\Laracord\Interactions\VerifyDiscordSignature;
use Kyzegs\Laracord\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

uses(TestCase::class);

it('accepts valid signatures and rejects tampering', function (): void {
    $keypair = sodium_crypto_sign_keypair();
    $secret = sodium_crypto_sign_secretkey($keypair);
    $public = sodium_crypto_sign_publickey($keypair);
    config(['laracord.public_key' => bin2hex($public)]);
    $timestamp = (string) Date::now()->getTimestamp();
    $body = '{"type":1}';
    $signature = bin2hex(sodium_crypto_sign_detached($timestamp.$body, $secret));
    $verifyDiscordSignature = resolve(VerifyDiscordSignature::class);

    $request = Request::create('/discord', 'POST', [], [], [], [
        'HTTP_X_SIGNATURE_ED25519' => $signature,
        'HTTP_X_SIGNATURE_TIMESTAMP' => $timestamp,
    ], $body);
    $response = $verifyDiscordSignature->handle($request, fn (): Response => new Response('ok'));
    expect($response->getStatusCode())->toBe(200);

    $tampered = Request::create('/discord', 'POST', [], [], [], [
        'HTTP_X_SIGNATURE_ED25519' => $signature,
        'HTTP_X_SIGNATURE_TIMESTAMP' => $timestamp,
    ], '{"type":2}');
    expect($verifyDiscordSignature->handle($tampered, fn (): Response => new Response('ok'))->getStatusCode())->toBe(401);
});
