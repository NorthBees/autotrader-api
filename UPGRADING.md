# Upgrading

## 1.x to 2.0.0

2.0.0 makes `getVehicle()` return the Vehicles API response exactly as Autotrader sends it.
Nothing else in the package changed shape — Stock, Search, Valuations, Taxonomy, Deals,
Finance and the rest were always pass-through and are untouched.

The 1.x line remains supported on the `1.x` branch. If you are not ready to migrate your
readers, stay on `^1.2.0` — it accepts both the old and new API payloads and keeps working
after 28 October 2026.

### 1. `getVehicle()` no longer flattens the response

1.x lifted `results[0]` onto the response root so that callers written against the
pre-August-2026 API kept working. 2.0.0 does not.

```php
// 1.x
$response = $api->getVehicle($advertiserId, $vrm, $mileage, ['history' => 'true']);
$make = $response['vehicle']['make'];
$previousOwners = $response['history']['previousOwners'];

// 2.0.0
$response = $api->getVehicle($advertiserId, $vrm, $mileage, ['history' => 'true']);
$record = $response['results'][0];
$make = $record['vehicle']['make'];
$previousOwners = $record['history']['previousOwners'];
```

Every per-record block moves with it — `vehicle`, `advertiser`, `metadata`, `features`,
`motTests`, `chargeTimes`, `valuations`, `check`, `history`, `competitors`,
`vehicleMetrics` and `factoryCodes` all now live under `results.0`.

If you read a lot of these, the smallest possible change is to take the record once at the
top of each call site and leave the rest of your code alone:

```php
$record = $api->getVehicle($advertiserId, $vrm, $mileage, $options)['results'][0] ?? [];
```

### 2. Detecting "no vehicle found"

1.x produced a response with no `vehicle` key. 2.0.0 gives you the API's own answer.

```php
// 1.x
if (empty($response['vehicle'] ?? [])) { /* not found */ }

// 2.0.0
if (($response['totalResults'] ?? 0) === 0) { /* not found */ }
// or equivalently
if (($response['results'] ?? []) === []) { /* not found */ }
```

Watch for this one. A `results`-shaped response read with the old `empty($response['vehicle'])`
check reports "not found" for a vehicle that was found perfectly well.

### 3. `serviceWarnings` and `recordWarnings` are gone

1.x added these two keys to tell service level warnings from record level ones. 2.0.0 leaves
warnings where the API puts them:

```php
// 1.x
$service = $response['serviceWarnings'];
$record = $response['recordWarnings'];

// 2.0.0
$service = $response['warnings'] ?? [];              // service level
$record = $response['results'][0]['warnings'] ?? []; // record level
```

Until 28 October 2026 the API duplicates record level warnings at the root, so the root
array may contain both. After that date the root holds service level warnings only. 1.x
de-duplicated this for you; 2.0.0 does not, so de-duplicate yourself if you merge the two.

`VehicleResponseNormaliser` has been removed.

### 4. The odometer guard now fires correctly

`AutotraderMissingOdometerException` guards options Autotrader can only derive from mileage.
In 1.x that check was wrong in both directions:

| Call | 1.x | 2.0.0 |
| --- | --- | --- |
| `getVehicle($id, $vrm)` — no options, no odometer | **threw** | returns normally |
| `getVehicle($id, $vrm, null, ['valuations' => 'false'])` | **threw** | returns normally |
| `getVehicle($id, $vrm, null, ['vehicleMetrics' => 'true'])` | returned normally | **throws** |
| `getVehicle($id, $vrm, null, ['valuations' => 'true'])` | threw | throws |

1.x evaluated the option for truthiness, and the values are the *strings* `'true'` and
`'false'` — so `'false'` counted as enabled. It also checked a `metrics` key, while the
option is named `vehicleMetrics`, so metrics never triggered the guard at all.

If you were passing an odometer reading purely to dodge the spurious exception, you can now
drop it. If you request `vehicleMetrics` without one, you will now get the exception the
API's own requirements imply.
