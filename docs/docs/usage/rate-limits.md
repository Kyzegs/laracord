# Rate Limits

Laracord uses `kyzegs/guzzle-rate-limit-middleware` with Discord's dynamic bucket hashes and major parameters.

- Per-route state persists through Laravel cache.
- Laravel atomic locks serialize competing workers.
- Global capacity defaults to 50 requests per second per credential.
- Unexpected 429 responses honor headers or JSON `retry_after`.
- 401, 403, and non-shared 429 responses feed a configurable invalid-request safety budget.

Use Redis or another shared atomic-lock cache in multi-worker production deployments. All limits and safety margins are configurable under `laracord.rate_limit`.
