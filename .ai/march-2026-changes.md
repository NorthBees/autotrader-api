# Release 1.1.0 - March 2026 API Changes

## Instructions for AI Agents

This document describes the changes required for release 1.1.0 of the `northbees/autotrader-api` Laravel SDK. It is intended as a reference for AI agents implementing these changes.

### Architecture Context

- This is a **Laravel SDK** (`northbees/autotrader-api`) wrapping the Autotrader UK API
- The SDK uses **traits** on a single `AutotraderApi` class -- each trait handles a different API domain
- **There are no model/DTO classes** -- all API responses are returned as raw `array` from `$response->json()`
- New response fields require **no code changes** -- they appear automatically in the returned arrays
- Request parameters are passed as raw arrays -- no typed request objects exist
- Version is managed via git tags and `CHANGELOG.md` (no version file)

### Codebase Conventions

- PHP 8.4+, strict types, Laravel 12
- Code style: Laravel Pint (`composer lint`)
- Static analysis: PHPStan with Larastan (`composer analyse`)
- Tests: Pest PHP with `Http::fake()` for integration tests (`composer test`)
- Test files follow pattern: `tests/Feature/AutotraderWebService{Domain}Test.php`
- Version-specific tests go in `tests/Feature/AutotraderWebServiceV{version}Test.php` with `->group('v{version}')`
- PHPDoc is used extensively to document API response fields, deprecations, and removals
- Endpoints are defined in `src/Enum/AutotraderEndpoints.php`

---

## Change 1: `amountNoVatGBP` in Trended & Future Valuations

**Type:** Response-only (documentation update)

**Background:** `amountNoVatGBP` valuation fields for retail, trade, and partExchange markets were introduced in Aug 2025 for Vehicles, Stock, Valuations, and Historic Valuations. This release extends them to Trended Valuations and Future Valuations. Only available for LCVs (Light Commercial Vehicles), produced alongside `amountExVatGBP`.

**Files to modify:**
- `src/Traits/AutotraderFutureValuationsTrait.php` -- Add PHPDoc to `getFutureValuation()` noting:
  - Response includes `amountNoVatGBP` for retail, trade, and partExchange valuations (Mar 2026)
  - Only available for LCVs, produced alongside `amountExVatGBP`
- `CHANGELOG.md` -- Add under "Response-Only Changes" in Version 1.1.0
- `README.md` -- Update "Future and Historic Valuation Requests" section with response notes

**No code changes required** -- response fields are automatically available in raw array responses.

---

## Change 2: `wheelbaseTypes` in buyingSignals Preferences (Deals API & Notifications)

**Type:** Response-only (documentation update)

**Background:** `buyingSignals` was introduced in Nov 2025. The `preferences` object within buyingSignals now includes `wheelbaseTypes`. Only available for Van consumer activity.

**Files to modify:**
- `src/Traits/AutotraderDealsTrait.php` -- Update PHPDoc on `getDeals()` and `getDeal()`:
  - Add `wheelbaseTypes` to the list of buyingSignals preferences
  - Note it is only available for Van consumer activity
- `CHANGELOG.md` -- Add under "Response-Only Changes" in Version 1.1.0
- `README.md` -- Update Deals API response notes

**No code changes required.**

---

## Change 3: Removal of Deprecated Finance API Fields

**Type:** Documentation update (deprecated -> removed)

**Background:** The following fields were deprecated in v1.0 (Feb 2026) and have now been **removed**:
- `financeTerms.product` -- replaced by `financeTerms.productType`
- `affordability.replacingExistingLoan` -- replaced by `applicant.replacingExistingLoan`
- `affordability.affordableLoan` -- removed (note: this was listed as `affordability.affordabeLoan` in the release notes, likely a typo for `affordableLoan`)

**Files to modify:**
- `src/Traits/AutotraderFinanceTrait.php` -- Update PHPDoc:
  - Change `@deprecated` to **removed** for the three fields listed above
  - Remove "Both old and new fields are supported during the transition period" language
- `CHANGELOG.md` -- Add under "Changed" in Version 1.1.0 documenting the removals
- `README.md` -- Update Finance API section to reflect fields are now removed, not deprecated

**No SDK code changes required** since the SDK passes raw arrays.

---

## Change 4: Anonymised Finance Application Error Handling

**Type:** SDK code change + new method

**Background:** Previously, requesting an anonymised finance application returned HTTP 451 (which the SDK threw as `AutotraderException`). Now the API returns HTTP 200 with a payload of just `{applicationId, status: "Expired"}`.

**Impact:** Consumers who were catching `AutotraderException` for 451 responses will no longer see exceptions. Instead they receive a normal array response with status "Expired".

**Files to create/modify:**

### New method: `getFinanceApplication()`
Add to `src/Traits/AutotraderFinanceTrait.php`:

```php
/**
 * Get a finance application by ID
 *
 * Note: As of Mar 2026, anonymised finance applications return HTTP 200
 * with a payload of {applicationId, status: "Expired"} instead of the
 * previous HTTP 451 error. Check the 'status' field in the response
 * to determine if an application has been anonymised.
 *
 * @param  int  $advertiserId  The advertiser ID
 * @param  string  $applicationId  The finance application ID
 * @return array
 */
public function getFinanceApplication(int $advertiserId, string $applicationId)
{
    return $this->performRequest(
        HttpMethods::GET,
        AutotraderEndpoints::Finance->value.'/'.$applicationId,
        [],
        ['advertiserId' => $advertiserId]
    );
}
```

### Tests
Add to `tests/Feature/AutotraderWebServiceV110Test.php`:

1. **Test `getFinanceApplication()` returns normal application** -- fake HTTP 200 with full application data, verify response has expected keys
2. **Test `getFinanceApplication()` returns anonymised (Expired) application** -- fake HTTP 200 with `{applicationId: '...', status: 'Expired'}`, verify response has `applicationId` and `status` of `Expired`
3. **Test method existence** -- verify `getFinanceApplication` method exists on `AutotraderApi`

### Documentation
- `CHANGELOG.md` -- Add under "Added" (new method) and "Changed" (error handling behaviour)
- `README.md` -- Add usage example in Finance section, document the Expired status handling

---

## Files Summary

| File | Changes |
|---|---|
| `src/Traits/AutotraderFutureValuationsTrait.php` | Add PHPDoc for `amountNoVatGBP` response fields |
| `src/Traits/AutotraderDealsTrait.php` | Update PHPDoc for `wheelbaseTypes` in buyingSignals |
| `src/Traits/AutotraderFinanceTrait.php` | Add `getFinanceApplication()` method, update PHPDoc for removed fields |
| `tests/Feature/AutotraderWebServiceV110Test.php` | New test file for v1.1.0 features |
| `CHANGELOG.md` | New Version 1.1.0 section |
| `README.md` | Update Finance, Deals, and Valuations sections |

## Validation

After all changes, run:
```bash
composer lint    # Laravel Pint code style
composer analyse # PHPStan static analysis
composer test    # Pest test suite
```

All three must pass before the release is complete.
