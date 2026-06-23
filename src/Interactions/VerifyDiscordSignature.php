<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Interactions;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpFoundation\Response;

final readonly class VerifyDiscordSignature
{
    public function __construct(private Repository $repository) {}

    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('X-Signature-Ed25519');
        $timestamp = $request->header('X-Signature-Timestamp');
        $publicKey = $this->repository->get('laracord.public_key');
        $maxAge = (int) $this->repository->get('laracord.signatures.max_age_seconds', 300);

        if (! is_string($signature) || ! is_string($timestamp) || ! is_string($publicKey)
            || ! ctype_xdigit($signature) || ! ctype_xdigit($publicKey)
            || strlen($signature) !== SODIUM_CRYPTO_SIGN_BYTES * 2
            || strlen($publicKey) !== SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES * 2
            || ! ctype_digit($timestamp)
            || abs(Date::now()->getTimestamp() - (int) $timestamp) > $maxAge) {
            return new JsonResponse(['message' => 'Invalid Discord request signature.'], 401);
        }

        $signatureBytes = hex2bin($signature);
        $publicKeyBytes = hex2bin($publicKey);

        if ($signatureBytes === false || $signatureBytes === ''
            || $publicKeyBytes === false || $publicKeyBytes === ''
            || ! sodium_crypto_sign_verify_detached($signatureBytes, $timestamp.$request->getContent(), $publicKeyBytes)) {
            return new JsonResponse(['message' => 'Invalid Discord request signature.'], 401);
        }

        return $next($request);
    }
}
